<?php

namespace App\Services;

use App\Repositories\Interface\VehicleRepositoryInterface as VehicleRepository;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;

class VehicleService
{
    /**
     * Summary of __construct
     * @param \App\Repositories\Interface\VehicleRepositoryInterface $VehicleRepository
     */
    public function __construct(
        protected VehicleRepository $vehicleRepository,
    ) {}

    /**
     * Summary of store
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $documents = [];

        foreach ($request->documents as $doc) {
            $doc['document_file'] = ImageHelper::upload($doc['document_file']);
            $documents[] = $doc;
        }

        $vehicleData = $data;
        unset($vehicleData['documents']);

        $vehicle = $this->vehicleRepository->create($vehicleData);

        foreach ($documents as $doc) {
            $vehicle->documents()->create($doc);
        }

        return $vehicle;
    }

    /**
     * Summary of getFilteredVehicles
     * @param array $filters
     */
    public function getFilteredVehicles(array $filters)
    {
        return $this->vehicleRepository->getVehiclesWithFilters($filters);
    }
}
