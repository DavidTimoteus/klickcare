<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Poli;
use App\Models\PrescriptionItem;
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
     * Halaman Detail Poliklinik Spesifik (Layar Monitor Antrian Publik)
     */
    public function poliDetail(string $slug): View
    {
        $poli = Poli::where('slug', $slug)->firstOrFail();
        $todayDate = today()->format('Y-m-d');

        $activeQueues = $poli->queues()
            ->whereDate('visit_date', $todayDate)
            ->whereIn('status', ['waiting', 'in_progress', 'skipped'])
            ->orderByRaw("
                CASE 
                    WHEN status = 'in_progress' THEN 1 
                    WHEN status = 'waiting' THEN 2 
                    ELSE 3 
                END
            ")
            ->orderBy('queue_number')
            ->get();

        $currentCalling = $activeQueues->firstWhere('status', 'in_progress');

        return view('poliDetail', compact('poli', 'activeQueues', 'currentCalling'));
    }

    /**
     * Endpoint API Live Real-Time Sinkronisasi Layar Antrian & Audio Trigger
     */
    public function livePoliStatus(string $slug): JsonResponse
    {
        $poli = Poli::where('slug', $slug)->firstOrFail();
        $todayDate = today()->format('Y-m-d');

        $activeQueues = $poli->queues()
            ->whereDate('visit_date', $todayDate)
            ->whereIn('status', ['waiting', 'in_progress', 'skipped'])
            ->orderByRaw("
                CASE 
                    WHEN status = 'in_progress' THEN 1 
                    WHEN status = 'waiting' THEN 2 
                    ELSE 3 
                END
            ")
            ->orderBy('queue_number')
            ->get();

        $currentCalling = $activeQueues->firstWhere('status', 'in_progress');

        return response()->json([
            'poli_name' => $poli->name,
            'room' => $poli->room,
            'remaining_quota' => $poli->remainingQuotaToday(),
            'current_calling' => $currentCalling ? [
                'id' => $currentCalling->id,
                'queue_code' => $currentCalling->queue_code,
                'status' => $currentCalling->status,
            ] : null,
            'waiting_count' => $activeQueues->where('status', 'waiting')->count(),
            'skipped_count' => $activeQueues->where('status', 'skipped')->count(),
            'queues' => $activeQueues->map(function ($q) {
                return [
                    'id' => $q->id,
                    'queue_code' => $q->queue_code,
                    'status' => $q->status,
                    'status_label' => $q->status_label,
                    'status_badge' => $q->status_badge_class,
                    'time' => $q->created_at->format('H:i') . ' WIB',
                ];
            }),
            'timestamp' => now()->toIso8601String(),
        ]);
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

        // Riwayat rekam medis terstruktur & resep obat pasien
        $medicalRecords = MedicalRecord::with(['poli', 'prescriptions.medicine'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(5);

        return view('layouts.user', compact(
            'user', 
            'polies', 
            'activeQueue', 
            'waitingAheadCount', 
            'historyQueues', 
            'medicalRecords'
        ));
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

        // Antrian hari ini dengan filter poli opsional & eager loaded RME
        $queuesQuery = Queue::with(['user', 'poli', 'medicalRecord.prescriptions.medicine'])
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

        // Stok obat aktif untuk modal peresepan medis dokter
        $medicines = Medicine::where('is_active', true)->orderBy('name')->get();
        $lowStockCount = Medicine::where('is_active', true)->whereColumn('stock', '<=', 'min_stock')->count();

        return view('layouts.admin', compact(
            'polies',
            'todayQueues',
            'waitingCount',
            'inProgressCount',
            'completedCount',
            'totalPatientsCount',
            'selectedPoliId',
            'medicines',
            'lowStockCount'
        ));
    }

    /**
     * Update Status Antrian, Rekam Medis & Pengurangan Stok Resep Obat (Atomic)
     * Menegakkan aturan: 1 pasien aktif per poli, alur Lewati (Skipped), sanitasi resep aman, dan pengurangan stok otomatis
     */
    public function updateQueueStatus(Request $request, Queue $queue): RedirectResponse
    {
        // Sanitasi input resep obat: Hanya simpan baris yang benar-benar memilih obat, buang baris kosong
        if ($request->has('medicines') && is_array($request->medicines)) {
            $cleanedMedicines = [];
            foreach ($request->medicines as $item) {
                if (is_array($item) && !empty($item['id'])) {
                    $cleanedMedicines[] = [
                        'id' => (int) $item['id'],
                        'quantity' => (!empty($item['quantity']) && (int) $item['quantity'] > 0) ? (int) $item['quantity'] : 1,
                        'dosage' => !empty($item['dosage']) ? trim((string) $item['dosage']) : 'Sesuai anjuran dokter (3x1 sesudah makan)',
                    ];
                }
            }
            $request->merge(['medicines' => $cleanedMedicines]);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:waiting,in_progress,skipped,completed,cancelled'],
            'doctor_notes' => ['nullable', 'string', 'max:1000'],
            'diagnosis' => ['nullable', 'string', 'max:500'],
            'icd10_code' => ['nullable', 'string', 'max:20'],
            'symptoms' => ['nullable', 'string', 'max:500'],
            'allergies_checked' => ['nullable', 'string', 'max:255'],
            'blood_pressure' => ['nullable', 'string', 'max:20'],
            'heart_rate' => ['nullable', 'string', 'max:10'],
            'respiratory_rate' => ['nullable', 'string', 'max:10'],
            'temperature' => ['nullable', 'string', 'max:10'],
            'weight' => ['nullable', 'string', 'max:10'],
            'height' => ['nullable', 'string', 'max:10'],
            'physical_exam' => ['nullable', 'string', 'max:1000'],
            'follow_up_advice' => ['nullable', 'string', 'max:1000'],
            'medicines' => ['nullable', 'array'],
            'medicines.*.id' => ['required', 'exists:tbl_medicines,id'],
            'medicines.*.quantity' => ['required', 'integer', 'min:1'],
            'medicines.*.dosage' => ['required', 'string', 'max:100'],
        ], [
            'status.required' => 'Status pelayanan wajib dipilih.',
            'diagnosis.required' => 'Diagnosis klinis wajib diisi.',
            'medicines.*.id.exists' => 'Obat yang dipilih tidak terdaftar di sistem farmasi.',
            'medicines.*.quantity.min' => 'Jumlah obat pada resep minimal 1.',
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

        try {
            DB::transaction(function () use ($validated, $queue) {
                $queue->status = $validated['status'];

                // Jika pemeriksaan selesai, buat Rekam Medis terstruktur & kurangi stok obat secara atomik
                if ($validated['status'] === 'completed') {
                    $diagnosisText = $validated['diagnosis'] ?? ($validated['doctor_notes'] ?? 'Pemeriksaan Klinis Rutin');
                    $treatmentNotes = $validated['doctor_notes'] ?? null;

                    // Buat kode rekam medis kunjungan unik e.g. RM-202611-0001
                    $recordCount = MedicalRecord::count() + 1;
                    $recordCode = 'RM-' . date('Ym') . '-' . str_pad((string) $recordCount, 4, '0', STR_PAD_LEFT);

                    $medicalRecord = MedicalRecord::create([
                        'record_code' => $recordCode,
                        'queue_id' => $queue->id,
                        'user_id' => $queue->user_id,
                        'poli_id' => $queue->poli_id,
                        'doctor_name' => $queue->poli->doctor_name,
                        'doctor_sip' => 'SIP.440/012/DPJP/' . date('Y'),
                        'diagnosis' => $diagnosisText,
                        'icd10_code' => $validated['icd10_code'] ?? null,
                        'symptoms' => $validated['symptoms'] ?? $queue->complaint,
                        'allergies_checked' => $validated['allergies_checked'] ?? ($queue->user->allergies ?? 'Tidak ada riwayat alergi'),
                        'blood_pressure' => $validated['blood_pressure'] ?? null,
                        'heart_rate' => $validated['heart_rate'] ?? null,
                        'respiratory_rate' => $validated['respiratory_rate'] ?? null,
                        'temperature' => $validated['temperature'] ?? null,
                        'weight' => $validated['weight'] ?? null,
                        'height' => $validated['height'] ?? null,
                        'physical_exam' => $validated['physical_exam'] ?? null,
                        'treatment_notes' => $treatmentNotes,
                        'follow_up_advice' => $validated['follow_up_advice'] ?? null,
                    ]);

                    // Proses pengurangan stok obat dan simpan item resep
                    $prescribedMedicineNames = [];
                    if (!empty($validated['medicines'])) {
                        foreach ($validated['medicines'] as $medItem) {
                            if (empty($medItem['id']) || empty($medItem['quantity'])) continue;

                            $medicine = Medicine::where('id', $medItem['id'])->lockForUpdate()->firstOrFail();

                            if ($medicine->stock < (int) $medItem['quantity']) {
                                throw new \RuntimeException("Stok obat {$medicine->name} tidak mencukupi (tersisa: {$medicine->stock} {$medicine->unit}).");
                            }

                            $medicine->decrement('stock', (int) $medItem['quantity']);

                            PrescriptionItem::create([
                                'medical_record_id' => $medicalRecord->id,
                                'medicine_id' => $medicine->id,
                                'quantity' => (int) $medItem['quantity'],
                                'dosage' => $medItem['dosage'] ?? 'Sesuai petunjuk dokter',
                            ]);

                            $prescribedMedicineNames[] = "{$medicine->name} ({$medItem['quantity']} {$medicine->unit})";
                        }
                    }

                    // Susun ringkasan catatan dokter
                    $summaryNotes = "Diagnosa: {$diagnosisText}";
                    if (!empty($prescribedMedicineNames)) {
                        $summaryNotes .= " | Resep: " . implode(', ', $prescribedMedicineNames);
                    }
                    if ($treatmentNotes && $treatmentNotes !== $diagnosisText) {
                        $summaryNotes .= " | Catatan: {$treatmentNotes}";
                    }
                    $queue->doctor_notes = $summaryNotes;
                } elseif (isset($validated['doctor_notes'])) {
                    $queue->doctor_notes = $validated['doctor_notes'];
                }

                $queue->save();
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['queue' => $e->getMessage()]);
        } catch (\Throwable $e) {
            return back()->withErrors(['queue' => 'Gagal memperbarui status pemeriksaan. ' . $e->getMessage()]);
        }

        $statusMsg = match ($validated['status']) {
            'in_progress' => "Pasien {$queue->user->name} ({$queue->queue_code}) sedang dipanggil ke ruang periksa.",
            'skipped' => "Pasien {$queue->user->name} ({$queue->queue_code}) telah dilewati dan dapat dipanggil ulang nanti.",
            'completed' => "Pemeriksaan pasien {$queue->user->name} ({$queue->queue_code}) telah selesai. Rekam medis & pemotongan stok obat berhasil dicatat.",
            'cancelled' => "Antrian {$queue->queue_code} telah dibatalkan.",
            default => "Status antrian {$queue->queue_code} berhasil diperbarui.",
        };

        return back()->with('success', $statusMsg);
    }

    /**
     * Manajemen Farmasi & Stok Obat Poliklinik (Admin Medis)
     */
    public function pharmacyIndex(Request $request): View
    {
        $search = $request->query('q');
        $selectedCategory = $request->query('category');

        $query = Medicine::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($selectedCategory) {
            $query->where('category', $selectedCategory);
        }

        $medicines = $query->orderBy('name')->paginate(12);

        $categories = Medicine::distinct()->pluck('category');
        $totalMedicinesCount = Medicine::count();
        $lowStockCount = Medicine::where('is_active', true)->whereColumn('stock', '<=', 'min_stock')->count();
        $outOfStockCount = Medicine::where('is_active', true)->where('stock', '<=', 0)->count();

        return view('admin.pharmacy', compact(
            'medicines',
            'categories',
            'totalMedicinesCount',
            'lowStockCount',
            'outOfStockCount',
            'search',
            'selectedCategory'
        ));
    }

    /**
     * Tambah Stok Obat yang Ada
     */
    public function updateMedicineStock(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'medicine_id' => ['required', 'exists:tbl_medicines,id'],
            'additional_stock' => ['required', 'integer', 'min:1', 'max:10000'],
        ], [
            'additional_stock.min' => 'Jumlah penambahan stok minimal 1 unit.',
        ]);

        $medicine = Medicine::findOrFail($validated['medicine_id']);
        $medicine->increment('stock', $validated['additional_stock']);

        return back()->with('success', "Stok obat {$medicine->name} berhasil ditambah sebanyak {$validated['additional_stock']} {$medicine->unit}. Total stok saat ini: {$medicine->stock} {$medicine->unit}.");
    }

    /**
     * Tambah Data Obat Baru ke Katalog Farmasi
     */
    public function storeMedicine(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:tbl_medicines,code'],
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:50'],
            'unit' => ['required', 'string', 'max:30'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'code.unique' => 'Kode obat sudah digunakan dalam katalog.',
            'name.required' => 'Nama obat wajib diisi.',
            'category.required' => 'Kategori obat wajib diisi.',
            'unit.required' => 'Satuan obat wajib diisi.',
        ]);

        $medicine = Medicine::create($validated);

        return back()->with('success', "Obat {$medicine->name} ({$medicine->code}) berhasil didaftarkan ke katalog farmasi.");
    }

    /**
     * Manajemen Perubahan Dokter & Pengaturan Operasional Poliklinik oleh Admin
     */
    public function updatePoli(Request $request, Poli $poli): RedirectResponse
    {
        $validated = $request->validate([
            'doctor_name' => ['required', 'string', 'max:255'],
            'room' => ['required', 'string', 'max:255'],
            'open_time' => ['required', 'string'],
            'close_time' => ['required', 'string'],
            'daily_quota' => ['required', 'integer', 'min:1', 'max:500'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable'],
        ], [
            'doctor_name.required' => 'Nama dokter DPJP wajib diisi.',
            'room.required' => 'Ruangan poliklinik wajib diisi.',
            'daily_quota.required' => 'Kuota harian antrian wajib diisi.',
            'daily_quota.min' => 'Kuota minimal 1 pasien.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $poli->update($validated);

        return back()->with('success', "Data operasional dan tenaga medis {$poli->name} (Dokter: {$poli->doctor_name}) berhasil diperbarui!");
    }
}
