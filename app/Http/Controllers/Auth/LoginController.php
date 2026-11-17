<?php

/**
 * Handles sign-in and sign-out for all roles.
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Failed sign-in attempts allowed per login ID and IP address before a
     * short lockout, to slow down password guessing.
     */
    private const MAX_FAILED_ATTEMPTS = 5;

    private const LOCKOUT_SECONDS = 60;

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
            'role' => ['required', 'string', Rule::in(User::ROLES)],
            'login_id' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ], [
            'role.required' => 'Please choose who you are signing in as.',
            'login_id.required' => 'This field is required.',
            'password.required' => 'This field is required.',
        ]);

        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_FAILED_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return $this->rejectLogin($request, 'login_id', "Too many sign-in attempts. Please try again in {$seconds} seconds.");
        }

        $attemptCredentials = $this->attemptCredentials($credentials);

        if ($attemptCredentials === null || ! Auth::attempt($attemptCredentials)) {
            RateLimiter::hit($throttleKey, self::LOCKOUT_SECONDS);

            return $this->rejectLogin($request, 'login_id', 'Invalid Credentials.');
        }

        /** @var User $user */
        $user = $request->user();

        if ($user->role !== $credentials['role']) {
            return $this->rejectLogin($request, 'role', 'The selected role does not match this account.');
        }

        if (($user->isTeacher() && ! $user->teacher()->exists()) || ($user->isStudent() && ! $user->student()->exists())) {
            return $this->rejectLogin($request, 'login_id', 'This account is not properly linked to an active role profile.');
        }

        RateLimiter::clear($throttleKey);
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

    /**
     * Build the credentials for Auth::attempt(). Students sign in with their
     * registration number, faculty with their employee code and admins with
     * their email. Returns null when the login ID matches no profile.
     *
     * @param  array{role: string, login_id: string, password: string}  $credentials
     * @return array<string, mixed>|null
     */
    private function attemptCredentials(array $credentials): ?array
    {
        $loginId = trim($credentials['login_id']);

        if ($credentials['role'] === User::ROLE_ADMIN) {
            return ['email' => strtolower($loginId), 'password' => $credentials['password']];
        }

        $userId = $credentials['role'] === User::ROLE_STUDENT
            ? Student::query()->where('student_number', strtoupper($loginId))->value('user_id')
            : Teacher::query()->where('employee_number', strtoupper($loginId))->value('user_id');

        return $userId === null ? null : ['id' => $userId, 'password' => $credentials['password']];
    }

    /**
     * Sign out anyone half-signed-in and send them back to the login form with
     * an error. Redirecting to the login route (not back()) keeps the message,
     * because invalidating the session forgets the previous URL.
     */
    private function rejectLogin(Request $request, string $field, string $message): RedirectResponse
    {
        if (Auth::check()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()
            ->route('login')
            ->withErrors([$field => $message])
            ->onlyInput('login_id', 'role');
    }

    /**
     * The rate-limiter key for this role, login ID and IP address.
     */
    private function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower(
            $request->string('role').'|'.$request->string('login_id')->trim().'|'.$request->ip()
        ));
    }
}
