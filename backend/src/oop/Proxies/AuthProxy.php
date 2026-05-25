<?php

namespace App\Proxies;

use App\Repositories\UserRepository;
use App\Models\User;

/**
 * Proxy Pattern - Authentication Proxy
 * Kullanıcı işlemlerini kontrol eder ve yetkilendirme yapar
 */
class AuthProxy
{
    private UserRepository $userRepository;
    private ?User $currentUser = null;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Kullanıcı girişi
     */
    public function login(string $email, string $password): bool
    {
        $user = $this->userRepository->findByEmail($email);
        
        if ($user === null) {
            return false;
        }

        if (!password_verify($password, $user->getPassword())) {
            return false;
        }

        $this->currentUser = $user;
        $this->startSession($user);
        return true;
    }

    /**
     * Kullanıcı çıkışı
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        $this->currentUser = null;
    }

    /**
     * Mevcut kullanıcıyı getir
     */
    public function getCurrentUser(): ?User
    {
        if ($this->currentUser === null) {
            $this->loadUserFromSession();
        }
        return $this->currentUser;
    }

    /**
     * Rol kontrolü
     */
    public function hasRole(string $role): bool
    {
        $user = $this->getCurrentUser();
        return $user !== null && $user->getRole() === $role;
    }

    /**
     * Rollerden birine sahip mi?
     */
    public function hasAnyRole(array $roles): bool
    {
        $user = $this->getCurrentUser();
        if ($user === null) {
            return false;
        }
        return in_array($user->getRole(), $roles);
    }

    /**
     * Session başlat
     */
    private function startSession(User $user): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['full_name'] = $user->getFullName();
        $_SESSION['role'] = $user->getRole();
        $_SESSION['company_id'] = $user->getCompanyId();
        $_SESSION['balance'] = $user->getBalance();
    }

    /**
     * Session'dan kullanıcı yükle
     */
    private function loadUserFromSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            $this->currentUser = $this->userRepository->findById($_SESSION['user_id']);
        }
    }
}

