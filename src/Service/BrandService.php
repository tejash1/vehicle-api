<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Request\BrandRequest;
use App\Entity\Brand;
use App\Exception\BrandNotFoundException;
use App\Exception\ConflictException;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;

final class BrandService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BrandRepository $brandRepository,
    ) {}

    /** @return Brand[] */
    public function findAll(): array
    {
        return $this->brandRepository->findAllOrderedByName();
    }

    public function findOrFail(int $id): Brand
    {
        $brand = $this->brandRepository->find($id);

        if ($brand === null) {
            throw new BrandNotFoundException($id);
        }

        return $brand;
    }

    public function create(BrandRequest $dto): Brand
    {
        if ($this->brandRepository->findOneByName($dto->name) !== null) {
            throw new ConflictException("Brand '{$dto->name}' already exists.");
        }

        $brand = (new Brand())
            ->setName($dto->name)
            ->setCountry($dto->country);

        $this->em->persist($brand);
        $this->em->flush();

        return $brand;
    }

    public function update(Brand $brand, BrandRequest $dto): Brand
    {
        if ($brand->getName() !== $dto->name
            && $this->brandRepository->findOneByName($dto->name) !== null
        ) {
            throw new ConflictException("Brand '{$dto->name}' already exists.");
        }

        $brand->setName($dto->name)->setCountry($dto->country);
        $this->em->flush();

        return $brand;
    }

    public function delete(Brand $brand): void
    {
        $this->em->remove($brand);
        $this->em->flush();
    }
}
