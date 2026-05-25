<?php

namespace App\Facades;

use App\Mediators\TicketPurchaseMediator;
use App\Repositories\RepositoryFactory;
use App\Strategies\BalancePaymentStrategy;
use App\Observers\EventManager;
use App\Observers\EmailNotificationObserver;
use App\Observers\LogObserver;
use App\Database\DatabaseAdapter;

/**
 * Facade Pattern - Booking Facade
 * Karmaşık bilet satın alma işlemlerini basitleştirir
 */
class BookingFacade
{
    private TicketPurchaseMediator $mediator;
    private RepositoryFactory $repositoryFactory;

    public function __construct()
    {
        $adapter = new DatabaseAdapter();
        $this->repositoryFactory = new RepositoryFactory($adapter);
        
        $paymentStrategy = new BalancePaymentStrategy();
        $eventManager = new EventManager();
        
        // Observer'ları ekle
        $eventManager->attach(new EmailNotificationObserver());
        $eventManager->attach(new LogObserver());
        
        $this->mediator = new TicketPurchaseMediator(
            $this->repositoryFactory,
            $paymentStrategy,
            $eventManager
        );
    }

    /**
     * Basit bilet satın alma arayüzü
     */
    public function purchaseTicket(
        string $userId,
        string $tripId,
        array $selectedSeats,
        ?string $couponCode = null
    ): array {
        try {
            $userRepository = $this->repositoryFactory->createUserRepository();
            $tripRepository = $this->repositoryFactory->createTripRepository();
            
            $user = $userRepository->findById($userId);
            $trip = $tripRepository->findById($tripId);
            
            if ($user === null || $trip === null) {
                throw new \Exception("Kullanıcı veya sefer bulunamadı");
            }
            
            $ticket = $this->mediator->purchaseTicket($user, $trip, $selectedSeats, $couponCode);
            
            return [
                'success' => true,
                'ticket_id' => $ticket->getId(),
                'message' => 'Bilet başarıyla satın alındı'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Seferleri ara
     */
    public function searchTrips(?string $departureCity = null, ?string $destinationCity = null): array
    {
        $tripRepository = $this->repositoryFactory->createTripRepository();
        return $tripRepository->findByRoute($departureCity, $destinationCity);
    }
}

