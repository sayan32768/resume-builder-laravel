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

    private function calculateCompletion($resume): int
    {
        $score = 0;

        if (
            $resume->personalDetails &&
            collect([
                $resume->personalDetails->fullName,
                $resume->personalDetails->email,
                $resume->personalDetails->phone,
                $resume->personalDetails->about,
            ])->filter()->isNotEmpty()
        ) {
            $score += 20;
        }

        if ($resume->educationDetails && $resume->educationDetails->isNotEmpty()) {
            $score += 15;
        }

        if (
            $resume->skills &&
            collect($resume->skills->toArray())
            ->except(['id', 'resume_id', 'created_at', 'updated_at'])
            ->filter()
            ->isNotEmpty()
        ) {
            $score += 15;
        }

        if ($resume->professionalExperience && $resume->professionalExperience->isNotEmpty()) {
            $score += 25;
        }

        if ($resume->projects && $resume->projects->isNotEmpty()) {
            $score += 15;
        }

        if ($resume->certifications && $resume->certifications->isNotEmpty()) {
            $score += 10;
        }

        return min($score, 100);
    }

    public function viewResume(string $resumeId): void
    {
        $resume = Resume::query()
            ->with('user')
            ->findOrFail($resumeId);

        AuditLogger::log(
            'ADMIN_RESUME_VIEWED',
            $resume,
            null,
            null,
            [
                'page' => 'admin/users',
                'user_email' => optional($resume->user)->email,
                'user_name' => optional($resume->user)->fullName,
            ]
        );

        redirect()->route('admin.resumes.show', $resumeId);
    }

    public function deleteResume(string $resumeId): void
    {
        $resume = Resume::findOrFail($resumeId);

        if ((string) $resume->userId !== (string) $this->user->id) {
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

        $before = [
            'id' => (string) $resume->id,
            'userId' => (string) $resume->userId,
            'resumeType' => $resume->resumeType ?? null,
            'created_at' => optional($resume->created_at)->toDateTimeString(),
        ];

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

        $analyticsResumes = Resume::query()
            ->where('userId', $this->user->id)
            ->with([
                'personalDetails',
                'educationDetails',
                'skills',
                'professionalExperience',
                'projects',
                'certifications',
            ])
            ->get();

        $draftCount = 0;
        $completedCount = 0;
        $scores = [];
        $templateCounts = [];

        $threshold = 60;

        foreach ($analyticsResumes as $r) {
            if ($r->isDraft) $draftCount++;

            $score = $this->calculateCompletion($r);
            $scores[] = $score;

            if ($score >= $threshold) $completedCount++;

            if (!blank($r->resumeType)) {
                $templateCounts[$r->resumeType] = ($templateCounts[$r->resumeType] ?? 0) + 1;
            }
        }

        $total = $analyticsResumes->count();
        $avgCompletion = $total > 0 ? round(array_sum($scores) / $total, 1) : 0;
        $bestCompletion = $total > 0 ? max($scores) : 0;

        arsort($templateCounts);
        $topTemplate = count($templateCounts) ? array_key_first($templateCounts) : null;

        $distribution = [
            '0-20' => 0,
            '21-40' => 0,
            '41-60' => 0,
            '61-80' => 0,
            '81-100' => 0,
        ];

        foreach ($scores as $s) {
            if ($s <= 20) $distribution['0-20']++;
            elseif ($s <= 40) $distribution['21-40']++;
            elseif ($s <= 60) $distribution['41-60']++;
            elseif ($s <= 80) $distribution['61-80']++;
            else $distribution['81-100']++;
        }

        $completionLabels = array_keys($distribution);
        $completionData = array_values($distribution);

        $resumesWithCompletion = $resumes->getCollection()->map(function ($resume) {
            $resume->loadMissing([
                'personalDetails',
                'educationDetails',
                'skills',
                'professionalExperience',
                'projects',
                'certifications',
            ]);

            $resume->completion = $this->calculateCompletion($resume);

            return $resume;
        });

        $resumes->setCollection($resumesWithCompletion);

        return view('livewire.admin.user-details', [
            'resumes' => $resumes,
            'totalResumes' => $totalResumes,
            'sessions' => $sessions,

            'resumeStats' => [
                'total' => $total,
                'drafts' => $draftCount,
                'completed' => $completedCount,
                'avgCompletion' => $avgCompletion,
                'bestCompletion' => $bestCompletion,
                'topTemplate' => $topTemplate,
                'threshold' => $threshold,
            ],

            'completionLabels' => $completionLabels,
            'completionData' => $completionData,
        ]);
    }
}
