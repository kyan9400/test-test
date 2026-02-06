# Architectural Decisions

## Repository Pattern
Data access is abstracted through repository interfaces, keeping business logic in services separate from Eloquent queries. This makes swapping storage or writing unit tests straightforward.

## Service Layer
All business logic lives in service classes. Controllers only handle HTTP concerns (receive request, call service, return response).

## FormRequest Validation
Every incoming request is validated through a dedicated FormRequest class instead of inline controller validation. This keeps controllers thin and makes validation rules reusable.

## API Resources
All API responses go through JsonResource classes. This decouples internal model structure from the public API contract.

## Spatie Media Library
Chosen for file handling because it provides polymorphic media associations, collection management, and disk abstraction out of the box.

## Spatie Permission
Handles role-based access with middleware integration. Two roles: `admin` and `manager`.

## PHP Enum for Ticket Status
Using native PHP 8.1+ backed enum gives type safety, IDE support, and a clean place to attach labels.

## Daily Ticket Limit
Enforced in `TicketService` — checks if the customer already has a ticket created today before allowing a new one. Returns 429 if exceeded.

## Customer Deduplication
Customers are matched by email or phone. If a matching customer exists, their name is updated; otherwise a new record is created.

## Widget as Standalone Page
The widget is a self-contained HTML page with inline CSS and vanilla JS. No framework dependencies, no CSS conflicts when embedded via iframe.

## Statistics via Eloquent Scopes
Date-based filtering is implemented as model scopes (`createdToday`, `createdThisWeek`, `createdThisMonth`) for reuse across the codebase.

## Session Auth for Admin, Sanctum for API
Admin panel uses standard session authentication. The statistics API endpoint uses Sanctum tokens for flexibility with external consumers.
