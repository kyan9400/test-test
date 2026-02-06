<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->manager = User::factory()->create();
        $this->manager->assignRole('manager');
    }

    public function test_guest_cannot_access_admin_tickets(): void
    {
        $this->get('/admin/tickets')->assertRedirect('/login');
    }

    public function test_manager_can_view_tickets_list(): void
    {
        Ticket::factory()->count(5)->create();

        $this->actingAs($this->manager)
            ->get('/admin/tickets')
            ->assertStatus(200)
            ->assertViewIs('admin.tickets.index')
            ->assertViewHas('tickets');
    }

    public function test_admin_can_view_tickets_list(): void
    {
        Ticket::factory()->count(5)->create();

        $this->actingAs($this->admin)
            ->get('/admin/tickets')
            ->assertStatus(200)
            ->assertViewIs('admin.tickets.index');
    }

    public function test_can_filter_tickets_by_status(): void
    {
        Ticket::factory()->new()->count(3)->create();
        Ticket::factory()->done()->count(2)->create();

        $this->actingAs($this->manager)
            ->get('/admin/tickets?status=new')
            ->assertStatus(200);
    }

    public function test_can_view_single_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $this->actingAs($this->manager)
            ->get("/admin/tickets/{$ticket->id}")
            ->assertStatus(200)
            ->assertViewIs('admin.tickets.show')
            ->assertViewHas('ticket');
    }

    public function test_can_update_ticket_status(): void
    {
        $ticket = Ticket::factory()->new()->create();

        $this->actingAs($this->manager)
            ->patch("/admin/tickets/{$ticket->id}/status", ['status' => 'in_progress'])
            ->assertRedirect("/admin/tickets/{$ticket->id}")
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'status' => 'in_progress']);
    }

    public function test_updating_status_to_done_sets_answered_at(): void
    {
        $ticket = Ticket::factory()->new()->create();

        $this->actingAs($this->manager)
            ->patch("/admin/tickets/{$ticket->id}/status", ['status' => 'done']);

        $ticket->refresh();

        $this->assertEquals(TicketStatus::DONE, $ticket->status);
        $this->assertNotNull($ticket->answered_at);
    }

    public function test_cannot_update_status_with_invalid_value(): void
    {
        $ticket = Ticket::factory()->create();

        $this->actingAs($this->manager)
            ->patch("/admin/tickets/{$ticket->id}/status", ['status' => 'invalid_status'])
            ->assertSessionHasErrors(['status']);
    }
}
