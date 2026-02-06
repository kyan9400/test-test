@extends('admin.layouts.app')

@section('title', 'Tickets')

@push('styles')
<style>
    .filters {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
        padding: 16px;
        background: var(--surface);
        border-radius: 8px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .filter-group label {
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--text-muted);
    }

    .filter-group input,
    .filter-group select {
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 0.875rem;
        min-width: 150px;
    }

    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid var(--border);
    }

    th {
        background: #f9fafb;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
    }

    td {
        font-size: 0.875rem;
    }

    tr:hover td {
        background: #f9fafb;
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

    .pagination {
        display: flex;
        gap: 8px;
        justify-content: center;
        margin-top: 20px;
    }

    .pagination a,
    .pagination span {
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.875rem;
        color: var(--text);
    }

    .pagination a:hover {
        background: var(--border);
    }

    .pagination .active span {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .empty-state {
        text-align: center;
        padding: 48px 20px;
        color: var(--text-muted);
    }

    .empty-state h3 {
        margin-bottom: 8px;
        color: var(--text);
    }
</style>
@endpush

@section('content')
<form action="{{ route('admin.tickets.index') }}" method="GET" class="filters">
    <div class="filter-group">
        <label for="status">Status</label>
        <select name="status" id="status">
            <option value="">All</option>
            @foreach(\App\Enums\TicketStatus::cases() as $status)
                <option value="{{ $status->value }}" {{ ($filters['status'] ?? '') === $status->value ? 'selected' : '' }}>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="filter-group">
        <label for="date_from">Date From</label>
        <input type="date" name="date_from" id="date_from" value="{{ $filters['date_from'] ?? '' }}">
    </div>

    <div class="filter-group">
        <label for="date_to">Date To</label>
        <input type="date" name="date_to" id="date_to" value="{{ $filters['date_to'] ?? '' }}">
    </div>

    <div class="filter-group">
        <label for="email">Email</label>
        <input type="text" name="email" id="email" value="{{ $filters['email'] ?? '' }}" placeholder="Search by email">
    </div>

    <div class="filter-group">
        <label for="phone">Phone</label>
        <input type="text" name="phone" id="phone" value="{{ $filters['phone'] ?? '' }}" placeholder="Search by phone">
    </div>

    <div class="filter-group" style="justify-content: flex-end;">
        <label>&nbsp;</label>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary btn-sm">Reset</a>
        </div>
    </div>
</form>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Assigned To</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td>#{{ $ticket->id }}</td>
                        <td>{{ Str::limit($ticket->subject, 40) }}</td>
                        <td>
                            <div>{{ $ticket->customer->name }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $ticket->customer->email }}</div>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $ticket->status->value }}">
                                {{ $ticket->status->label() }}
                            </span>
                        </td>
                        <td>{{ $ticket->created_at->format('M d, Y H:i') }}</td>
                        <td>{{ $ticket->assignedUser?->name ?? '—' }}</td>
                        <td>
                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-primary btn-sm">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <h3>No tickets found</h3>
                                <p>Try adjusting your filters or wait for new submissions.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($tickets->hasPages())
    <div class="pagination">
        {{ $tickets->withQueryString()->links('pagination::simple-tailwind') }}
    </div>
@endif
@endsection

