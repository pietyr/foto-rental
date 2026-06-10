<?php

namespace App\Enums;

enum EquipmentStatus: string
{
    case Available = 'available';
    case Rented = 'rented';
    case Maintenance = 'maintenance';
}
