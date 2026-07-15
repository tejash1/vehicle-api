<?php

declare(strict_types=1);

namespace App\Enum;

enum VehicleStatus: string
{
    case Available = 'available';
    case Sold      = 'sold';
    case Reserved  = 'reserved';

    public function label(): string
    {
        return match($this) {
            self::Available => 'Available',
            self::Sold      => 'Sold',
            self::Reserved  => 'Reserved',
        };
    }
}
