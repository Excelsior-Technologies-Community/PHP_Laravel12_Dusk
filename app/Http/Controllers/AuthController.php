<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show registration form
    public function showRegisterForm()
    {
        return view('register');
    }

    // Register user
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')->with('success', 'Registration successful! Login now.');
    }

    // Show login form
    public function showLoginForm()
    {
        return view('login');
    }

    // Login user
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended('/dashboard');
        }

        return back()->with('error', 'Invalid credentials');
    }

    // Dashboard with extra data
    public function dashboard()
    {
        $totalUsers = User::count();
        $latestUsers = User::latest()->take(5)->get();

        return view('dashboard', compact('totalUsers', 'latestUsers'));
    }

    // Show profile page
    public function profile()
    {
        return view('profile');
    }

    // Update profile + password
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        // Update basic info
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Change password (optional)
        if ($request->filled('new_password')) {

            if (!Hash::check($request->old_password, $user->password)) {
                return back()->with('error', 'Old password is incorrect');
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
        }

        return back()->with('success', 'Profile updated successfully');
    }

    // Users list with search + pagination
  public function users(Request $request)
{
    $search = $request->search;

    $users = User::when($search, function ($query, $search) {
        return $query->where('name', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
    })->latest()->paginate(5);

    //  AJAX: return ONLY table + pagination (NOT full page)
    if ($request->ajax()) {

        $output = '';

        // Table Start
        $output .= '
        <div class="bg-white shadow rounded">
            <table class="w-full">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-2 text-left">Name</th>
                        <th class="p-2 text-left">Email</th>
                    </tr>
                </thead>
                <tbody>';

        if ($users->count() > 0) {
            foreach ($users as $user) {
                $output .= '
                <tr class="border-t">
                    <td class="p-2">'.$user->name.'</td>
                    <td class="p-2">'.$user->email.'</td>
                </tr>';
            }
        } else {
            $output .= '
            <tr>
                <td colspan="2" class="text-center p-4 text-gray-500">
                    No users found
                </td>
            </tr>';
        }

        $output .= '</tbody></table></div>';

        // Pagination
        $output .= '<div class="mt-4">'.$users->links().'</div>';

        return response($output);
    }

    return view('users', compact('users'));
}
    // Logout
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}