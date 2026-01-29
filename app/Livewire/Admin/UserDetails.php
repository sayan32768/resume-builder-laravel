<?php

namespace App\Livewire\Admin;

use App\Models\Resume;
use App\Models\User;
use App\Models\UserSession;
use Jenssegers\Agent\Agent;
use Livewire\Component;
use Livewire\WithPagination;

class UserDetails extends Component
{
    use WithPagination;

    public User $user;

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function deleteResume(string $resumeId): void
    {
        $resume = Resume::findOrFail($resumeId);

        dd($resume->userId, $this->user->id);

        if ($resume->userId !== $this->user->id) {
            session()->flash('error', 'Resume does not belong to this user.');
            return;
        }

        $resume->delete();

        session()->flash('success', 'Resume deleted successfully.');

        $this->resetPage();
    }


    public function render()
    {
        $resumes = $this->user->resumes()
            ->latest()
            ->paginate(10);

        $totalResumes = $this->user->resumes()->count();

        $sessions = UserSession::query()
            ->where('user_id', $this->user->id)
            ->orderByDesc('last_seen_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($session) {
                $agent = new Agent();
                $agent->setUserAgent($session->user_agent ?? '');

                $session->browser = $agent->browser();
                $session->platform = $agent->platform();
                $session->device = $agent->device() ?: ($agent->isDesktop() ? 'Desktop' : 'Mobile');

                return $session;
            });

        return view('livewire.admin.user-details', [
            'resumes' => $resumes,
            'totalResumes' => $totalResumes,
            'sessions' => $sessions,
        ]);
    }
}
