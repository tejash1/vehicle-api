<?php

declare(strict_types=1);

namespace App\Exception;

final class VehicleNotFoundException extends ResourceNotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("Vehicle with ID {$id} not found.");
    }
}
