<?php

namespace App\Repositories;

/**
 * Repository Interface - Abstract Factory Pattern için
 */
interface RepositoryInterface
{
    public function findById(string $id): ?object;
    public function findAll(): array;
    public function save(object $entity): bool;
    public function delete(string $id): bool;
    public function update(object $entity): bool;
}

