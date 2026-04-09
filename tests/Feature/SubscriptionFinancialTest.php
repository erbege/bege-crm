<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Subscription\SubscriptionManager;

class SubscriptionFinancialTest extends TestCase
{
    // usage of RefreshDatabase might wipe DB, so be careful. 
    // The user has a persistent DB. I should NOT use RefreshDatabase.
    // I will use transaction or just manual cleanup.

    public function test_subscription_creation_generates_invoice()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user found');
        }
        $this->actingAs($user);

        $customer = Customer::first();
        $package = Package::first();

        if (!$customer || !$package) {
            $this->markTestSkipped('No customer or package found');
        }

        // Simulate Livewire component
        // But first let's just test Model logic if possible?
        // No, logic is in SubscriptionManager. So I must test Livewire.

        Livewire::test(SubscriptionManager::class)
            ->set('customer_id', $customer->id)
            ->set('package_id', $package->id)
            ->set('period_start', now()->format('Y-m-d'))
            ->set('period_end', now()->addMonth()->format('Y-m-d'))
            ->set('installation_date', now()->format('Y-m-d'))
            ->set('amount', $package->price)
            ->set('total', $package->price)
            ->set('status', 'unpaid')
            ->call('save')
            ->assertHasNoErrors();

        // Verify Subscription Created
        $subscription = Subscription::latest()->first();
        $this->assertEquals($customer->id, $subscription->customer_id);

        // Verify Invoice Created
        $invoice = Invoice::where('subscription_id', $subscription->id)->latest()->first();
        $this->assertNotNull($invoice);
        $this->assertEquals($package->price, $invoice->subtotal);

        // CLEANUP
        $invoice->delete();
        $subscription->delete();
    }
}
