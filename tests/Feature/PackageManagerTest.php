<?php

namespace Tests\Feature;

use App\Livewire\MasterData\PackageManager;
use App\Models\BwProfile;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PackageManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_package_with_new_fields()
    {
        $this->actingAs(User::factory()->create());

        $profile = BwProfile::factory()->create(['name' => '10Mbps', 'rate_limit' => '10M/10M', 'is_active' => true]);

        Livewire::test(PackageManager::class)
            ->set('name', 'Test Package')
            ->set('code', 'TEST-PKG')
            ->set('bw_profile_id', $profile->id)
            ->set('price', 100000)
            ->set('service_type', 'HOTSPOT')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('packages', [
            'name' => 'Test Package',
            'code' => 'TEST-PKG',
            'service_type' => 'HOTSPOT',
        ]);
    }

    public function test_can_update_package_with_new_fields()
    {
        $this->actingAs(User::factory()->create());

        $profile = BwProfile::factory()->create();

        $package = Package::create([
            'name' => 'Old Package',
            'code' => 'OLD-PKG',
            'bw_profile_id' => $profile->id,
            'price' => 50000,
            'service_type' => 'PPP',
            'is_active' => true,
        ]);

        Livewire::test(PackageManager::class)
            ->call('editPackage', $package->id)
            ->set('name', 'New Package')
            ->set('service_type', 'DHCP')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('packages', [
            'id' => $package->id,
            'name' => 'New Package',
            'service_type' => 'DHCP',
        ]);
    }
}
