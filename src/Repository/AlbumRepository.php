<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Album;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Album|null find($id, $lockMode = null, $lockVersion = null)
 * @method Album|null findOneBy(array $criteria, array $orderBy = null)
 * @method array<Album> findAll()
 * @method array<Album> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class AlbumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Album::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Album $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Album $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
