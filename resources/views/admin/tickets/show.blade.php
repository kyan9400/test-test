@extends('admin.layouts.app')

@section('title', 'Ticket #' . $ticket->id)

@push('styles')
<style>
    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
    }

    .ticket-subject {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .ticket-meta {
        display: flex;
        gap: 16px;
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-new {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-in_progress {
        background: #fef3c7;
        color: #92400e;
    }

    .status-done {
        background: #d1fae5;
        color: #065f46;
    }

    .grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    @media (max-width: 768px) {
        .grid {
            grid-template-columns: 1fr;
        }
    }

    .section-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 12px;
    }

    .ticket-text {
        white-space: pre-wrap;
        line-height: 1.8;
        color: var(--text);
    }

    .info-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .info-value {
        font-size: 0.875rem;
    }

    .files-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .file-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px;
        background: #f9fafb;
        border-radius: 8px;
    }

    .file-info {
        display: flex;
        flex-direction: column;
    }

    .file-name {
        font-weight: 500;
        font-size: 0.875rem;
    }

    .file-size {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .status-form {
        display: flex;
        gap: 8px;
        margin-top: 16px;
    }

    .status-form select {
        flex: 1;
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 0.875rem;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        text-decoration: none;
        font-size: 0.875rem;
        margin-bottom: 16px;
    }

    .back-link:hover {
        color: var(--primary);
    }
</style>
@endpush

@section('content')
<a href="{{ route('admin.tickets.index') }}" class="back-link">
    ← Back to tickets
</a>

<div class="ticket-header">
    <div>
        <h1 class="ticket-subject">{{ $ticket->subject }}</h1>
        <div class="ticket-meta">
            <span>Created {{ $ticket->created_at->diffForHumans() }}</span>
            <span class="status-badge status-{{ $ticket->status->value }}">
                {{ $ticket->status->label() }}
            </span>
        </div>
    </div>
</div>

<div class="grid">
    <div>
        <div class="card">
            <div class="card-header">Message</div>
            <div class="card-body">
                <div class="ticket-text">{{ $ticket->text }}</div>
            </div>
        </div>

        @if($ticket->getMedia('attachments')->count() > 0)
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">Attachments</div>
                <div class="card-body">
                    <div class="files-list">
                        @foreach($ticket->getMedia('attachments') as $media)
                            <div class="file-item">
                                <div class="file-info">
                                    <span class="file-name">{{ $media->file_name }}</span>
                                    <span class="file-size">{{ number_format($media->size / 1024, 2) }} KB</span>
                                </div>
                                <a href="{{ route('admin.tickets.download-file', [$ticket, $media]) }}" class="btn btn-primary btn-sm">
                                    Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div>
        <div class="card">
            <div class="card-header">Customer Information</div>
            <div class="card-body">
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Name</span>
                        <span class="info-value">{{ $ticket->customer->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value">
                            <a href="mailto:{{ $ticket->customer->email }}">{{ $ticket->customer->email }}</a>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone</span>
                        <span class="info-value">
                            <a href="tel:{{ $ticket->customer->phone }}">{{ $ticket->customer->phone }}</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-top: 24px;">
            <div class="card-header">Ticket Details</div>
            <div class="card-body">
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Ticket ID</span>
                        <span class="info-value">#{{ $ticket->id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Created At</span>
                        <span class="info-value">{{ $ticket->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Assigned To</span>
                        <span class="info-value">{{ $ticket->assignedUser?->name ?? 'Not assigned' }}</span>
                    </div>
                    @if($ticket->answered_at)
                        <div class="info-item">
                            <span class="info-label">Answered At</span>
                            <span class="info-value">{{ $ticket->answered_at->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card" style="margin-top: 24px;">
            <div class="card-header">Update Status</div>
            <div class="card-body">
                <form action="{{ route('admin.tickets.update-status', $ticket) }}" method="POST" class="status-form">
                    @csrf
                    @method('PATCH')
                    <select name="status" required>
                        @foreach(\App\Enums\TicketStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ $ticket->status === $status ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

