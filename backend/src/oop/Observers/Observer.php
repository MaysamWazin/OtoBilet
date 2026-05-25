<?php

namespace App\Observers;

/**
 * Observer Pattern - Observer Interface
 */
interface Observer
{
    public function update(string $event, $data): void;
}

/**
 * Observer Pattern - Subject Interface
 */
interface Subject
{
    public function attach(Observer $observer): void;
    public function detach(Observer $observer): void;
    public function notify(string $event, $data): void;
}

/**
 * Event Manager - Observer Pattern Implementation
 */
class EventManager implements Subject
{
    private array $observers = [];

    public function attach(Observer $observer): void
    {
        $this->observers[] = $observer;
    }

    public function detach(Observer $observer): void
    {
        $key = array_search($observer, $this->observers, true);
        if ($key !== false) {
            unset($this->observers[$key]);
        }
    }

    public function notify(string $event, $data): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($event, $data);
        }
    }
}

/**
 * Email Notification Observer
 */
class EmailNotificationObserver implements Observer
{
    public function update(string $event, $data): void
    {
        switch ($event) {
            case 'ticket_purchased':
                $this->sendTicketConfirmation($data);
                break;
            case 'ticket_cancelled':
                $this->sendCancellationEmail($data);
                break;
        }
    }

    private function sendTicketConfirmation($data): void
    {
        // Email gönderme işlemi
        error_log("Email: Bilet satın alındı - " . json_encode($data));
    }

    private function sendCancellationEmail($data): void
    {
        // Email gönderme işlemi
        error_log("Email: Bilet iptal edildi - " . json_encode($data));
    }
}

/**
 * Log Observer
 */
class LogObserver implements Observer
{
    public function update(string $event, $data): void
    {
        error_log("Event: $event - " . json_encode($data));
    }
}

