<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTicketStatusRequest;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'date_from', 'date_to', 'email', 'phone']);
        $tickets = $this->ticketService->getTickets($filters);

        return view('admin.tickets.index', compact('tickets', 'filters'));
    }

    public function show(Ticket $ticket): View
    {
        $ticket = $this->ticketService->getTicket($ticket->id);

        return view('admin.tickets.show', compact('ticket'));
    }

    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->ticketService->updateStatus($ticket, $request->validated('status'));

        return redirect()
            ->route('admin.tickets.show', $ticket)
            ->with('success', 'Ticket status updated successfully.');
    }

    public function downloadFile(Ticket $ticket, Media $media): StreamedResponse
    {
        if ($media->model_id !== $ticket->id || $media->model_type !== Ticket::class) {
            abort(404);
        }

        return response()->streamDownload(function () use ($media) {
            echo file_get_contents($media->getPath());
        }, $media->file_name);
    }
}
