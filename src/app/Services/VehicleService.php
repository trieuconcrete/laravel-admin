<?php

namespace App\Services;

use App\Repositories\Interface\VehicleRepositoryInterface as VehicleRepository;
use App\Repositories\Interface\VehicleDocumentRepositoryInterface as VehicleDocumentRepository;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;

class VehicleService
{
    /**
     * Summary of __construct
     * @param \App\Repositories\Interface\VehicleRepositoryInterface $vehicleRepository
     * @param \App\Repositories\Interface\VehicleDocumentRepositoryInterface $vehicleDocumentRepository
     */
    public function __construct(
        protected VehicleRepository $vehicleRepository,
        protected VehicleDocumentRepository $vehicleDocumentRepository,
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
            if (isset($doc['document_file'])) {
                $doc['document_file'] = ImageHelper::upload($doc['document_file']);
                $documents[] = $doc;
            }
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

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param mixed $vehicle
     */
    public function update(Request $request, $vehicle)
    {
        $data = $request->all();
        $documents = [];

        // Handle documents array if it exists
        if ($request->has('documents')) {
            foreach ($request->documents as $index => $doc) {
                // Make sure vehicle_id is set
                $doc['vehicle_id'] = $vehicle->vehicle_id;
                
                // Only process document_file if it's an actual uploaded file
                if (isset($doc['document_file']) && $request->hasFile('documents.' . $index . '.document_file')) {
                    $doc['document_file'] = ImageHelper::upload($doc['document_file']);
                }
                
                $documents[] = $doc;
            }
        }

        $vehicleData = $data;
        unset($vehicleData['documents']);
        
        // Remove temp document fields if present
        if (isset($vehicleData['_documentFile0_temp'])) unset($vehicleData['_documentFile0_temp']);
        if (isset($vehicleData['_documentFile1_temp'])) unset($vehicleData['_documentFile1_temp']);

        $vehicle = $this->vehicleRepository->update($vehicle->vehicle_id, $vehicleData);

        foreach ($documents as $doc) {
            if (!empty($doc['document_id'])) {
                $this->vehicleDocumentRepository->update($doc['document_id'], $doc);
            } else {
                $this->vehicleDocumentRepository->create($doc);
            }
        }

        return $vehicle;
    }
}
