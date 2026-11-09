<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class checkRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|int  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors(['role' => 'Silakan masuk terlebih dahulu untuk mengakses layanan.']);
        }

        $userRole = (string) Auth::user()->role;
        $allowedRoles = [];

        foreach ($roles as $role) {
            foreach (explode(',', (string) $role) as $item) {
                $trimmed = trim($item);
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        if (in_array($userRole, $allowedRoles, true)) {
            return $next($request);
        }

        // Jika user adalah admin tapi membuka halaman user, arahkan ke admin dashboard
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin')->withErrors(['role' => 'Halaman tersebut ditujukan untuk portal pasien sivitas akademika.']);
        }

        // Jika user adalah pasien tapi mencoba membuka halaman admin
        return redirect()->route('user')->withErrors(['role' => 'Anda tidak memiliki hak akses administratif ke konsol tersebut.']);
    }
}
