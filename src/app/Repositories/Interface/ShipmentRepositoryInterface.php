<?php

namespace App\Repositories\Interface;

interface ShipmentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Summary of getShipmentsWithFilters
     * @param mixed $filters
     * @param mixed $perPage
     * @return void
     */
    public function getShipmentsWithFilters($filters = [], $perPage = 20);
}
