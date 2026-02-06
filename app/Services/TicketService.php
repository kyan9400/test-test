<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TicketStatus;
use App\Exceptions\DailyTicketLimitException;
use App\Models\Ticket;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

class TicketService
{
    public function __construct(
        private readonly TicketRepositoryInterface $ticketRepository,
        private readonly CustomerService $customerService
    ) {}

    public function createTicket(array $data, array $files = []): Ticket
    {
        $customer = $this->customerService->findOrCreate([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);

        if ($this->ticketRepository->hasTicketCreatedToday($customer->id)) {
            throw new DailyTicketLimitException(
                'You can only submit one ticket per day.'
            );
        }

        $ticket = $this->ticketRepository->create([
            'customer_id' => $customer->id,
            'subject' => $data['subject'],
            'text' => $data['text'],
            'status' => TicketStatus::NEW,
        ]);

        if (! empty($files)) {
            $this->attachFiles($ticket, $files);
        }

        return $ticket->load(['customer', 'media']);
    }

    public function getTickets(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->ticketRepository->getPaginated($filters, $perPage);
    }

    public function getTicket(int $id): ?Ticket
    {
        return $this->ticketRepository->findWithRelations($id, ['customer', 'assignedUser', 'media']);
    }

    public function updateStatus(Ticket $ticket, TicketStatus|string $status): Ticket
    {
        if (is_string($status)) {
            $status = TicketStatus::from($status);
        }

        return $this->ticketRepository->updateStatus($ticket, $status);
    }

    public function assignToUser(Ticket $ticket, int $userId): Ticket
    {
        return $this->ticketRepository->assignToUser($ticket, $userId);
    }

    public function getStatistics(string $period = 'day'): array
    {
        return $this->ticketRepository->getStatistics($period);
    }

    private function attachFiles(Ticket $ticket, array $files): void
    {
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $ticket->addMedia($file)
                    ->toMediaCollection('attachments');
            }
        }
    }
}
