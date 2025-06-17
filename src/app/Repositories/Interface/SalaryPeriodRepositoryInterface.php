<?php

namespace App\Repositories\Interface;

use App\Models\SalaryPeriod;

interface SalaryPeriodRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Summary of findSalaryPeriodByCondition
     * @param array $params
     * @return \App\Models\SalaryPeriod|null
     */
    public function findSalaryPeriodByCondition(array $params = []): ?SalaryPeriod;
}
