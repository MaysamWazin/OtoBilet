<?php

namespace App\Mediators;

use App\Models\User;
use App\Models\Trip;
use App\Models\Ticket;
use App\Repositories\RepositoryFactory;
use App\Strategies\PaymentStrategy;
use App\Observers\EventManager;

/**
 * Mediator Pattern - Ticket Purchase Mediator
 * Bilet satın alma işlemlerini koordine eder
 */
class TicketPurchaseMediator
{
    private RepositoryFactory $repositoryFactory;
    private PaymentStrategy $paymentStrategy;
    private EventManager $eventManager;

    public function __construct(
        RepositoryFactory $repositoryFactory,
        PaymentStrategy $paymentStrategy,
        EventManager $eventManager
    ) {
        $this->repositoryFactory = $repositoryFactory;
        $this->paymentStrategy = $paymentStrategy;
        $this->eventManager = $eventManager;
    }

    /**
     * Bilet satın alma işlemini koordine et
     */
    public function purchaseTicket(
        User $user,
        Trip $trip,
        array $selectedSeats,
        ?string $couponCode = null
    ): Ticket {
        // 1. Koltuk kontrolü
        $this->validateSeats($trip, $selectedSeats);

        // 2. Fiyat hesaplama
        $totalPrice = $this->calculatePrice($trip, $selectedSeats, $couponCode);

        // 3. Ödeme işlemi
        $this->paymentStrategy->pay($user, $totalPrice);

        // 4. Bilet oluşturma
        $ticket = $this->createTicket($user, $trip, $selectedSeats, $totalPrice);

        // 5. Kullanıcı bakiyesini güncelle
        $userRepository = $this->repositoryFactory->createUserRepository();
        $userRepository->update($user);

        // 6. Event gönder
        $this->eventManager->notify('ticket_purchased', [
            'ticket_id' => $ticket->getId(),
            'user_id' => $user->getId(),
            'total_price' => $totalPrice
        ]);

        return $ticket;
    }

    private function validateSeats(Trip $trip, array $selectedSeats): void
    {
        $ticketRepository = $this->repositoryFactory->createTicketRepository();
        $bookedSeats = $ticketRepository->getBookedSeats($trip->getId());

        foreach ($selectedSeats as $seat) {
            if (in_array($seat, $bookedSeats)) {
                throw new \Exception("Koltuk $seat zaten rezerve edilmiş");
            }
            if ($seat > $trip->getCapacity()) {
                throw new \Exception("Geçersiz koltuk numarası: $seat");
            }
        }
    }

    private function calculatePrice(Trip $trip, array $selectedSeats, ?string $couponCode): float
    {
        $totalPrice = $trip->getPrice() * count($selectedSeats);

        if ($couponCode !== null) {
            $couponRepository = $this->repositoryFactory->createCouponRepository();
            $coupon = $couponRepository->findByCode($couponCode);
            
            if ($coupon !== null && $coupon->isValid()) {
                $discount = $totalPrice * ($coupon->getDiscount() / 100);
                $totalPrice -= $discount;
            }
        }

        return $totalPrice;
    }

    private function createTicket(User $user, Trip $trip, array $selectedSeats, float $totalPrice): Ticket
    {
        $ticket = new \App\Models\Ticket(
            uniqid('ticket_', true),
            $trip->getId(),
            $user->getId(),
            null,
            $totalPrice,
            $selectedSeats
        );

        $ticketRepository = $this->repositoryFactory->createTicketRepository();
        $ticketRepository->save($ticket);

        return $ticket;
    }
}

