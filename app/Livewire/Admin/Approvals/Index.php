<?php

namespace App\Livewire\Admin\Approvals;

use App\Models\Institute;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Approvals')]
class Index extends Component
{
    use WithPagination;
    public $perPage = 15;
    public $totalPendings = 0;

    #[Computed(persist: true, seconds: 900)]
    public function __construct()
    {
        $this->totalPendings = Institute::where('status', Institute::STATUS_PENDING)->count();
    }

    public function view($id)
    {
        $this->redirect(route('admin.organizations.view', $id));
    }

    #[Computed(persist: true, seconds: 900)]
    public function render()
    {
        $institutes = Institute::where('status', Institute::STATUS_PENDING)
            ->with('user:id,name,email,created_at')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
        return view('livewire.admin.approvals.index', ['institutes' => $institutes, 'totalPendings' => $this->totalPendings]);
    }
}
