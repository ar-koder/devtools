<?php

declare(strict_types=1);

namespace App\Dto;

use Stringable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

class Bin implements Stringable
{
    public function __construct(private string $id)
    {
        if (!Uuid::isValid($id) || !(Uuid::fromString($id) instanceof UuidV4)) {
            throw new NotFoundHttpException(sprintf('"%s" is an invalid bin', $id));
        }
    }

    public function __toString(): string
    {
        return $this->getId();
    }

    public function getId(): string
    {
        return $this->id;
    }
}
