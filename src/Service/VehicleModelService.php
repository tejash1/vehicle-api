<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Request\VehicleModelRequest;
use App\Entity\Brand;
use App\Entity\VehicleModel;
use App\Exception\ConflictException;
use App\Exception\VehicleModelNotFoundException;
use App\Repository\VehicleModelRepository;
use Doctrine\ORM\EntityManagerInterface;

final class VehicleModelService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly VehicleModelRepository $modelRepository,
        private readonly BrandService $brandService,
    ) {}

    /** @return VehicleModel[] */
    public function findAll(): array
    {
        return $this->modelRepository->findAllWithBrand();
    }

    /** @return VehicleModel[] */
    public function findByBrand(Brand $brand): array
    {
        return $this->modelRepository->findByBrand($brand->getId() ?? 0);
    }

    public function findOrFail(int $id): VehicleModel
    {
        $model = $this->modelRepository->find($id);

        if ($model === null) {
            throw new VehicleModelNotFoundException($id);
        }

        return $model;
    }

    public function create(VehicleModelRequest $dto): VehicleModel
    {
        $brand = $this->brandService->findOrFail($dto->brandId);

        $this->guardDuplicateName($brand, $dto->name);

        $model = (new VehicleModel())
            ->setBrand($brand)
            ->setName($dto->name);

        $this->em->persist($model);
        $this->em->flush();

        return $model;
    }

    public function update(VehicleModel $model, VehicleModelRequest $dto): VehicleModel
    {
        $brand = $this->brandService->findOrFail($dto->brandId);

        if ($model->getName() !== $dto->name || $model->getBrand()?->getId() !== $brand->getId()) {
            $this->guardDuplicateName($brand, $dto->name, excludeId: $model->getId());
        }

        $model->setBrand($brand)->setName($dto->name);
        $this->em->flush();

        return $model;
    }

    public function delete(VehicleModel $model): void
    {
        $this->em->remove($model);
        $this->em->flush();
    }

    private function guardDuplicateName(Brand $brand, string $name, ?int $excludeId = null): void
    {
        $existing = $this->modelRepository->findOneBy([
            'brand' => $brand,
            'name'  => $name,
        ]);

        if ($existing !== null && $existing->getId() !== $excludeId) {
            throw new ConflictException(
                "Model '{$name}' already exists under brand '{$brand->getName()}'."
            );
        }
    }
}
