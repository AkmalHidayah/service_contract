<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
    
        if ($request->user()->usertype === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($request->user()->usertype === 'approval') {
            // Redirect to the approval index route with a query parameter for unit_work
            return redirect()->route('approval.index', ['unit_work' => $request->user()->unit_work]);
        } elseif ($request->user()->usertype === 'pkm') {
            return redirect()->route('pkm.dashboard');
        }
        return redirect()->intended(route('dashboard'));
    }
    
    
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
