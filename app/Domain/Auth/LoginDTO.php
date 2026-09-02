<?php

declare(strict_types=1);

namespace App\Domain\Auth;

readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public bool $remember = false
    ) {}

    public static function fromArray(array $data): LoginDTO
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            remember: (bool) ($data['remember_me'] ?? $data['remember'] ?? false)
        );
    }
}
