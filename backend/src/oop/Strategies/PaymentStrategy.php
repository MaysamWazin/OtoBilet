<?php

namespace App\Strategies;

use App\Models\User;
use App\Models\Ticket;

/**
 * Strategy Pattern - Payment Strategy Interface
 */
interface PaymentStrategy
{
    public function pay(User $user, float $amount): bool;
    public function refund(User $user, float $amount): bool;
}

/**
 * Balance Payment Strategy - Bakiye ile ödeme
 */
class BalancePaymentStrategy implements PaymentStrategy
{
    public function pay(User $user, float $amount): bool
    {
        if ($user->getBalance() < $amount) {
            throw new \Exception("Yetersiz bakiye");
        }

        $newBalance = $user->getBalance() - $amount;
        $user->setBalance($newBalance);
        return true;
    }

    public function refund(User $user, float $amount): bool
    {
        $newBalance = $user->getBalance() + $amount;
        $user->setBalance($newBalance);
        return true;
    }
}

/**
 * Credit Card Payment Strategy - Kredi kartı ile ödeme (gelecek için)
 */
class CreditCardPaymentStrategy implements PaymentStrategy
{
    public function pay(User $user, float $amount): bool
    {
        // Kredi kartı ödeme işlemi simülasyonu
        // Gerçek uygulamada payment gateway entegrasyonu olur
        return true;
    }

    public function refund(User $user, float $amount): bool
    {
        // Kredi kartı iade işlemi
        return true;
    }
}

