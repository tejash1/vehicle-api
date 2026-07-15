<?php

declare(strict_types=1);

namespace App\Exception;

final class BrandNotFoundException extends ResourceNotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("Brand with ID {$id} not found.");
    }
}
