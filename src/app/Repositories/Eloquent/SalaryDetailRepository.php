<?php

namespace App\Repositories\Eloquent;

use App\Models\SalaryDetail;
use App\Repositories\Interface\SalaryDetailRepositoryInterface;

class SalaryDetailRepository extends BaseRepository implements SalaryDetailRepositoryInterface
{
    /**
     * Summary of __construct
     * @param \App\Models\SalaryDetail $model
     */
    public function __construct(SalaryDetail $model)
    {
        parent::__construct($model);
    }

}
