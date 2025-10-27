<?php

namespace Database\Factories;

use App\Enums\ResourceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resource>
 */
class ResourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(ResourceType::cases());
        
        return [
            'name' => $this->generateNameByType($type),
            'type' => $type,
            'description' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Generate appropriate name based on resource type.
     */
    private function generateNameByType(ResourceType $type): string
    {
        return match ($type) {
            ResourceType::ROOM => fake()->randomElement([
                'Conference Room A',
                'Meeting Room B',
                'Board Room',
                'Training Room',
                'Executive Suite',
            ]) . ' - Floor ' . fake()->numberBetween(1, 10),
            ResourceType::VEHICLE => fake()->randomElement([
                'Company Van',
                'Delivery Truck',
                'Executive Car',
                'Service Vehicle',
            ]) . ' (' . fake()->lexify('???-####') . ')',
            ResourceType::EQUIPMENT => fake()->randomElement([
                'Projector',
                'Laptop',
                'Camera',
                'Microphone System',
                'Video Conference Setup',
                'Whiteboard',
            ]) . ' #' . fake()->numberBetween(1, 100),
            ResourceType::SPACE => fake()->randomElement([
                'Parking Spot',
                'Storage Unit',
                'Workstation',
                'Locker',
            ]) . ' ' . fake()->bothify('##?'),
        };
    }
}
