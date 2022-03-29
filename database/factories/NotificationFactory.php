<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $factory->define(\Illuminate\Notifications\DatabaseNotification::class, function ($faker) {
            return [
                'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
                'type' => 'App\Notifications\ThreadWasUpdated',
                'notifiable_id' => function () {
                    return auth()->id() ?: factory('App\User')->create()->id;
                },
                'notifiable_type' => 'App\User',
                'data' => ['foo' => 'bar']
            ];
        });
    }
}
