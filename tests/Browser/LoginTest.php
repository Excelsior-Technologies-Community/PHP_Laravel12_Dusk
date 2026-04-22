<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class LoginTest extends DuskTestCase
{
    /**
     * Test login and dashboard access.
     *
     * @return void
     */

public function test_login_and_dashboard()
{
    $user = \App\Models\User::factory()->create([
        'email' => 'test' . time() . '@example.com',
        'password' => bcrypt('password'),
    ]);

    $this->browse(function ($browser) use ($user) {

        $browser->visit('/login')
                ->pause(3000)

                ->waitForText('Login', 10) //  ensures page loaded
                ->assertPresent('input[name="email"]') //  check DOM

                ->type('input[name="email"]', $user->email)
                ->type('input[name="password"]', 'password')

                ->press('Login')
                ->pause(3000)

                ->assertPathIs('/dashboard');
    });
}

}
