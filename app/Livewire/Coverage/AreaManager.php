<?php

namespace App\Livewire\Coverage;

use App\Models\Area;
use Livewire\Component;
use Livewire\WithPagination;

class AreaManager extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';
    public $editMode = false;
    public $areaId = null;

    // Form fields
    public $parent_id = '';
    public $name = '';
    public $code = '';
    public $type = 'district';
    public $latitude = '';
    public $longitude = '';
    public $is_active = true;

    protected $queryString = ['search', 'typeFilter'];

    protected function rules()
    {
        $uniqueRule = $this->areaId
            ? 'unique:areas,code,' . $this->areaId
            : 'unique:areas,code';

        return [
            'parent_id' => 'nullable|exists:areas,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|' . $uniqueRule,
            'type' => 'required|in:province,city,district,village',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->dispatch('open-modal');
    }

    public function editArea($id)
    {
        $area = Area::findOrFail($id);
        $this->areaId = $area->id;
        $this->parent_id = $area->parent_id ?? '';
        $this->name = $area->name;
        $this->code = $area->code;
        $this->type = $area->type;
        $this->latitude = $area->latitude ?? '';
        $this->longitude = $area->longitude ?? '';
        $this->is_active = $area->is_active;
        $this->editMode = true;
        $this->dispatch('open-modal');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'parent_id' => $this->parent_id ?: null,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'latitude' => $this->latitude ?: null,
            'longitude' => $this->longitude ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->editMode) {
            $area = Area::findOrFail($this->areaId);
            $area->update($data);
            $this->dispatch('toast', type: 'success', message: 'Wilayah berhasil diperbarui.');
        } else {
            Area::create($data);
            $this->dispatch('toast', type: 'success', message: 'Wilayah berhasil ditambahkan.');
        }

        $this->dispatch('close-modal');
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->areaId = $id;
        $this->dispatch('open-delete-modal');
    }

    public function deleteArea()
    {
        $area = Area::findOrFail($this->areaId);

        if ($area->children()->count() > 0) {
            $this->dispatch('toast', type: 'error', message: 'Wilayah tidak dapat dihapus karena memiliki sub-wilayah.');
            $this->dispatch('close-modal');
            return;
        }

        if ($area->coveragePoints()->count() > 0) {
            $this->dispatch('toast', type: 'error', message: 'Wilayah tidak dapat dihapus karena memiliki titik coverage.');
            $this->dispatch('close-modal');
            return;
        }

        $area->delete();
        $this->dispatch('toast', type: 'success', message: 'Wilayah berhasil dihapus.');
        $this->dispatch('close-modal');
        $this->areaId = null;
    }

    public function closeModal()
    {
        $this->dispatch('close-modal');
        $this->resetForm();
    }

    public function closeDeleteModal()
    {
        $this->dispatch('close-modal');
        $this->areaId = null;
    }

    private function resetForm()
    {
        $this->reset([
            'areaId',
            'parent_id',
            'name',
            'code',
            'latitude',
            'longitude',
            'editMode'
        ]);
        $this->type = 'district';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function getParentOptionsProperty()
    {
        // Computed property — only re-evaluated when dependencies change (not on every render)
        $parentTypes = match ($this->type) {
            'city' => ['province'],
            'district' => ['city'],
            'village' => ['district'],
            default => [],
        };

        if (empty($parentTypes)) {
            return collect([]);
        }

        return Area::whereIn('type', $parentTypes)
            ->active()
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        $areas = Area::query()
            ->with('parent.parent.parent.parent')
            ->withCount(['children', 'coveragePoints'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->orderBy('type')
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.coverage.area-manager', [
            'areas' => $areas,
            'parentOptions' => $this->parentOptions,
        ])->layout('layouts.app');
    }
}
