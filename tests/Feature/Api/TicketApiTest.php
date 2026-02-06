<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_can_create_ticket_with_valid_data(): void
    {
        $response = $this->postJson('/api/tickets', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'subject' => 'Test Subject',
            'text' => 'This is a test message.',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'subject', 'text', 'status', 'status_label', 'created_at', 'customer'],
            ]);

        $this->assertDatabaseHas('tickets', ['subject' => 'Test Subject', 'status' => 'new']);
        $this->assertDatabaseHas('customers', ['email' => 'john@example.com', 'phone' => '+1234567890']);
    }

    public function test_can_create_ticket_with_files(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson('/api/tickets', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'subject' => 'Test Subject',
            'text' => 'This is a test message.',
            'files' => [$file],
        ]);

        $response->assertStatus(201);

        $ticket = Ticket::first();
        $this->assertCount(1, $ticket->getMedia('attachments'));
    }

    public function test_validation_fails_with_invalid_phone(): void
    {
        $response = $this->postJson('/api/tickets', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '123456',
            'subject' => 'Test Subject',
            'text' => 'This is a test message.',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['phone']);
    }

    public function test_validation_fails_with_missing_required_fields(): void
    {
        $response = $this->postJson('/api/tickets', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'phone', 'subject', 'text']);
    }

    public function test_cannot_create_more_than_one_ticket_per_day(): void
    {
        $this->postJson('/api/tickets', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'subject' => 'First ticket',
            'text' => 'First message.',
        ])->assertStatus(201);

        $response = $this->postJson('/api/tickets', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+9876543210',
            'subject' => 'Second ticket',
            'text' => 'Second message.',
        ]);

        $response->assertStatus(429)->assertJson(['error' => 'daily_limit_exceeded']);
    }

    public function test_statistics_requires_authentication(): void
    {
        $this->getJson('/api/tickets/statistics')->assertStatus(401);
    }

    public function test_authenticated_manager_can_get_statistics(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('manager');

        Ticket::factory()->count(5)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/tickets/statistics?period=day');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['period', 'total', 'by_status']]);
    }
}
