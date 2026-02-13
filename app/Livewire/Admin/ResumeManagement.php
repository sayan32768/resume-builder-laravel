<?php

namespace App\Livewire\Admin;

use App\Models\Resume;
use App\Services\AuditLogger;
use Livewire\Component;
use Livewire\WithPagination;

class ResumeManagement extends Component
{
    use WithPagination;

    protected $queryString = ['search'];


    // FILTERS SECTION
    public string $search = '';
    public $showFilters = false;

    public $resumeType = '';
    public $createdFrom = null;
    public $createdTo = null;

    public $sortBy = 'created_at';
    public $sortDir = 'desc';

    public function mount()
    {
        $template = request()->query('template');

        if (!blank($template)) {
            $this->resumeType = ucfirst($template);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updated($property)
    {
        if (in_array($property, [
            'resumeType',
            'createdFrom',
            'createdTo',
            'sortBy',
            'sortDir'
        ])) {
            $this->resetPage();
        }
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }


    public function resetFilters()
    {
        $this->resumeType = '';
        $this->createdFrom = null;
        $this->createdTo = null;
        $this->sortBy = 'created_at';
        $this->sortDir = 'desc';

        $this->resetPage();
    }

    public function getHasActiveFiltersProperty()
    {
        return $this->resumeType || $this->createdFrom || $this->createdTo
            || $this->sortBy !== 'created_at' || $this->sortDir !== 'desc';
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
                'page' => 'admin/resumes',
                'user_email' => optional($resume->user)->email,
                'user_name' => optional($resume->user)->fullName,
            ]
        );

        redirect()->route('admin.resumes.show', $resumeId);
    }

    public function deleteResume(string $resumeId): void
    {
        $resume = Resume::query()
            ->with('user')
            ->findOrFail($resumeId);

        // snapshot before deletion
        $before = [
            'id' => (string) $resume->id,
            'userId' => (string) $resume->userId,
            'resumeTitle' => $resume->resumeTitle ?? null,
            'resumeType' => $resume->resumeType ?? null,
            'created_at' => optional($resume->created_at)->toDateTimeString(),
        ];

        AuditLogger::log(
            'ADMIN_RESUME_DELETED',
            $resume,
            $before,
            null,
            [
                'page' => 'admin/resumes',
                'user_email' => optional($resume->user)->email,
                'user_name' => optional($resume->user)->fullName,
            ]
        );

        $resume->delete();

        session()->flash('success', 'Resume deleted successfully.');

        $this->resetPage();
    }


    public function render()
    {
        $resumes = Resume::query()
            ->with('user')
            ->when($this->search, function ($q) {
                $q->where(function ($qq) {
                    $qq->where('resumeTitle', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($uq) {
                            $uq->where('fullName', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->resumeType, fn($q) => $q->where('resumeType', $this->resumeType))
            ->when($this->createdFrom, fn($q) => $q->whereDate('created_at', '>=', $this->createdFrom))
            ->when($this->createdTo, fn($q) => $q->whereDate('created_at', '<=', $this->createdTo))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(10);

        $resumeTypes = Resume::query()
            ->select('resumeType')
            ->whereNotNull('resumeType')
            ->distinct()
            ->pluck('resumeType')
            ->toArray();

        return view('livewire.admin.resume-management', [
            'resumes' => $resumes,
            'resumeTypes' => $resumeTypes,
        ]);
    }
}
