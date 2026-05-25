<?php

namespace App\Models;

/**
 * User Model - Entity Class
 */
class User
{
    private ?string $id;
    private string $fullName;
    private string $email;
    private string $role;
    private string $password;
    private ?string $companyId;
    private float $balance;
    private string $createdAt;

    public function __construct(
        ?string $id = null,
        string $fullName = '',
        string $email = '',
        string $role = 'user',
        string $password = '',
        ?string $companyId = null,
        float $balance = 1000.0,
        string $createdAt = ''
    ) {
        $this->id = $id;
        $this->fullName = $fullName;
        $this->email = $email;
        $this->role = $role;
        $this->password = $password;
        $this->companyId = $companyId;
        $this->balance = $balance;
        $this->createdAt = $createdAt;
    }

    // Getters
    public function getId(): ?string { return $this->id; }
    public function getFullName(): string { return $this->fullName; }
    public function getEmail(): string { return $this->email; }
    public function getRole(): string { return $this->role; }
    public function getPassword(): string { return $this->password; }
    public function getCompanyId(): ?string { return $this->companyId; }
    public function getBalance(): float { return $this->balance; }
    public function getCreatedAt(): string { return $this->createdAt; }

    // Setters
    public function setId(string $id): void { $this->id = $id; }
    public function setFullName(string $fullName): void { $this->fullName = $fullName; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setRole(string $role): void { $this->role = $role; }
    public function setPassword(string $password): void { $this->password = $password; }
    public function setCompanyId(?string $companyId): void { $this->companyId = $companyId; }
    public function setBalance(float $balance): void { $this->balance = $balance; }
    public function setCreatedAt(string $createdAt): void { $this->createdAt = $createdAt; }

    /**
     * Array'e dönüştür (Database için)
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->fullName,
            'email' => $this->email,
            'role' => $this->role,
            'password' => $this->password,
            'company_id' => $this->companyId,
            'balance' => $this->balance,
            'created_at' => $this->createdAt
        ];
    }

    /**
     * Array'den oluştur (Database'den)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['full_name'] ?? '',
            $data['email'] ?? '',
            $data['role'] ?? 'user',
            $data['password'] ?? '',
            $data['company_id'] ?? null,
            $data['balance'] ?? 1000.0,
            $data['created_at'] ?? ''
        );
    }
}

