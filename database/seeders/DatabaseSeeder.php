<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Generate and display API token for the test user
        $token = $user->createToken('dev-token')->plainTextToken;
        echo "Test User API Token: {$token}\n";

        // Seed resources and reservations
        $this->call([
            ResourceSeeder::class,
            ReservationSeeder::class,
        ]);
    }
}
