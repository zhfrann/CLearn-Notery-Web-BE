<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->status_akun !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda telah diban. Silakan hubungi admin.',
                'isBanned' => true
            ], 403);
        }

        return $next($request);
    }
}
