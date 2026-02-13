<?php

namespace App\Livewire\Admin;

use App\Models\Institute;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Manage Institutes')]
class Institutes extends Component
{
    use WithPagination;

    public $filterStatus = '';
    public $filterType = '';

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function approve($id)
    {
        $institute = Institute::findOrFail($id);

        if ($institute->status !== 'pending') {
            session()->flash('error', 'Only pending institutes can be approved.');
            return;
        }

        try {
            DB::beginTransaction();

            $institute->update(['status' => 'approved']);
            $institute->user->update(['status' => 'active']);

            DB::commit();
            session()->flash('message', 'Institute approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Approval failed.');
        }
    }

    public function reject($id)
    {
        $institute = Institute::findOrFail($id);

        if ($institute->status !== 'pending') {
            session()->flash('error', 'Only pending institutes can be rejected.');
            return;
        }

        try {
            DB::beginTransaction();

            $institute->update(['status' => 'rejected']);
            $institute->user->update(['status' => 'blocked']);

            DB::commit();
            session()->flash('message', 'Institute rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Rejection failed.');
        }
    }

    public function render()
    {
        $query = Institute::with('user');

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        $institutes = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin.institutes', [
            'institutes' => $institutes,
        ]);
    }
}
