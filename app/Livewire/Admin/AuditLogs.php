<?php

namespace App\Livewire\Admin;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AuditLog;

class AuditLogs extends Component
{
    use WithPagination;

    public string $search = '';

    // dropdown
    public bool $showFilters = false;

    // filters
    public string $action = '';
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    // sorting
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';

    // modal
    public bool $showViewModal = false;
    public ?int $viewLogId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'action' => ['except' => ''],
        'dateFrom' => ['except' => null],
        'dateTo' => ['except' => null],
        'sortBy' => ['except' => 'created_at'],
        'sortDir' => ['except' => 'desc'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updated($property): void
    {
        if (in_array($property, ['action', 'dateFrom', 'dateTo', 'sortBy', 'sortDir'])) {
            $this->resetPage();
        }
    }

    public function toggleFilters(): void
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters(): void
    {
        $this->reset([
            'action',
            'dateFrom',
            'dateTo',
            'sortBy',
            'sortDir',
        ]);

        $this->sortBy = 'created_at';
        $this->sortDir = 'desc';

        $this->resetPage();
    }

    public function getHasActiveFiltersProperty(): bool
    {
        return !blank($this->action)
            || !blank($this->dateFrom)
            || !blank($this->dateTo)
            || $this->sortBy !== 'created_at'
            || $this->sortDir !== 'desc';
    }

    public function viewLog(int $id): void
    {
        $this->viewLogId = $id;
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->reset(['showViewModal', 'viewLogId']);
    }


    public function render()
    {
        $logs = AuditLog::query()
            ->with('actor')
            ->when($this->search, function ($q) {
                $q->where(function ($qq) {
                    $qq->where('action', 'like', '%' . $this->search . '%')
                        ->orWhere('target_type', 'like', '%' . $this->search . '%')
                        ->orWhere('target_id', 'like', '%' . $this->search . '%')
                        // ->orWhere('ip', 'like', '%' . $this->search . '%')
                        ->orWhereHas('actor', function ($uq) {
                            $uq->where('fullName', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->action, fn($q) => $q->where('action', $this->action))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', Carbon::parse($this->dateFrom)->startOfDay()))
            ->when($this->dateTo, fn($q) => $q->whereDate('created_at', '<=', Carbon::parse($this->dateTo)->endOfDay()))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(10);

        $actions = AuditLog::query()
            ->select('action')
            ->whereNotNull('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action')
            ->toArray();

        $viewLog = null;

        if ($this->showViewModal && $this->viewLogId) {
            $viewLog = AuditLog::with('actor')->find($this->viewLogId);
        }

        return view('livewire.admin.audit-logs', [
            'logs' => $logs,
            'actions' => $actions,
            'viewLog' => $viewLog,
        ]);
    }
}
