<?php

namespace App\Services;

use App\Repositories\Interface\VehicleRepositoryInterface as VehicleRepository;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;

class VehicleService
{
    /**
     * Summary of vehicleRepository
     * @var 
     */
    protected $vehicleRepository;

    /**
     * Summary of __construct
     * @param \App\Repositories\Interface\VehicleRepositoryInterface $vehicleRepository
     */
    public function __construct(VehicleRepository $vehicleRepository)
    {
        $this->vehicleRepository = $vehicleRepository;
    }

    /**
     * Summary of store
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        $documents = [];

        foreach ($request->documents ?? [] as $doc) {
            $data['document_file'] = ImageHelper::upload($request->file('document_file'));

            $documents[] = $doc;
        }

        return $this->vehicleRepository->create($data, $documents);
    }
}
