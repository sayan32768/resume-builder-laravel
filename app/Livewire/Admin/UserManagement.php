<?php

namespace App\Livewire\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserSession;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public string $search = '';

    // protected $queryString = ['search'];

    public bool $showDeleteModal = false;
    public ?string $deleteUserId = null;
    public ?string $deleteUserName = null;

    // related to filters
    // filter dropdown
    public $showFilters = false;

    // filters
    public $role = '';
    public $status = '';
    public $joinedFrom = null;
    public $joinedTo = null;

    // sorting
    public $sortBy = 'created_at';
    public $sortDir = 'desc';


    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Filter Method
    public function updated($property)
    {
        // whenever any filter changes, reset pagination
        if (in_array($property, [
            'role',
            'status',
            'joinedFrom',
            'joinedTo',
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
        $this->role = '';
        $this->status = '';
        $this->joinedFrom = null;
        $this->joinedTo = null;

        $this->sortBy = 'created_at';
        $this->sortDir = 'desc';

        $this->resetPage();
    }

    public function getHasActiveFiltersProperty()
    {
        return $this->role || $this->status || $this->joinedFrom || $this->joinedTo
            || $this->sortBy !== 'created_at' || $this->sortDir !== 'desc';
    }

    public function closeViewModal(): void
    {
        $this->reset(['showViewModal', 'viewUser']);
    }

    public function viewUser(string $userId): void
    {
        AuditLogger::log(
            'ADMIN_VIEWED_USER',
            User::findOrFail($userId),
            null,
            null,
            [
                'page' => 'admin/users'
            ]
        );

        redirect()->route('admin.users.show', $userId);
    }

    public function toggleAdmin(string $userId)
    {
        $auth = auth()->user();

        // Optional: prevent non-admins from using
        if (!$auth || ($auth->role ?? 'user') !== 'ADMIN') {
            session()->flash('error', 'Unauthorized.');
            return;
        }

        // Prevent changing self role (recommended)
        if ($auth->id === $userId) {
            session()->flash('error', 'You cannot change your own role.');
            return;
        }

        $user = \App\Models\User::findOrFail($userId);

        $before = [
            'role' => $user->role,
        ];

        // Toggle role
        $user->role = ($user->role === 'ADMIN') ? 'USER' : 'ADMIN';
        $user->save();

        $after = [
            'role' => $user->role,
        ];

        AuditLogger::log(
            $user->role === 'ADMIN' ? 'ADMIN_USER_GRANTED_ADMIN' : 'ADMIN_USER_REVOKED_ADMIN',
            $user,
            $before,
            $after
        );

        session()->flash('success', 'User role updated successfully.');
    }



    public function toggleBlock(string $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->id === auth()->id() || $user->role === "ADMIN") {

            AuditLogger::log('ADMIN_ACTION_DENIED', $user, null, null, [
                'reason' => 'Cannot block self/admin',
                'action' => 'toggleBlock',
            ]);

            session()->flash('error', 'Action not allowed.');
            return;
        }

        DB::transaction(function () use ($user) {

            $before = [
                'is_blocked' => (bool) $user->is_blocked,
                'isLoggedIn' => (bool) $user->isLoggedIn,
            ];

            $user->is_blocked = ! $user->is_blocked;

            if ($user->is_blocked) {
                $user->tokens()->delete();
                UserSession::where('user_id', $user->id)->delete();
                $user->isLoggedIn = false;
            }

            $user->save();

            $after = [
                'is_blocked' => (bool) $user->is_blocked,
                'isLoggedIn' => (bool) $user->isLoggedIn,
            ];

            AuditLogger::log(
                $user->is_blocked ? 'ADMIN_USER_BLOCKED' : 'ADMIN_USER_UNBLOCKED',
                $user,
                $before,
                $after,
                [
                    'target_label' => $user->fullName ?? $user->email,
                    'target_email' => $user->email,
                    'tokens_deleted' => $user->is_blocked,
                    'sessions_deleted' => $user->is_blocked,
                    'forced_logout' => $user->is_blocked,
                ]
            );
        });

        session()->flash('success', $user->is_blocked ? 'User blocked and logged out from all devices.' : 'User unblocked.');
    }


    // DOUBLE PROTECTION FOR DELETE ACTION
    public function confirmDelete(string $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->role === 'ADMIN' || $user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete this user.');
            return;
        }

        $this->deleteUserId = $user->id;
        $this->deleteUserName = $user->fullName ?? $user->email;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->reset(['showDeleteModal', 'deleteUserId', 'deleteUserName']);
    }

    public function deleteConfirmed(): void
    {
        if (!$this->deleteUserId) return;
        $this->deleteUser($this->deleteUserId);
        $this->cancelDelete();
    }

    // DOUBLE PROTECTION FOR DELETE ACTION
    public function deleteUser(string $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->role === 'ADMIN' || $user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete this user.');
            return;
        }

        $before = $user->toArray();
        unset($before['password']);

        AuditLogger::log('ADMIN_USER_DELETED', $user, $before, null, [
            'deleted_from' => 'user-management',
            'target_label' => $user->fullName ?? $user->email,
            'target_email' => $user->email,
        ]);

        $user->delete();

        session()->flash('success', 'User deleted successfully.');
        $this->resetPage();
    }


    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($q) {
                $q->where(function ($qq) {
                    $qq->where('fullName', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('id', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->role, fn($q) => $q->where('role', $this->role))
            ->when($this->status, function ($q) {
                if ($this->status === 'active') {
                    $q->where('is_blocked', false);
                }
                if ($this->status === 'blocked') {
                    $q->where('is_blocked', true);
                }
            })
            ->when($this->joinedFrom, function ($q) {
                $q->whereDate('created_at', '>=', Carbon::parse($this->joinedFrom)->startOfDay());
            })
            ->when($this->joinedTo, function ($q) {
                $q->whereDate('created_at', '<=', Carbon::parse($this->joinedTo)->endOfDay());
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(5);


        return view('livewire.admin.user-management', [
            'showDeleteModal' => $this->showDeleteModal,
            'deleteUserName' => $this->deleteUserName,
            'users' => $users,
        ]);
    }
}
