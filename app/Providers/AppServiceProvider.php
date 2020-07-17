<?php

namespace App\Providers;

use App\Machine;
use App\User;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Cheating here since we don't really have authentication for this demo.
        // Just create a new user when we need one.
        $this->app->bind(User::class, function () {
            return $this->getDefaultUser();
        });

        // Also cheating here since we only have 1 predefined machine.
        $this->app->bind(Machine::class, function () {
            return $this->getDefaultMachine();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    private function getDefaultUser() {
        return User::firstOrCreate([
            'username' => 'default_user',
        ], [
            'coins' => 5,
        ]);
    }

    private function getDefaultMachine() {
        $user = $this->getDefaultUser();

        $machine = Machine::firstOrCreate([
            'name' => 'default_machine',
        ], [
            'coins' => 0,
            'user_id' => $user->id,
        ]);

        // If we have an incorrect amount of items, make new ones
        if ($machine->items()->count() !== 3) {
            $machine->items()->delete();
            for ($i = 0; $i < 3; $i++) {
                $machine->items()->create([
                    'count' => 5,
                ]);
            }
        }

        return $machine;
    }

}
