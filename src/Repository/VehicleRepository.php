<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Vehicle;
use App\Enum\VehicleStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicle>
 */
class VehicleRepository extends ServiceEntityRepository
{
    private const SORTABLE_COLUMNS = ['year', 'price', 'mileage', 'createdAt'];
    private const DEFAULT_SORT     = 'createdAt';
    private const DEFAULT_DIR      = 'DESC';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    /**
     * Paginated, filtered, sorted vehicle list.
     *
     * Returns:
     *   [
     *     'items'  => Vehicle[],
     *     'total'  => int,
     *     'page'   => int,
     *     'limit'  => int,
     *     'pages'  => int,
     *   ]
     *
     * @return array{items: Vehicle[], total: int, page: int, limit: int, pages: int}
     */
    public function findWithFilters(
        int            $page      = 1,
        int            $limit     = 20,
        ?int           $brandId   = null,
        ?int           $modelId   = null,
        ?VehicleStatus $status    = null,
        ?int           $yearFrom  = null,
        ?int           $yearTo    = null,
        ?string        $priceFrom = null,
        ?string        $priceTo   = null,
        string         $sortBy    = self::DEFAULT_SORT,
        string         $sortDir   = self::DEFAULT_DIR,
    ): array {
        $qb = $this->baseQueryBuilder();

        $this->applyFilters($qb, $brandId, $modelId, $status, $yearFrom, $yearTo, $priceFrom, $priceTo);
        $this->applySort($qb, $sortBy, $sortDir);

        return $this->paginate($qb, $page, $limit);
    }

    /**
     * Full-text style search across VIN, description, brand and model name, color.
     *
     * @return array{items: Vehicle[], total: int, page: int, limit: int, pages: int}
     */
    public function search(string $term, int $page = 1, int $limit = 20): array
    {
        $like = '%' . $term . '%';

        $qb = $this->baseQueryBuilder()
            ->andWhere(
                $this->getEntityManager()->getExpressionBuilder()->orX(
                    'v.vin LIKE :term',
                    'v.color LIKE :term',
                    'v.description LIKE :term',
                    'm.name LIKE :term',
                    'b.name LIKE :term',
                )
            )
            ->setParameter('term', $like)
            ->orderBy('v.createdAt', 'DESC');

        return $this->paginate($qb, $page, $limit);
    }

    /**
     * Single vehicle with all relations loaded in one query.
     */
    public function findOneWithRelations(int $id): ?Vehicle
    {
        return $this->baseQueryBuilder()
            ->addSelect('i')
            ->leftJoin('v.images', 'i')
            ->andWhere('v.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    private function baseQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('v')
            ->addSelect('m', 'b')
            ->join('v.vehicleModel', 'm')
            ->join('m.brand', 'b');
    }

    private function applyFilters(
        QueryBuilder   $qb,
        ?int           $brandId,
        ?int           $modelId,
        ?VehicleStatus $status,
        ?int           $yearFrom,
        ?int           $yearTo,
        ?string        $priceFrom,
        ?string        $priceTo,
    ): void {
        if ($brandId !== null) {
            $qb->andWhere('b.id = :brandId')->setParameter('brandId', $brandId);
        }

        if ($modelId !== null) {
            $qb->andWhere('m.id = :modelId')->setParameter('modelId', $modelId);
        }

        if ($status !== null) {
            $qb->andWhere('v.status = :status')->setParameter('status', $status);
        }

        if ($yearFrom !== null) {
            $qb->andWhere('v.year >= :yearFrom')->setParameter('yearFrom', $yearFrom);
        }

        if ($yearTo !== null) {
            $qb->andWhere('v.year <= :yearTo')->setParameter('yearTo', $yearTo);
        }

        if ($priceFrom !== null) {
            $qb->andWhere('v.price >= :priceFrom')->setParameter('priceFrom', $priceFrom);
        }

        if ($priceTo !== null) {
            $qb->andWhere('v.price <= :priceTo')->setParameter('priceTo', $priceTo);
        }
    }

    private function applySort(QueryBuilder $qb, string $sortBy, string $sortDir): void
    {
        if (!in_array($sortBy, self::SORTABLE_COLUMNS, strict: true)) {
            $sortBy = self::DEFAULT_SORT;
        }

        $sortDir = strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';

        $qb->orderBy('v.' . $sortBy, $sortDir);
    }

    /**
     * @return array{items: Vehicle[], total: int, page: int, limit: int, pages: int}
     */
    private function paginate(QueryBuilder $qb, int $page, int $limit): array
    {
        $page  = max(1, $page);
        $limit = min(max(1, $limit), 100);

        $total = (int) (clone $qb)
            ->select('COUNT(DISTINCT v.id)')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleScalarResult();

        $items = $qb
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return [
            'items' => $items,
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'pages' => $total > 0 ? (int) ceil($total / $limit) : 1,
        ];
    }
}
