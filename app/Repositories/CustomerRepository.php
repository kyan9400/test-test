<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Interfaces\CustomerRepositoryInterface;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function __construct(
        private readonly Customer $model
    ) {}

    public function find(int $id): ?Customer
    {
        return $this->model->find($id);
    }

    public function findByEmail(string $email): ?Customer
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByPhone(string $phone): ?Customer
    {
        return $this->model->where('phone', $phone)->first();
    }

    public function findByEmailOrPhone(string $email, string $phone): ?Customer
    {
        return $this->model
            ->where('email', $email)
            ->orWhere('phone', $phone)
            ->first();
    }

    public function create(array $data): Customer
    {
        return $this->model->create($data);
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);

        return $customer->fresh();
    }
}

