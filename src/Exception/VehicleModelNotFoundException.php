<?php

declare(strict_types=1);

namespace App\Exception;

final class VehicleModelNotFoundException extends ResourceNotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("Vehicle model with ID {$id} not found.");
    }
}
