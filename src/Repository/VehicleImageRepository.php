<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\VehicleImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VehicleImage>
 */
class VehicleImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VehicleImage::class);
    }

    /**
     * Returns all images for a vehicle ordered by sort_order then id.
     *
     * @return VehicleImage[]
     */
    public function findByVehicle(int $vehicleId): array
    {
        return $this->createQueryBuilder('i')
            ->where('IDENTITY(i.vehicle) = :vehicleId')
            ->setParameter('vehicleId', $vehicleId)
            ->orderBy('i.sortOrder', 'ASC')
            ->addOrderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Clears the is_primary flag on all images for a vehicle before setting a new one.
     * Call this before marking an image as primary.
     */
    public function clearPrimaryFlag(int $vehicleId): void
    {
        $this->createQueryBuilder('i')
            ->update()
            ->set('i.isPrimary', ':false')
            ->where('IDENTITY(i.vehicle) = :vehicleId')
            ->setParameter('false', false)
            ->setParameter('vehicleId', $vehicleId)
            ->getQuery()
            ->execute();
    }
}
