<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Request\VehicleCreateRequest;
use App\DTO\Request\VehicleFilterRequest;
use App\DTO\Request\VehicleUpdateRequest;
use App\Entity\Vehicle;
use App\Enum\VehicleStatus;
use App\Exception\VehicleNotFoundException;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;

final class VehicleService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly VehicleRepository $vehicleRepository,
        private readonly VehicleModelService $modelService,
    ) {}

    public function findOrFail(int $id): Vehicle
    {
        $vehicle = $this->vehicleRepository->find($id);

        if ($vehicle === null) {
            throw new VehicleNotFoundException($id);
        }

        return $vehicle;
    }

    public function findWithRelations(int $id): Vehicle
    {
        $vehicle = $this->vehicleRepository->findOneWithRelations($id);

        if ($vehicle === null) {
            throw new VehicleNotFoundException($id);
        }

        return $vehicle;
    }

    /**
     * @return array{items: Vehicle[], total: int, page: int, limit: int, pages: int}
     */
    public function findWithFilters(VehicleFilterRequest $dto): array
    {
        $status = $dto->status !== null ? VehicleStatus::from($dto->status) : null;

        return $this->vehicleRepository->findWithFilters(
            page:      $dto->page,
            limit:     $dto->limit,
            brandId:   $dto->brandId,
            modelId:   $dto->modelId,
            status:    $status,
            yearFrom:  $dto->yearFrom,
            yearTo:    $dto->yearTo,
            priceFrom: $dto->priceFrom,
            priceTo:   $dto->priceTo,
            sortBy:    $dto->sortBy,
            sortDir:   $dto->sortDir,
        );
    }

    /**
     * @return array{items: Vehicle[], total: int, page: int, limit: int, pages: int}
     */
    public function search(string $term, int $page, int $limit): array
    {
        return $this->vehicleRepository->search($term, $page, $limit);
    }

    public function create(VehicleCreateRequest $dto): Vehicle
    {
        $model = $this->modelService->findOrFail($dto->vehicleModelId);

        $vehicle = new Vehicle($dto->year);
        $vehicle->setVehicleModel($model);

        if ($dto->color !== null) {
            $vehicle->setColor($dto->color);
        }
        if ($dto->price !== null) {
            $vehicle->setPrice($dto->price);
        }
        if ($dto->mileage !== null) {
            $vehicle->setMileage($dto->mileage);
        }
        if ($dto->status !== null) {
            $vehicle->setStatus(VehicleStatus::from($dto->status));
        }
        if ($dto->vin !== null) {
            $vehicle->setVin($dto->vin);
        }
        if ($dto->description !== null) {
            $vehicle->setDescription($dto->description);
        }

        $this->em->persist($vehicle);
        $this->em->flush();

        return $vehicle;
    }

    public function update(Vehicle $vehicle, VehicleUpdateRequest $dto): Vehicle
    {
        if ($dto->vehicleModelId !== null) {
            $vehicle->setVehicleModel($this->modelService->findOrFail($dto->vehicleModelId));
        }
        if ($dto->year !== null) {
            $vehicle->setYear($dto->year);
        }
        if ($dto->color !== null) {
            $vehicle->setColor($dto->color);
        }
        if ($dto->price !== null) {
            $vehicle->setPrice($dto->price);
        }
        if ($dto->mileage !== null) {
            $vehicle->setMileage($dto->mileage);
        }
        if ($dto->status !== null) {
            $vehicle->setStatus(VehicleStatus::from($dto->status));
        }
        if ($dto->vin !== null) {
            $vehicle->setVin($dto->vin);
        }
        if ($dto->description !== null) {
            $vehicle->setDescription($dto->description);
        }

        $this->em->flush();

        return $vehicle;
    }

    public function delete(Vehicle $vehicle): void
    {
        $this->em->remove($vehicle);
        $this->em->flush();
    }
}
