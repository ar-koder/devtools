<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Album;
use Doctrine\Persistence\ManagerRegistry;

final class AlbumRepository extends BaseEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Album::class);
    }
}
