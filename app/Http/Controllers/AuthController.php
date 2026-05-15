<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('register');
    }

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

    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended('/dashboard');
        }

        return back()->with('error', 'Invalid credentials');
    }

    public function dashboard()
    {
        $totalUsers = User::count();
        $latestUsers = User::latest()->take(5)->get();

        return view('dashboard', compact('totalUsers', 'latestUsers'));
    }

    public function profile()
    {
        return view('profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required',
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
            if (!Hash::check($request->old_password, $user->password)) {
                return back()->with('error', 'Old password is incorrect');
            }

            $updateData['password'] = Hash::make($request->new_password);
        }

        $user->update($updateData);

        return back()->with('success', 'Profile updated successfully');
    }

    public function users(Request $request)
    {
        $search = $request->search;

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
        })->latest()->paginate(5);

        if ($request->ajax()) {
            $output = '';
            $output .= '
            <div class="bg-white shadow rounded">
                <table class="w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="p-2 text-left">Avatar</th>
                            <th class="p-2 text-left">Name</th>
                            <th class="p-2 text-left">Email</th>
                        </tr>
                    </thead>
                    <tbody>';

            if ($users->count() > 0) {
                foreach ($users as $user) {
                    $output .= '
                    <tr class="border-t">
                        <td class="p-2">
                            <img src="'.$user->avatar_url.'" class="w-10 h-10 rounded-full object-cover">
                        </td>
                        <td class="p-2">'.$user->name.'</td>
                        <td class="p-2">'.$user->email.'</td>
                    </tr>';
                }
            } else {
                $output .= '
                <tr>
                    <td colspan="3" class="text-center p-4 text-gray-500">
                        No users found
                    </td>
                </tr>';
            }

            $output .= '</tbody></table></div>';
            $output .= '<div class="mt-4">'.$users->links().'</div>';

            return response($output);
        }

        return view('users', compact('users'));
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}