<?php

namespace App\Repositories\Eloquent;

use App\Models\DriverLicense;
use App\Repositories\Interface\DriverLicenseRepositoryInterface;

/**
 * Summary of DriverLicenseRepository
 */
class DriverLicenseRepository extends BaseRepository implements DriverLicenseRepositoryInterface
{
    /**
     * Summary of __construct
     * @param \App\Models\DriverLicense $model
     */
    public function __construct(DriverLicense $model)
    {
        parent::__construct($model);
    }

    /**
     * Summary of updateOrCreateByUser
     * @param int $userId
     * @param array $data
     * @return DriverLicense
     */
    public function updateOrCreateByUser(int $userId, array $data): DriverLicense
    {
        return DriverLicense::updateOrCreate(
            ['user_id' => $userId],
            $data
        );
    }
}
