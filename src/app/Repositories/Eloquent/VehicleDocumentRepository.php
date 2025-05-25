<?php

namespace App\Repositories\Eloquent;

use App\Models\VehicleDocument;
use App\Repositories\Interface\VehicleDocumentRepositoryInterface;

class VehicleDocumentRepository extends BaseRepository implements VehicleDocumentRepositoryInterface
{
    /**
     * Summary of __construct
     * @param \App\Models\VehicleDocument $model
     */
    public function __construct(VehicleDocument $model)
    {
        parent::__construct($model);
    }
}
