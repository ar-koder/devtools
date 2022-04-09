<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Todo;
use Doctrine\Persistence\ManagerRegistry;

final class TodoRepository extends BaseEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Todo::class);
    }
}
