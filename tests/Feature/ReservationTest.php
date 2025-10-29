<?php

use App\Models\Reservation;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('creates a reservation successfully with valid data', function (): void {
    $user = User::factory()->create();
    $resource = Resource::factory()->create();

    Sanctum::actingAs($user);

    $data = [
        'resource_id' => $resource->id,
        'start_time' => now()->addHours(2)->toIso8601String(),
        'end_time' => now()->addHours(4)->toIso8601String(),
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'notes' => 'Test reservation',
    ];

    $response = $this->postJson('/api/reservations', $data);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'resource' => ['id'],
                'start_time',
                'end_time',
                'duration_minutes',
                'customer' => ['name', 'email'],
                'notes',
                'created_at',
                'updated_at',
            ],
        ])
        ->assertJson([
            'message' => 'Reservation created successfully',
            'data' => [
                'customer' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                ],
                'notes' => 'Test reservation',
            ],
        ]);

    $this->assertDatabaseHas('reservations', [
        'resource_id' => $resource->id,
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
    ]);
});

test('requires authentication with bearer token', function (): void {
    $resource = Resource::factory()->create();

    $data = [
        'resource_id' => $resource->id,
        'start_time' => now()->addHours(2)->toIso8601String(),
        'end_time' => now()->addHours(4)->toIso8601String(),
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
    ];

    $response = $this->postJson('/api/reservations', $data);

    $response->assertStatus(401);
});

test('validates required fields', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/reservations', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'resource_id',
            'start_time',
            'end_time',
            'customer_name',
            'customer_email',
        ]);
});

test('validates resource_id must exist in database', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $data = [
        'resource_id' => 99999,
        'start_time' => now()->addHours(2)->toIso8601String(),
        'end_time' => now()->addHours(4)->toIso8601String(),
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
    ];

    $response = $this->postJson('/api/reservations', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['resource_id'])
        ->assertJson([
            'errors' => [
                'resource_id' => ['The selected resource does not exist.'],
            ],
        ]);
});

test('validates start_time must be in the future', function (): void {
    $user = User::factory()->create();
    $resource = Resource::factory()->create();
    Sanctum::actingAs($user);

    $data = [
        'resource_id' => $resource->id,
        'start_time' => now()->subHour()->toIso8601String(),
        'end_time' => now()->addHours(2)->toIso8601String(),
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
    ];

    $response = $this->postJson('/api/reservations', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['start_time'])
        ->assertJson([
            'errors' => [
                'start_time' => ['The reservation must start in the future.'],
            ],
        ]);
});

test('validates end_time must be after start_time', function (): void {
    $user = User::factory()->create();
    $resource = Resource::factory()->create();
    Sanctum::actingAs($user);

    $data = [
        'resource_id' => $resource->id,
        'start_time' => now()->addHours(4)->toIso8601String(),
        'end_time' => now()->addHours(2)->toIso8601String(),
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
    ];

    $response = $this->postJson('/api/reservations', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['end_time'])
        ->assertJson([
            'errors' => [
                'end_time' => ['The end time must be after the start time.'],
            ],
        ]);
});

test('validates customer_email must be valid email format', function (): void {
    $user = User::factory()->create();
    $resource = Resource::factory()->create();
    Sanctum::actingAs($user);

    $data = [
        'resource_id' => $resource->id,
        'start_time' => now()->addHours(2)->toIso8601String(),
        'end_time' => now()->addHours(4)->toIso8601String(),
        'customer_name' => 'John Doe',
        'customer_email' => 'invalid-email',
    ];

    $response = $this->postJson('/api/reservations', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['customer_email']);
});

test('validates customer_name max length is 255 characters', function (): void {
    $user = User::factory()->create();
    $resource = Resource::factory()->create();
    Sanctum::actingAs($user);

    $data = [
        'resource_id' => $resource->id,
        'start_time' => now()->addHours(2)->toIso8601String(),
        'end_time' => now()->addHours(4)->toIso8601String(),
        'customer_name' => str_repeat('a', 256),
        'customer_email' => 'john@example.com',
    ];

    $response = $this->postJson('/api/reservations', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['customer_name']);
});

test('validates notes max length is 1000 characters', function (): void {
    $user = User::factory()->create();
    $resource = Resource::factory()->create();
    Sanctum::actingAs($user);

    $data = [
        'resource_id' => $resource->id,
        'start_time' => now()->addHours(2)->toIso8601String(),
        'end_time' => now()->addHours(4)->toIso8601String(),
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'notes' => str_repeat('a', 1001),
    ];

    $response = $this->postJson('/api/reservations', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['notes']);
});

test('allows notes to be optional', function (): void {
    $user = User::factory()->create();
    $resource = Resource::factory()->create();
    Sanctum::actingAs($user);

    $data = [
        'resource_id' => $resource->id,
        'start_time' => now()->addHours(2)->toIso8601String(),
        'end_time' => now()->addHours(4)->toIso8601String(),
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
    ];

    $response = $this->postJson('/api/reservations', $data);

    $response->assertStatus(201);

    $this->assertDatabaseHas('reservations', [
        'customer_name' => 'John Doe',
        'notes' => null,
    ]);
});

test('prevents overlapping reservations for the same resource', function (): void {
    $user = User::factory()->create();
    $resource = Resource::factory()->create();
    Sanctum::actingAs($user);

    // Create existing reservation
    Reservation::factory()->create([
        'resource_id' => $resource->id,
        'start_time' => now()->addHours(2),
        'end_time' => now()->addHours(4),
    ]);

    // Try to create overlapping reservation
    $data = [
        'resource_id' => $resource->id,
        'start_time' => now()->addHours(3)->toIso8601String(),
        'end_time' => now()->addHours(5)->toIso8601String(),
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
    ];

    $response = $this->postJson('/api/reservations', $data);

    $response->assertStatus(409);
});

test('allows reservations for same time slot on different resources', function (): void {
    $user = User::factory()->create();
    $resource1 = Resource::factory()->create();
    $resource2 = Resource::factory()->create();
    Sanctum::actingAs($user);

    // Create reservation for first resource
    Reservation::factory()->create([
        'resource_id' => $resource1->id,
        'start_time' => now()->addHours(2),
        'end_time' => now()->addHours(4),
    ]);

    // Create reservation for second resource at the same time
    $data = [
        'resource_id' => $resource2->id,
        'start_time' => now()->addHours(2)->toIso8601String(),
        'end_time' => now()->addHours(4)->toIso8601String(),
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
    ];

    $response = $this->postJson('/api/reservations', $data);

    $response->assertStatus(201);
});

test('allows consecutive reservations without overlap', function (): void {
    $user = User::factory()->create();
    $resource = Resource::factory()->create();
    Sanctum::actingAs($user);

    // Create first reservation
    Reservation::factory()->create([
        'resource_id' => $resource->id,
        'start_time' => now()->addHours(2),
        'end_time' => now()->addHours(4),
    ]);

    // Create consecutive reservation (starts exactly when first ends)
    $data = [
        'resource_id' => $resource->id,
        'start_time' => now()->addHours(4)->toIso8601String(),
        'end_time' => now()->addHours(6)->toIso8601String(),
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
    ];

    $response = $this->postJson('/api/reservations', $data);

    $response->assertStatus(201);
});

test('calculates duration_minutes correctly in response', function (): void {
    $user = User::factory()->create();
    $resource = Resource::factory()->create();
    Sanctum::actingAs($user);

    $startTime = now()->addHours(2);
    $endTime = now()->addHours(5); // 3 hours = 180 minutes

    $data = [
        'resource_id' => $resource->id,
        'start_time' => $startTime->toIso8601String(),
        'end_time' => $endTime->toIso8601String(),
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
    ];

    $response = $this->postJson('/api/reservations', $data);

    $response->assertStatus(201)
        ->assertJsonPath('data.duration_minutes', 180);
});

test('returns 405 for unsupported HTTP methods', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->getJson('/api/reservations')->assertStatus(405);
    $this->putJson('/api/reservations')->assertStatus(405);
    $this->patchJson('/api/reservations')->assertStatus(405);
    $this->deleteJson('/api/reservations')->assertStatus(405);
});

test('returns 404 for any path under reservations except root POST', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->getJson('/api/reservations/1')->assertStatus(404);
    $this->putJson('/api/reservations/1')->assertStatus(404);
    $this->deleteJson('/api/reservations/1')->assertStatus(404);
});
