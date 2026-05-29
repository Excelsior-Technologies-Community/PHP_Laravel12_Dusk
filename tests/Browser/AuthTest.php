<?php
// tests/Browser/AuthTest.php
namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends DuskTestCase
{
    public function test_user_can_register()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->type('name', 'Test User')
                    ->type('email', 'test@example.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->press('Register')
                    ->assertPathIs('/login')
                    ->assertSee('Registration successful');
        });
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'password' => Hash::make('password123')
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password123')
                    ->press('Login')
                    ->assertPathIs('/dashboard')
                    ->assertSee($user->name);
        });

        $user->delete();
    }

    public function test_user_can_update_profile()
    {
        $user = User::factory()->create([
            'email_verified_at' => now()
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->type('name', 'Updated Name')
                    ->press('Save Changes')
                    ->assertSee('Profile updated successfully');
        });

        $user->delete();
    }
}