<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\VehicleModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VehicleModel>
 */
class VehicleModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VehicleModel::class);
    }

    /**
     * Returns all models for a given brand, ordered by name.
     *
     * @return VehicleModel[]
     */
    public function findByBrand(int $brandId): array
    {
        return $this->createQueryBuilder('m')
            ->join('m.brand', 'b')
            ->where('b.id = :brandId')
            ->setParameter('brandId', $brandId)
            ->orderBy('m.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns models with their brand eagerly loaded.
     *
     * @return VehicleModel[]
     */
    public function findAllWithBrand(): array
    {
        return $this->createQueryBuilder('m')
            ->addSelect('b')
            ->join('m.brand', 'b')
            ->orderBy('b.name', 'ASC')
            ->addOrderBy('m.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
