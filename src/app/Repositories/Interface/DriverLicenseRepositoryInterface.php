<?php

namespace App\Repositories\Interface;

use App\Models\DriverLicense;

/**
 * Summary of DriverLicenseRepositoryInterface
 */
interface DriverLicenseRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Summary of updateOrCreateByUser
     * @param int $userId
     * @param array $data
     * @return DriverLicense
     */
    public function updateOrCreateByUser(int $userId, array $data): DriverLicense;
}
