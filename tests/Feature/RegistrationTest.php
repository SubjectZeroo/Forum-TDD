<?php

namespace Tests\Feature;


use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{

    use DatabaseMigrations;
    /** @test */
    public function a_confirmation_email_is_sent_upon_registration()
    {
        // Notification::fake();

        // event(new Registered($user = User::factory()->create()));

        // Notification::assertSentTo($user, SendEmailVerificationNotification::class);

        Event::fake();

        $this->post(route('register'), [
            'name' => 'John',
            'email' => 'johndoe@test.com',
            'password' => 'passwordtest',
            'password_confirmation' => 'passwordtest'
        ]);

        Event::assertDispatched(Registered::class);
    }
}
