<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketStatus: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'Новая',
            self::IN_PROGRESS => 'В работе',
            self::DONE => 'Выполнена',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

