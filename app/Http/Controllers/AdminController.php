<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(): Response
    {
        // 1. Total registered users count
        $totalUsers = User::count();

        // 2. Breakdown of users by their last active client (Web vs Mobile)
        $clientUsage = User::select('last_client_used', DB::raw('count(*) as total'))
            ->groupBy('last_client_used')
            ->pluck('total', 'last_client_used');

        // 3. Get the list of all users for the management table
        $users = User::select('id', 'name', 'email', 'role', 'status', 'last_client_used', 'last_login_at', 'created_at')
            ->latest()
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'totalUsers' => $totalUsers,
            'clientUsage' => [
                'web' => $clientUsage['vue-web'] ?? 0,
                'mobile' => $clientUsage['flutter-mobile'] ?? 0,
            ],
            'users' => $users,
        ]);
    }
}
