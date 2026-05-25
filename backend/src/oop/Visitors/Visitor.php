<?php

namespace App\Visitors;

use App\Models\Ticket;
use App\Models\Trip;
use App\Models\User;

/**
 * Visitor Pattern - Visitor Interface
 */
interface Visitor
{
    public function visitTicket(Ticket $ticket): string;
    public function visitTrip(Trip $trip): string;
    public function visitUser(User $user): string;
}

/**
 * Visitor Pattern - Element Interface
 */
interface Element
{
    public function accept(Visitor $visitor): string;
}

/**
 * PDF Report Visitor - PDF raporu oluşturur
 */
class PDFReportVisitor implements Visitor
{
    public function visitTicket(Ticket $ticket): string
    {
        $data = $ticket->toArray();
        return "PDF Ticket Report:\n" . 
               "Ticket ID: {$data['id']}\n" .
               "Trip ID: {$data['trip_id']}\n" .
               "User ID: {$data['user_id']}\n" .
               "Total Price: {$data['total_price']} TL\n" .
               "Status: {$data['status']}\n";
    }

    public function visitTrip(Trip $trip): string
    {
        return "PDF Trip Report:\n" .
               "Trip ID: {$trip->getId()}\n" .
               "Route: {$trip->getDepartureCity()} → {$trip->getDestinationCity()}\n" .
               "Price: {$trip->getPrice()} TL\n" .
               "Capacity: {$trip->getCapacity()}\n";
    }

    public function visitUser(User $user): string
    {
        return "PDF User Report:\n" .
               "User ID: {$user->getId()}\n" .
               "Name: {$user->getFullName()}\n" .
               "Email: {$user->getEmail()}\n" .
               "Role: {$user->getRole()}\n" .
               "Balance: {$user->getBalance()} TL\n";
    }
}

/**
 * JSON Report Visitor - JSON raporu oluşturur
 */
class JSONReportVisitor implements Visitor
{
    public function visitTicket(Ticket $ticket): string
    {
        return json_encode($ticket->toArray(), JSON_PRETTY_PRINT);
    }

    public function visitTrip(Trip $trip): string
    {
        return json_encode($trip->toArray(), JSON_PRETTY_PRINT);
    }

    public function visitUser(User $user): string
    {
        return json_encode($user->toArray(), JSON_PRETTY_PRINT);
    }
}

/**
 * Ticket Element - Visitor Pattern için
 */
class TicketElement implements Element
{
    private Ticket $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function accept(Visitor $visitor): string
    {
        return $visitor->visitTicket($this->ticket);
    }
}

