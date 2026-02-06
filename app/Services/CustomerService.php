<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use App\Repositories\Interfaces\CustomerRepositoryInterface;

class CustomerService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository
    ) {}

    public function findOrCreate(array $data): Customer
    {
        $customer = $this->customerRepository->findByEmailOrPhone(
            $data['email'],
            $data['phone']
        );

        if ($customer) {
            return $this->customerRepository->update($customer, [
                'name' => $data['name'],
            ]);
        }

        return $this->customerRepository->create($data);
    }

    public function findByEmail(string $email): ?Customer
    {
        return $this->customerRepository->findByEmail($email);
    }

    public function findByPhone(string $phone): ?Customer
    {
        return $this->customerRepository->findByPhone($phone);
    }
}
