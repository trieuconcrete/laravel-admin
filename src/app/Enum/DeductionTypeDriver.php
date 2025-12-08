<?php

namespace App\Enum;

enum DeductionTypeDriver: int
{
    case ALLOWANCE_DRIVER_2 = 9;
    case ALLOWANCE_DRIVER_3 = 10;
    case LOADER = 11;
    case TOLL_FEE = 12;
    case SUNDAY_ALLOWANCE = 13;
    case LONG_TRIP_ALLOWANCE = 14;
    case EARLY_NIGHT_ALLOWANCE = 15;
    case LO_ALLOWANCE = 16;
    case DINNER_ALLOWANCE = 17;
    case DAY_MEAL_ALLOWANCE = 18;
    case OTHER_COST = 19;
    case SELF_TOLL = 20;
    case EXTRA_TOLL = 21;
    case MOOC_SHORT_RUN = 22;
    case LOADING_BONUS = 23;
    case EARLY_NIGHT_EXTRA = 24;
    case ADVANCE_MONEY = 25;
    case POLICE_FEE = 26;
}
