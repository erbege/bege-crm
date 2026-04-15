<?php

namespace App\Livewire\MasterData;

use App\Models\ScriptTemplate;
use Livewire\Component;
use Livewire\WithPagination;

class ScriptTemplateManager extends Component
{
    use WithPagination;

    public $search = '';
    public $editMode = false;
    public $templateId = null;

    // Form fields
    public $name = '';
    public $brand = 'zte';
    public $type = 'activation';
    public $content = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'brand' => 'required|string|in:zte,huawei,nokia,fiberhome',
        'type' => 'required|string',
        'content' => 'required|string',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->dispatch('open-modal');
    }

    public function editTemplate($id)
    {
        $template = ScriptTemplate::findOrFail($id);
        $this->templateId = $template->id;
        $this->name = $template->name;
        $this->brand = $template->brand;
        $this->type = $template->type;
        $this->content = $template->content;
        $this->editMode = true;
        $this->dispatch('open-modal');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'brand' => $this->brand,
            'type' => $this->type,
            'content' => $this->content,
        ];

        if ($this->editMode) {
            $template = ScriptTemplate::findOrFail($this->templateId);
            $template->update($data);
            $this->dispatch('alert', type: 'success', message: 'Template script berhasil diperbarui.');
        } else {
            ScriptTemplate::create($data);
            $this->dispatch('alert', type: 'success', message: 'Template script berhasil ditambahkan.');
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
        $template = ScriptTemplate::findOrFail($this->templateId);
        $template->delete();
        $this->dispatch('alert', type: 'success', message: 'Template script berhasil dihapus.');
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
        $this->reset([
            'templateId',
            'name',
            'content',
            'editMode'
        ]);
        $this->brand = 'zte';
        $this->type = 'activation';
        $this->resetErrorBag();
    }

    public function render()
    {
        $templates = ScriptTemplate::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('brand', 'like', '%' . $this->search . '%')
                    ->orWhere('type', 'like', '%' . $this->search . '%');
            })
            ->orderBy('brand')
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.master-data.script-template-manager', [
            'templates' => $templates,
        ])->layout('layouts.app');
    }
}
