<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Customer;

interface CustomerRepositoryInterface
{
    public function find(int $id): ?Customer;

    public function findByEmail(string $email): ?Customer;

    public function findByPhone(string $phone): ?Customer;

    public function findByEmailOrPhone(string $email, string $phone): ?Customer;

    public function create(array $data): Customer;

    public function update(Customer $customer, array $data): Customer;
}
