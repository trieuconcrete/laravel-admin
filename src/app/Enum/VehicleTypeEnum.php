<?php

namespace App\Enum;

enum VehicleTypeEnum: int
{
    case ALL = 0;
    case TRUCK = 1;
    case TRACTOR = 2;
    case BOX_TRUCK = 3;
    case CONTAINER = 4;
    case DUMP_TRUCK = 5;
    case TANKER = 6;
    case SPECIAL_PURPOSE = 7;
    case VAN = 8;
}
