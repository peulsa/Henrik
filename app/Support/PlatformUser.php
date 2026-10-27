<?php

namespace App\Support;

final readonly class PlatformUser
{
    public function __construct(
        public string $email,
        public ?string $name = null,
        public ?string $externalId = null,
    ) {}
}
