<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HotspotProfile;
use App\Models\HotspotTransaction;
use App\Models\Setting;
use App\Services\HotspotVoucherCheckoutService;

class HotspotPortalController extends Controller
{
    /**
     * Tampilan utama portal (Halaman Login / Walled Garden)
     */
    public function index(Request $request)
    {
        // Parameter dari Mikrotik: mac, ip, username, link-login, link-orig, error
        $mikrotikParams = $request->only([
            'mac',
            'ip',
            'username',
            'link-login',
            'link-orig',
            'error'
        ]);

        $packages = HotspotProfile::where('is_active', true)
            ->where('price', '>', 0)
            ->orderBy('price')
            ->get();

        return view('hotspot.portal', compact('mikrotikParams', 'packages'));
    }

    /**
     * Proses pembuatan transaksi via gateway
     */
    public function checkout(Request $request, HotspotVoucherCheckoutService $checkoutService)
    {
        $request->validate([
            'profile_id' => 'required|exists:hotspot_profiles,id',
            'contact' => 'required|string', // WA or Email
            'customer_name' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string', // Gateway method (e.g. BRIVA, QRISC)
        ]);

        $profile = HotspotProfile::findOrFail($request->profile_id);

        if ($profile->price < 10000) {
            return back()->with('error', 'Minimum pembelian untuk pembayaran online adalah Rp 10.000. Silakan pilih paket lain.');
        }

        $activeGateway = $this->getActiveGateway();
        if (!$activeGateway) {
            return back()->with('error', 'Sistem pembayaran sedang tidak tersedia.');
        }

        $transaction = HotspotTransaction::create([
            'reference_number' => HotspotTransaction::generateReferenceNumber(),
            'hotspot_profile_id' => $profile->id,
            'customer_name' => $request->customer_name,
            'customer_contact' => $request->contact,
            'amount' => $profile->price,
            'status' => 'unpaid',
            'payment_method' => $activeGateway,
        ]);

        // Simpan parameter mikrotik ke session untuk digunakan nanti setelah pembayaran lunas
        $request->session()->put("hotspot_mikrotik_{$transaction->reference_number}", $request->only([
            'mac',
            'ip',
            'link-login',
            'link-orig'
        ]));

        try {
            $checkoutData = $checkoutService->processCheckout($transaction, $activeGateway, $request->payment_method);

            // Simpan detail checkout ke table kalau perlu, 
            // Tapi sementara kita redirect ke URL payment gateway (checkout_url)
            if (!empty($checkoutData['checkout_url'])) {
                return redirect()->away($checkoutData['checkout_url']);
            }

            return redirect()->route('hotspot.waiting', ['reference' => $transaction->reference_number]);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Halaman menunggu pembayaran sukses
     */
    public function waiting($reference)
    {
        $transaction = HotspotTransaction::where('reference_number', $reference)->firstOrFail();

        // Cek mikrotik params dari session
        $mikrotikParams = session("hotspot_mikrotik_{$reference}", []);

        // Jika sudah lunas, langsung ke sukses
        if ($transaction->status === 'paid') {
            return redirect()->route('hotspot.success', ['reference' => $reference]);
        }

        return view('hotspot.waiting', compact('transaction', 'mikrotikParams'));
    }

    /**
     * API Status Polling untuk frontend JS Walled Garden (Auto Login trigger)
     */
    public function status($reference)
    {
        $transaction = HotspotTransaction::where('reference_number', $reference)->with('voucher')->firstOrFail();

        if ($transaction->status === 'paid' && $transaction->voucher) {
            return response()->json([
                'status' => 'paid',
                'voucher_code' => $transaction->voucher->code,
                'voucher_password' => $transaction->voucher->password,
            ]);
        }

        return response()->json([
            'status' => $transaction->status
        ]);
    }

    /**
     * Halaman Sukses & Tampilkan PDF Voucher
     */
    public function success(Request $request, $reference)
    {
        $transaction = HotspotTransaction::where('reference_number', $reference)->with(['voucher', 'profile'])->firstOrFail();

        if ($transaction->status !== 'paid') {
            return redirect()->route('hotspot.waiting', ['reference' => $reference]);
        }

        // Params for "Kembali ke Beranda" link if the user has a link-orig
        $mikrotikParams = session("hotspot_mikrotik_{$reference}", []);

        return view('hotspot.success', compact('transaction', 'mikrotikParams'));
    }

    /**
     * Cetak/Download Voucher dari transaksi (PDF)
     */
    public function print($reference)
    {
        $transaction = HotspotTransaction::where('reference_number', $reference)->with(['voucher', 'profile'])->firstOrFail();

        if ($transaction->status !== 'paid' || !$transaction->voucher) {
            abort(404, 'Voucher belum tersedia.');
        }

        // We can reuse the existing Voucher printing logic if appropriate
        // Or create a simplified view for printing just this ticket
        $voucher = $transaction->voucher;
        return view('hotspot.print-single', compact('voucher', 'transaction'));
    }

    private function getActiveGateway()
    {
        if (Setting::get('payment.tripay_enabled'))
            return 'tripay';
        if (Setting::get('payment.ipaymu_enabled'))
            return 'ipaymu';
        if (Setting::get('payment.duitku_enabled'))
            return 'duitku';
        // mayar or others
        return null;
    }
}
