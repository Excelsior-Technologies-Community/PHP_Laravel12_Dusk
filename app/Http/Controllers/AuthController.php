<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        // Log activity
        $this->logActivity($user->id, 'User Registered', 'New user registration', $request->ip());

        return redirect('/login')->with('success', 'Registration successful! Please verify your email.');
    }

    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if email is verified
            if (is_null($user->email_verified_at)) {
                Auth::logout();
                return back()->with('error', 'Please verify your email address first.');
            }

            // Log activity
            $this->logActivity($user->id, 'User Login', 'User logged in successfully', $request->ip());

            $request->session()->regenerate();

            if ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function dashboard()
    {
        $totalUsers = User::count();
        $latestUsers = User::latest()->take(5)->get();
        $userActivity = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact('totalUsers', 'latestUsers', 'userActivity'));
    }

    public function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'total_logins' => ActivityLog::where('action', 'User Login')->count(),
            'active_users' => User::where('updated_at', '>=', now()->subDays(7))->count(),
        ];

        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->paginate(15);

        return view('admin.dashboard', compact('stats', 'recentActivities'));
    }

    public function profile()
    {
        return view('profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $updateData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->filled('new_password')) {
            $request->validate([
                'old_password' => 'required',
                'new_password' => 'min:6|confirmed',
            ]);

            if (!Hash::check($request->old_password, $user->password)) {
                return back()->with('error', 'Current password is incorrect');
            }

            $updateData['password'] = Hash::make($request->new_password);
        }

        $user->update($updateData);

        // Log activity
        $this->logActivity($user->id, 'Profile Updated', 'User updated profile information', $request->ip());

        return back()->with('success', 'Profile updated successfully');
    }

    public function users(Request $request)
    {
        $search = $request->search;

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
        })->latest()->paginate(10);

        if ($request->ajax()) {
            return view('users.partials.user_table', compact('users'))->render();
        }

        return view('users', compact('users'));
    }

    public function verifyEmail($id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return redirect('/login')->with('error', 'Invalid verification link.');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            
            // Log activity
            $this->logActivity($user->id, 'Email Verified', 'User verified email address', request()->ip());
            
            return redirect('/login')->with('success', 'Email verified successfully! You can now login.');
        }

        return redirect('/login')->with('success', 'Email already verified.');
    }

    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            return back()->with('success', 'Verification link sent!');
        }

        return back()->with('error', 'Email already verified or not found.');
    }

    public function showForgotForm()
    {
        return view('forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['success' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm($token)
    {
        return view('reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
                
                // Log activity
                $this->logActivity($user->id, 'Password Reset', 'User reset password', request()->ip());
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect('/login')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            // Log activity
            $this->logActivity($user->id, 'User Logout', 'User logged out', $request->ip());
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    private function logActivity($userId, $action, $details, $ipAddress)
    {
        ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'details' => $details,
            'ip_address' => $ipAddress,
        ]);
    }
}