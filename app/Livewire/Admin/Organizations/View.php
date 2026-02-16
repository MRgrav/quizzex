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
class View extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $status = '';

    #[Url(history: true)]
    public $type = '';

    #[Url(history: true)]
    public $search = '';

    public $perPage = 15;

    public $institute;

    public function mount(Institute $institute)
    {
        $this->institute = $institute;
    }

    // app/Livewire/Admin/Organizations/Index.php

    public function approve(InstituteService $service)
    {

        if ($this->institute->status !== Institute::STATUS_PENDING) {
            // You generally shouldn't reach here if the button is hidden correctly
            return;
        }

        try {
            DB::beginTransaction();

            $service->approveInstitute($this->institute);

            DB::commit();

            // Success: The button will automatically disappear because the status changed!
            $this->dispatch('notify', variant: 'success', message: 'Institute approved successfully.');

        } catch (\Throwable $e) {
            DB::rollBack(); // Fixed missing semicolon
            \Log::error($e->getMessage());
            $this->dispatch('notify', variant: 'danger', message: 'Approval failed.');
        }
    }

    public function reject(InstituteService $service)
    {

        if ($this->institute->status !== Institute::STATUS_PENDING) {
            // You generally shouldn't reach here if the button is hidden correctly
            return;
        }

        try {
            DB::beginTransaction();

            $service->rejectInstitute($this->institute);

            DB::commit();

            // Success: The button will automatically disappear because the status changed!
            $this->dispatch('notify', variant: 'success', message: 'Institute rejected successfully.');

        } catch (\Throwable $e) {
            DB::rollBack(); // Fixed missing semicolon
            \Log::error($e->getMessage());
            $this->dispatch('notify', variant: 'danger', message: 'Rejection failed.');
        }
    }

    public function back()
    {
        $this->redirect(route('admin.organizations'));
    }

    public function render()
    {
        return view('livewire.admin.organizations.view', [
            'institute' => $this->institute
        ]);

    }
}