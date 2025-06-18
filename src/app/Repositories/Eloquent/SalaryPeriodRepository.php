<?php

namespace App\Repositories\Eloquent;

use App\Models\SalaryPeriod;
use App\Repositories\Interface\SalaryPeriodRepositoryInterface;

class SalaryPeriodRepository extends BaseRepository implements SalaryPeriodRepositoryInterface
{
    /**
     * Summary of __construct
     * @param \App\Models\SalaryPeriod $model
     */
    public function __construct(SalaryPeriod $model)
    {
        parent::__construct($model);
    }

    /**
     * Summary of findSalaryPeriodByCondition
     * @param array $params
     * @return \App\Models\SalaryPeriod|null
     */
    public function findSalaryPeriodByCondition(array $params = []): ?SalaryPeriod
    {
        return $this->model->where($params)->first();
    }
}
