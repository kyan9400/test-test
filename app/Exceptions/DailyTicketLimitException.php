<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailyTicketLimitException extends Exception
{
    public function __construct(
        string $message = 'Daily ticket limit exceeded.',
        int $code = 429
    ) {
        parent::__construct($message, $code);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error' => 'daily_limit_exceeded',
        ], $this->getCode());
    }
}
