<?php

namespace App\Repositories\Eloquent;

use App\Models\Position;
use App\Repositories\Interface\PositionRepositoryInterface;

class PositionRepository extends BaseRepository implements PositionRepositoryInterface
{
    /**
     * Summary of __construct
     * @param \App\Models\Position $model
     */
    public function __construct(Position $model)
    {
        parent::__construct($model);
    }
}
