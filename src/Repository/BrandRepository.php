<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Brand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Brand>
 */
class BrandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Brand::class);
    }

    /**
     * Returns all brands ordered alphabetically with their model count.
     *
     * @return Brand[]
     */
    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns brands that have at least one vehicle.
     *
     * @return Brand[]
     */
    public function findBrandsWithVehicles(): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.models', 'm')
            ->join('m.vehicles', 'v')
            ->groupBy('b.id')
            ->orderBy('b.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByName(string $name): ?Brand
    {
        return $this->findOneBy(['name' => $name]);
    }
}
