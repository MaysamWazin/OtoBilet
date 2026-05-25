<?php

namespace App\States;

use App\Models\Ticket;

/**
 * State Pattern - Ticket State Interface
 */
abstract class TicketState
{
    abstract public function cancel(Ticket $ticket): void;
    abstract public function canCancel(): bool;
    abstract public function getStatus(): string;

    /**
     * Factory method - Status'tan state oluştur
     */
    public static function createFromStatus(string $status): TicketState
    {
        return match($status) {
            'active' => new ActiveState(),
            'cancelled' => new CancelledState(),
            'expired' => new ExpiredState(),
            default => new ActiveState()
        };
    }
}

/**
 * Active State - Aktif bilet
 */
class ActiveState extends TicketState
{
    public function cancel(Ticket $ticket): void
    {
        $ticket->setState(new CancelledState());
    }

    public function canCancel(): bool
    {
        return true;
    }

    public function getStatus(): string
    {
        return 'active';
    }
}

/**
 * Cancelled State - İptal edilmiş bilet
 */
class CancelledState extends TicketState
{
    public function cancel(Ticket $ticket): void
    {
        throw new \Exception("Bilet zaten iptal edilmiş");
    }

    public function canCancel(): bool
    {
        return false;
    }

    public function getStatus(): string
    {
        return 'cancelled';
    }
}

/**
 * Expired State - Süresi dolmuş bilet
 */
class ExpiredState extends TicketState
{
    public function cancel(Ticket $ticket): void
    {
        throw new \Exception("Süresi dolmuş bilet iptal edilemez");
    }

    public function canCancel(): bool
    {
        return false;
    }

    public function getStatus(): string
    {
        return 'expired';
    }
}

