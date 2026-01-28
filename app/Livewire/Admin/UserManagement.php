<?php

namespace App\Livewire\Admin;

use App\Models\User;
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


    // public function doSearch(): void
    // {
    //     $this->resetPage();
    // }

    public function updatingSearch()
    {
        $this->resetPage();
    }


    // DOUBLE PROTECTION FOR DELETE ACTION
    public function confirmDelete(string $userId): void
    {
        $user = \App\Models\User::findOrFail($userId);

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
        $user = \App\Models\User::findOrFail($userId);

        if ($user->role === 'ADMIN' || $user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete this user.');
            return;
        }

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
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(5);


        return view('livewire.admin.user-management', [
            'showDeleteModal' => $this->showDeleteModal,
            'deleteUserName' => $this->deleteUserName,
            'users' => $users,
        ]);
    }
}
