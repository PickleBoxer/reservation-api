<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Resource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all resources
        $resources = Resource::all();

        // Create 3-5 reservations for each resource
        $resources->each(function ($resource) {
            Reservation::factory()
                ->count(rand(3, 5))
                ->create([
                    'resource_id' => $resource->id,
                ]);
        });
    }
}
