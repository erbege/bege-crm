<?php

namespace App\Livewire\Coverage;

use App\Models\CoveragePoint;
use Livewire\Component;

class CoverageMap extends Component
{
    public $typeFilter = '';
    public $points = [];

    protected $queryString = ['typeFilter'];

    public function mount()
    {
        $this->loadPoints();
    }

    public function updatedTypeFilter()
    {
        $this->loadPoints();
    }

    public function loadPoints()
    {
        $query = CoveragePoint::query()
            ->with('area.parent.parent.parent')
            ->active();

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        $this->points = $query->get()->map(function ($point) {
            return [
                'id' => $point->id,
                'name' => $point->name,
                'code' => $point->code,
                'type' => $point->type,
                'typeLabel' => $point->type_label,
                'latitude' => (float) $point->latitude,
                'longitude' => (float) $point->longitude,
                'area' => $point->area->name ?? '',
                'areaPath' => $point->area->full_path ?? '',
                'capacity' => $point->capacity,
                'usedPorts' => $point->used_ports,
                'availablePorts' => $point->available_ports,
                'address' => $point->address,
                'markerColor' => $point->marker_color,
            ];
        })->toArray();

        $this->dispatch('points-updated', points: $this->points);
    }

    public function render()
    {
        return view('livewire.coverage.coverage-map')
            ->layout('layouts.app');
    }
}
