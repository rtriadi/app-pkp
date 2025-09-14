<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['error' => 'Akun Anda telah dinonaktifkan']);
        }

        // Parse roles (comma-separated)
        $allowedRoles = array_map('trim', explode(',', $roles));

        // Check if user has required role
        if (!in_array($user->role, $allowedRoles)) {
            // Return appropriate response based on request type
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Akses ditolak',
                    'message' => 'Anda tidak memiliki izin untuk mengakses resource ini'
                ], 403);
            }

            // For web requests, redirect with error
            return redirect()->back()->withErrors([
                'error' => 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.'
            ]);
        }

        return $next($request);
    }
}
