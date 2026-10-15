<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()) {
            return redirect()->route($request->user()->dashboardRoute());
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'role' => ['nullable', 'string', Rule::in(User::ROLES)],
            'login_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginId = trim($credentials['login_id']);
        
        $email = $loginId;
        if ($credentials['role'] === User::ROLE_TEACHER) {
            $email = strtolower($loginId) . '@teacher.system';
        } elseif ($credentials['role'] === User::ROLE_STUDENT) {
            $email = strtolower($loginId) . '@student.system';
        } else {
            $email = strtolower($loginId);
        }

        $normalizedCredentials = [
            'email' => $email,
            'password' => $credentials['password'],
        ];

        if (! Auth::attempt($normalizedCredentials)) {
            return back()
                ->withErrors(['login_id' => 'The provided credentials do not match our records.'])
                ->onlyInput('login_id', 'role');
        }

        /** @var User $user */
        $user = $request->user();

        if (! empty($credentials['role']) && $user->role !== $credentials['role']) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['role' => 'The selected role does not match this account.'])
                ->onlyInput('login_id', 'role');
        }

        if (! in_array($user->role, User::ROLES, true)
            || ($user->isTeacher() && ! $user->teacher()->exists())
            || ($user->isStudent() && ! $user->student()->exists())
        ) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['login_id' => 'This account is not properly linked to an active role profile.'])
                ->onlyInput('login_id', 'role');
        }

        $request->session()->regenerate();

        return redirect()->route($user->dashboardRoute());
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been signed out successfully.');
    }
}
