<?php

namespace App\Http\Controllers;

use App\Models\Poli;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Beranda Publik KlickCare
     */
    public function landing(): View
    {
        $polies = Poli::where('is_active', true)->get();

        $todayDate = today()->format('Y-m-d');

        // Statistik ringkas operasional hari ini
        $waitingToday = Queue::whereDate('visit_date', $todayDate)->where('status', 'waiting')->count();
        $inProgressToday = Queue::whereDate('visit_date', $todayDate)->where('status', 'in_progress')->count();
        $completedToday = Queue::whereDate('visit_date', $todayDate)->where('status', 'completed')->count();
        $activeDoctorsCount = Poli::where('is_active', true)->count();

        return view('landingPage', compact(
            'polies',
            'waitingToday',
            'inProgressToday',
            'completedToday',
            'activeDoctorsCount'
        ));
    }

    /**
     * Halaman Detail Poliklinik Spesifik
     */
    public function poliDetail(string $slug): View
    {
        $poli = Poli::where('slug', $slug)->firstOrFail();
        $todayDate = today()->format('Y-m-d');

        $activeQueues = $poli->queues()
            ->whereDate('visit_date', $todayDate)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->orderBy('queue_number')
            ->get();

        $currentCalling = $activeQueues->firstWhere('status', 'in_progress');

        return view('poliDetail', compact('poli', 'activeQueues', 'currentCalling'));
    }

    /**
     * Portal Pasien (Mahasiswa & Dosen/Pegawai)
     */
    public function userDashboard(): View
    {
        $user = Auth::user();
        $polies = Poli::where('is_active', true)->get();
        $todayDate = today()->format('Y-m-d');

        // Tiket antrian aktif pasien hari ini (menunggu, sedang diperiksa, atau dilewati)
        $activeQueue = Queue::with('poli')
            ->where('user_id', $user->id)
            ->whereDate('visit_date', $todayDate)
            ->whereIn('status', ['waiting', 'in_progress', 'skipped'])
            ->latest()
            ->first();

        // Hitung jumlah pasien yang sedang menunggu di depan pasien ini
        $waitingAheadCount = 0;
        if ($activeQueue && $activeQueue->status === 'waiting') {
            $waitingAheadCount = Queue::where('poli_id', $activeQueue->poli_id)
                ->whereDate('visit_date', $todayDate)
                ->where('status', 'waiting')
                ->where('queue_number', '<', $activeQueue->queue_number)
                ->count();
        }

        // Riwayat antrian pasien
        $historyQueues = Queue::with('poli')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('layouts.user', compact('user', 'polies', 'activeQueue', 'waitingAheadCount', 'historyQueues'));
    }

    /**
     * Form Pendaftaran Antrian Baru oleh Pasien (Atomic Concurrency Protected)
     */
    public function storeQueue(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'poli_id' => ['required', 'exists:tbl_polies,id'],
            'complaint' => ['required', 'string', 'min:5', 'max:500'],
            'visit_date' => ['nullable', 'date', 'after_or_equal:today'],
        ], [
            'poli_id.required' => 'Silakan pilih Poliklinik tujuan.',
            'poli_id.exists' => 'Poliklinik yang dipilih tidak valid.',
            'complaint.required' => 'Keluhan utama wajib diisi agar dokter dapat melakukan asesmen.',
            'complaint.min' => 'Deskripsi keluhan minimal 5 karakter.',
        ]);

        $visitDate = $validated['visit_date'] ?? today()->format('Y-m-d');
        $poli = Poli::findOrFail($validated['poli_id']);

        // Cek apakah pasien sudah memiliki antrian aktif di poli yang sama pada tanggal tersebut
        $existing = Queue::where('user_id', $user->id)
            ->where('poli_id', $poli->id)
            ->whereDate('visit_date', $visitDate)
            ->whereIn('status', ['waiting', 'in_progress', 'skipped'])
            ->first();

        if ($existing) {
            return back()->withErrors([
                'queue' => "Anda sudah memiliki nomor antrian aktif ({$existing->queue_code}) di {$poli->name} untuk hari tersebut.",
            ]);
        }

        try {
            $queue = DB::transaction(function () use ($user, $poli, $visitDate, $validated) {
                // Kuota aktif dihitung hanya dari antrian yang tidak dibatalkan (dengan lock)
                $activeCount = Queue::where('poli_id', $poli->id)
                    ->whereDate('visit_date', $visitDate)
                    ->where('status', '!=', 'cancelled')
                    ->lockForUpdate()
                    ->count();

                if ($activeCount >= $poli->daily_quota) {
                    throw new \RuntimeException("Mohon maaf, kuota antrian {$poli->name} untuk tanggal ini sudah penuh ({$poli->daily_quota} pasien).");
                }

                // Ambil nomor antrian tertinggi yang pernah diterbitkan pada tanggal tersebut
                $lastQueue = Queue::where('poli_id', $poli->id)
                    ->whereDate('visit_date', $visitDate)
                    ->lockForUpdate()
                    ->orderByDesc('queue_number')
                    ->first();

                $nextNumber = $lastQueue ? ($lastQueue->queue_number + 1) : 1;
                $queueCode = $poli->code_prefix . '-' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

                return Queue::create([
                    'user_id' => $user->id,
                    'poli_id' => $poli->id,
                    'queue_code' => $queueCode,
                    'queue_number' => $nextNumber,
                    'visit_date' => $visitDate,
                    'complaint' => $validated['complaint'],
                    'status' => 'waiting',
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['queue' => $e->getMessage()]);
        } catch (\Throwable $e) {
            return back()->withErrors(['queue' => 'Terjadi kendala saat menerbitkan nomor antrian. Silakan coba kembali.']);
        }

        return redirect()->route('user')->with('success', "Nomor antrian berhasil diterbitkan! Nomor Anda: {$queue->queue_code} di {$poli->name}.");
    }

    /**
     * Batalkan Antrian oleh Pasien
     */
    public function cancelQueue(Queue $queue): RedirectResponse
    {
        $user = Auth::user();

        if ($queue->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        if ($queue->status === 'completed') {
            return back()->withErrors(['queue' => 'Antrian yang sudah selesai tidak dapat dibatalkan.']);
        }

        $queue->update(['status' => 'cancelled']);

        return back()->with('info', "Antrian {$queue->queue_code} telah dibatalkan.");
    }

    /**
     * Konsol Monitoring & Operasional Admin / Petugas Medis
     */
    public function adminDashboard(Request $request): View
    {
        $todayDate = today()->format('Y-m-d');
        $selectedPoliId = $request->query('poli_id');

        $polies = Poli::where('is_active', true)->get();

        // Metrik riil operasional hari ini
        $waitingCount = Queue::whereDate('visit_date', $todayDate)->where('status', 'waiting')->count();
        $inProgressCount = Queue::whereDate('visit_date', $todayDate)->where('status', 'in_progress')->count();
        $completedCount = Queue::whereDate('visit_date', $todayDate)->where('status', 'completed')->count();
        $totalPatientsCount = User::whereIn('role', [User::ROLE_STAFF, User::ROLE_STUDENT])->count();

        // Antrian hari ini dengan filter poli opsional
        $queuesQuery = Queue::with(['user', 'poli'])
            ->whereDate('visit_date', $todayDate);

        if ($selectedPoliId) {
            $queuesQuery->where('poli_id', $selectedPoliId);
        }

        $todayQueues = $queuesQuery->orderByRaw("
            CASE 
                WHEN status = 'in_progress' THEN 1 
                WHEN status = 'waiting' THEN 2 
                WHEN status = 'skipped' THEN 3
                WHEN status = 'completed' THEN 4 
                ELSE 5 
            END
        ")->orderBy('queue_number')->get();

        // Daftar pasien terbaru
        $recentPatients = User::whereIn('role', [User::ROLE_STAFF, User::ROLE_STUDENT])
            ->latest()
            ->take(8)
            ->get();

        return view('layouts.admin', compact(
            'polies',
            'todayQueues',
            'waitingCount',
            'inProgressCount',
            'completedCount',
            'totalPatientsCount',
            'selectedPoliId'
        ));
    }

    /**
     * Update Status Antrian & Catatan Dokter oleh Admin/Petugas Medis
     * Menegakkan aturan: 1 pasien aktif per poli & alur Lewati (Skipped)
     */
    public function updateQueueStatus(Request $request, Queue $queue): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:waiting,in_progress,skipped,completed,cancelled'],
            'doctor_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Aturan Integritas Medis: Hanya boleh ada 1 pasien berstatus in_progress per poli pada tanggal kunjungan
        if ($validated['status'] === 'in_progress') {
            $currentlyActive = Queue::where('poli_id', $queue->poli_id)
                ->whereDate('visit_date', $queue->visit_date)
                ->where('status', 'in_progress')
                ->where('id', '!=', $queue->id)
                ->first();

            if ($currentlyActive) {
                return back()->withErrors([
                    'queue' => "Poli {$queue->poli->name} sedang memeriksa pasien {$currentlyActive->queue_code}. Selesaikan atau lewati pasien tersebut terlebih dahulu sebelum memanggil pasien baru.",
                ]);
            }
        }

        $queue->status = $validated['status'];
        if (isset($validated['doctor_notes'])) {
            $queue->doctor_notes = $validated['doctor_notes'];
        }
        $queue->save();

        $statusMsg = match ($validated['status']) {
            'in_progress' => "Pasien {$queue->user->name} ({$queue->queue_code}) sedang dipanggil ke ruang periksa.",
            'skipped' => "Pasien {$queue->user->name} ({$queue->queue_code}) telah dilewati dan dapat dipanggil ulang nanti.",
            'completed' => "Pemeriksaan pasien {$queue->user->name} ({$queue->queue_code}) telah selesai dicatat.",
            'cancelled' => "Antrian {$queue->queue_code} telah dibatalkan.",
            default => "Status antrian {$queue->queue_code} berhasil diperbarui.",
        };

        return back()->with('success', $statusMsg);
    }
}
