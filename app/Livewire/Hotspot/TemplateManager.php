<?php

namespace App\Livewire\Hotspot;

use App\Models\HotspotVoucherTemplate;
use Livewire\Component;
use Livewire\WithPagination;

class TemplateManager extends Component
{
    use WithPagination;

    public $search = '';
    public $editMode = false;
    public $templateId = null;

    // Form fields
    public $name = '';
    public $content = '';
    public $is_active = true;

    protected $queryString = ['search'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'content' => 'required|string',
        'is_active' => 'boolean',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->editMode = false;

        // Default template content for new templates
        $this->content = <<<'blade'
<div class="voucher-container" style="display: flex; flex-wrap: wrap; gap: 10px;">
    @foreach($vouchers as $voucher)
        <div class="voucher" style="border: 1px solid #ccc; padding: 10px; width: 200px;">
            <h3>{{ $voucher->profile->name }}</h3>
            <p>Code: <strong>{{ $voucher->code }}</strong></p>
            <p>Price: {{ number_format($voucher->profile->price, 0, ',', '.') }}</p>
        </div>
    @endforeach
</div>
blade;

        $this->dispatch('open-modal');
    }

    public function editTemplate($id)
    {
        $template = HotspotVoucherTemplate::findOrFail($id);
        $this->templateId = $template->id;
        $this->name = $template->name;
        $this->content = $template->content;
        $this->is_active = $template->is_active;
        $this->editMode = true;
        $this->dispatch('open-modal');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'content' => $this->content,
            'is_active' => $this->is_active,
        ];

        if ($this->editMode) {
            $template = HotspotVoucherTemplate::findOrFail($this->templateId);
            $template->update($data);
            $this->dispatch('toast', type: 'success', message: 'Template successfully updated.');
        } else {
            HotspotVoucherTemplate::create($data);
            $this->dispatch('toast', type: 'success', message: 'Template successfully created.');
        }

        $this->dispatch('close-modal');
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->templateId = $id;
        $this->dispatch('open-delete-modal');
    }

    public function deleteTemplate()
    {
        $template = HotspotVoucherTemplate::findOrFail($this->templateId);
        $template->delete();
        $this->dispatch('toast', type: 'success', message: 'Template successfully deleted.');
        $this->dispatch('close-modal');
        $this->templateId = null;
    }

    public function closeModal()
    {
        $this->dispatch('close-modal');
        $this->resetForm();
    }

    public function closeDeleteModal()
    {
        $this->dispatch('close-modal');
        $this->templateId = null;
    }

    private function resetForm()
    {
        $this->reset(['templateId', 'name', 'content', 'editMode']);
        $this->is_active = true;
        $this->resetErrorBag();
    }

    // Preview Logic
    public $previewHtml = '';
    public $previewMode = 'dummy'; // dummy, real
    public $previewBatchId = null;

    public function previewTemplate($id)
    {
        $this->templateId = $id;
        $this->generatePreview();
        $this->dispatch('open-preview-modal');
    }

    public function updatedPreviewMode()
    {
        $this->generatePreview();
    }

    public function updatedPreviewBatchId()
    {
        $this->generatePreview();
    }

    public function generatePreview()
    {
        $template = HotspotVoucherTemplate::find($this->templateId);

        if (!$template) {
            return;
        }

        $vouchers = collect();

        if ($this->previewMode === 'real' && $this->previewBatchId) {
            $vouchers = \App\Models\HotspotVoucher::with('profile')
                ->where('batch_id', $this->previewBatchId)
                ->limit(5) // Limit to 5 for preview
                ->get();
        } elseif ($this->previewMode === 'dummy') {
            $vouchers = $this->generateDummyVouchers();
        }

        try {
            // Render the blade content
            $this->previewHtml = \Illuminate\Support\Facades\Blade::render($template->content ?? '', ['vouchers' => $vouchers]);
        } catch (\Exception $e) {
            $this->previewHtml = '<div class="text-red-500 p-4">Error rendering template: ' . $e->getMessage() . '</div>';
        }
    }

    private function generateDummyVouchers()
    {
        $vouchers = collect();
        $profile = new \App\Models\HotspotProfile([
            'name' => '1 Jam-3K',
            'price' => 3000,
            'rate_limit' => '1M/1M',
        ]);

        for ($i = 0; $i < 3; $i++) {
            $code = strtoupper(\Illuminate\Support\Str::random(6));
            $pass = strtolower(\Illuminate\Support\Str::random(4));

            $voucher = new \App\Models\HotspotVoucher([
                'code' => $code,
                'password' => $pass,
                'time_limit' => '1h',
                'data_limit' => 1024 * 1024 * 1024, // 1GB
                'user_mode' => 'username_password',
            ]);

            $voucher->setRelation('profile', $profile);
            $vouchers->push($voucher);
        }

        return $vouchers;
    }

    public function closePreviewModal()
    {
        $this->dispatch('close-modal');
        $this->previewHtml = '';
        $this->previewBatchId = null;
        $this->previewMode = 'dummy';
    }

    public function render()
    {
        $templates = HotspotVoucherTemplate::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        // Get recent batches for real data preview
        $batches = \App\Models\HotspotVoucher::selectRaw('batch_id, min(created_at) as created_at')
            ->whereNotNull('batch_id')
            ->groupBy('batch_id')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('livewire.hotspot.template-manager', [
            'templates' => $templates,
            'batches' => $batches,
        ])->layout('layouts.app');
    }
}
