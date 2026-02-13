<?php

namespace App\Livewire\Admin\Organizations;

use App\Models\Institute;
use App\Services\InstituteService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout("layouts.admin")]
#[Title('Organizations')]
class Index extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $status = '';

    #[Url(history: true)]
    public $type = '';

    #[Url(history: true)]
    public $search = '';

    public $perPage = 15;

    // Reset page when any filter changes
    public function updated($property)
    {
        if (in_array($property, ['status', 'type', 'search'])) {
            $this->resetPage();
        }
    }

    // app/Livewire/Admin/Organizations/Index.php

    public function approve(InstituteService $service, $instituteId)
    {
        // Best Practice: Fetch ID manually to avoid model binding issues in actions if the list changes
        $institute = Institute::findOrFail($instituteId);

        if ($institute->status !== Institute::STATUS_PENDING) {
            // You generally shouldn't reach here if the button is hidden correctly
            return;
        }

        try {
            DB::beginTransaction();

            $service->approveInstitute($institute);

            DB::commit();

            // Success: The button will automatically disappear because the status changed!
            $this->dispatch('notify', variant: 'success', message: 'Institute approved successfully.');

        } catch (\Throwable $e) {
            DB::rollBack(); // Fixed missing semicolon
            \Log::error($e->getMessage());
            $this->dispatch('notify', variant: 'danger', message: 'Approval failed.');
        }
    }

    public function reject(InstituteService $service, $instituteId)
    {
        // Best Practice: Fetch ID manually to avoid model binding issues in actions if the list changes
        $institute = Institute::findOrFail($instituteId);

        if ($institute->status !== Institute::STATUS_PENDING) {
            // You generally shouldn't reach here if the button is hidden correctly
            return;
        }

        try {
            DB::beginTransaction();

            $service->rejectInstitute($institute);

            DB::commit();

            // Success: The button will automatically disappear because the status changed!
            $this->dispatch('notify', variant: 'success', message: 'Institute rejected successfully.');

        } catch (\Throwable $e) {
            DB::rollBack(); // Fixed missing semicolon
            \Log::error($e->getMessage());
            $this->dispatch('notify', variant: 'danger', message: 'Rejection failed.');
        }
    }

    public function render(InstituteService $service)
    {

        $institutes = $service->getPaginated([
            'status' => $this->status ?: null,
            'type' => $this->type ?: null,
            'search' => $this->search ?: null,
        ], $this->perPage);
        return view('livewire.admin.organizations.index', [
            'institutes' => $institutes
        ]);

    }
}