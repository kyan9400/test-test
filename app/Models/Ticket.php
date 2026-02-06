<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Ticket extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'customer_id',
        'assigned_user_id',
        'subject',
        'text',
        'status',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'answered_at' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->useDisk('public');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function scopeStatus(Builder $query, TicketStatus|string $status): Builder
    {
        if (is_string($status)) {
            $status = TicketStatus::from($status);
        }

        return $query->where('status', $status);
    }

    public function scopeCreatedToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeCreatedThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeCreatedThisMonth(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
    }

    public function scopeDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }

    public function scopeCustomerEmail(Builder $query, string $email): Builder
    {
        return $query->whereHas('customer', fn (Builder $q) => $q->where('email', 'like', "%{$email}%"));
    }

    public function scopeCustomerPhone(Builder $query, string $phone): Builder
    {
        return $query->whereHas('customer', fn (Builder $q) => $q->where('phone', 'like', "%{$phone}%"));
    }
}
