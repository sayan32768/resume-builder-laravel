<?php

namespace App\Livewire\Admin;

use App\Models\Resume;
use App\Models\User;
use App\Models\UserSession;
use App\Services\AuditLogger;
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

        // SECURITY CHECK: resume must belong to current profile user
        if ((string) $resume->userId !== (string) $this->user->id) {

            // log attempt
            AuditLogger::log(
                'ADMIN_RESUME_DELETE_DENIED',
                $resume,
                ['resume_user_id' => $resume->userId],
                null,
                [
                    'viewing_user_id' => (string) $this->user->id,
                    'resume_id' => (string) $resume->id,
                ]
            );

            session()->flash('error', 'Resume does not belong to this user.');
            return;
        }

        // save before snapshot
        $before = [
            'id' => (string) $resume->id,
            'userId' => (string) $resume->userId,
            'resumeType' => $resume->resumeType ?? null,
            'created_at' => optional($resume->created_at)->toDateTimeString(),
        ];

        // log BEFORE deleting
        AuditLogger::log(
            'ADMIN_RESUME_DELETED',
            $resume,
            $before,
            null,
            [
                'profile_user_id' => (string) $this->user->id,
            ]
        );

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
