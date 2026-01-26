<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Resume;

class AdminDashboard extends Component
{
    public function render()
    {
        $totalUsers = User::count();
        $totalResumes = Resume::count();

        // $blockedUsers = User::where('is_blocked', true)->count();
        $adminUsers = User::where('role', 'ADMIN')->count();

        $recentUsers = User::latest()->limit(5)->get();
        $recentResumes = Resume::latest()->with('user')->limit(5)->get();

        return view('livewire.admin.admin-dashboard', [
            'totalUsers' => $totalUsers,
            'totalResumes' => $totalResumes,
            // 'blockedUsers' => $blockedUsers,
            'adminUsers' => $adminUsers,
            'recentUsers' => $recentUsers,
            'recentResumes' => $recentResumes,
        ]);
    }
}
