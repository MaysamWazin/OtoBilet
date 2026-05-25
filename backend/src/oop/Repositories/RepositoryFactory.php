<?php

namespace App\Repositories;

use App\Database\DatabaseAdapter;

/**
 * Abstract Factory Pattern - Repository Factory
 * Farklı repository türlerini oluşturur
 */
class RepositoryFactory
{
    private DatabaseAdapter $adapter;

    public function __construct(DatabaseAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * User Repository oluştur
     */
    public function createUserRepository(): UserRepository
    {
        return new UserRepository($this->adapter);
    }

    /**
     * Trip Repository oluştur
     */
    public function createTripRepository(): TripRepository
    {
        return new TripRepository($this->adapter);
    }

    /**
     * Ticket Repository oluştur
     */
    public function createTicketRepository(): TicketRepository
    {
        return new TicketRepository($this->adapter);
    }

    /**
     * Company Repository oluştur
     */
    public function createCompanyRepository(): CompanyRepository
    {
        return new CompanyRepository($this->adapter);
    }
}

