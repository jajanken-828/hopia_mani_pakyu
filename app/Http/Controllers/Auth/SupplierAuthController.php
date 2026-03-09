<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SupplierAuthController extends Controller
{
    /** Show the supplier login form */
    public function showLogin()
    {
        return Inertia::render('Auth/SupplierLogin');
    }

    /** Handle supplier login */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('supplier')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->route('supplier.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /** Show the supplier registration form */
    public function create()
    {
        return Inertia::render('Auth/SupplierRegister');
    }

    /** Handle supplier registration */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'representative_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'email' => ['required', 'email', 'unique:suppliers,email'],
            'phone_number' => ['required', 'string', 'max:50'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $supplier = Supplier::create([
            'business_name' => $validated['business_name'],
            'representative_name' => $validated['representative_name'],
            'address' => $validated['address'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password' => bcrypt($validated['password']),
        ]);

        Auth::guard('supplier')->login($supplier);

        return redirect()->route('supplier.dashboard');
    }

    /**
     * Log out the supplier.
     *
     * IMPORTANT: We must log out the 'supplier' guard specifically.
     * Calling Auth::logout() only clears the default 'web' (employee) guard,
     * which leaves the supplier session alive — causing the Welcome page to
     * still show the "Supplier Hub" button and the sidebar to misbehave.
     */
    public function logout(Request $request)
    {
        Auth::guard('supplier')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
