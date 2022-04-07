<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\HttpFoundation\Cookie;

final class CookieRequestPayload
{
    public function __construct(public string $key, public string $value)
    {
    }

    public function toCookie(): Cookie
    {
        return new Cookie($this->key, $this->value);
    }
}
