<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;

class CustomerAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('portal.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $customer = Customer::where('phone', $request->phone)->first();

        if ($customer) {
            // Support default password from phone number
            if (is_null($customer->password) && $request->password === $customer->phone) {
                // Update password silently
                $customer->update(['password' => Hash::make($customer->phone)]);
            }

            if (Auth::guard('customer')->attempt(['phone' => $request->phone, 'password' => $request->password], $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended('/portal/dashboard');
            }
        }

        return back()->withErrors([
            'phone' => 'Kredensial tidak valid.',
        ])->onlyInput('phone');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/portal/login');
    }
}
