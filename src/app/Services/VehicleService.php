<?php

namespace App\Services;

use App\Enum\DeductionTypeDriver;
use App\Enum\VehicleTypeEnum;
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

        // Handle is_car_rental checkbox logic
        // If checkbox is not checked, it won't be sent in the request
        // So we need to explicitly set it to false if not present
        if (!$request->has('is_car_rental')) {
            $data['is_car_rental'] = false;
        }

        // Handle customer data for car rental vehicles
        if ($request->has('is_car_rental') && $request->is_car_rental) {
            $customerData = $this->handleCustomerData($request);
            if ($customerData) {
                $data['customer_id'] = $customerData->id;
            } else {
                // If is_car_rental is true but no customer data provided, set customer_id to null
                $data['customer_id'] = null;
            }
        } else {
            // If is_car_rental is false, always set customer_id to null
            $data['customer_id'] = null;
        }

        // Handle documents array if it exists
        if ($request->has('documents')) {
            foreach ($request->documents as $index => $doc) {
                // Only process document_file if it's an actual uploaded file
                if (isset($doc['document_file']) && $request->hasFile('documents.' . $index . '.document_file')) {
                    $doc['document_file'] = ImageHelper::upload($doc['document_file']);
                }

                $documents[] = $doc;
            }
        }

        $vehicleData = $data;
        unset($vehicleData['documents']);
        unset($vehicleData['customer_name']);
        unset($vehicleData['customer_phone']);
        unset($vehicleData['customer_email']);
        unset($vehicleData['customer_address']);

        $vehicle = $this->vehicleRepository->create($vehicleData);

        foreach ($documents as $doc) {
            $vehicle->documents()->create($doc);
        }

        return $vehicle;
    }

    /**
     * Handle customer data for car rental vehicles
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\Customer|null
     */
    private function handleCustomerData(Request $request)
    {
        // If customer_id is provided, use existing customer
        if ($request->has('customer_id') && $request->customer_id) {
            return \App\Models\Customer::find($request->customer_id);
        }

        // If customer data is provided, create new customer
        if ($request->has('customer_name') && $request->customer_name) {
            $customerData = [
                'name' => $request->customer_name,
                'phone' => $request->customer_phone,
                'email' => $request->customer_email,
                'address' => $request->customer_address,
                'type' => \App\Models\Customer::TYPE_CARRENTAL,
                'is_active' => true,
                'created_by' => auth('admin')->id(),
            ];

            return \App\Models\Customer::create($customerData);
        }

        return null;
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

        // Handle is_car_rental checkbox logic
        // If checkbox is not checked, it won't be sent in the request
        // So we need to explicitly set it to false if not present
        if (!$request->has('is_car_rental')) {
            $data['is_car_rental'] = false;
        }

        // Handle customer data for car rental vehicles
        if ($request->has('is_car_rental') && $request->is_car_rental) {
            $customerData = $this->handleCustomerData($request);
            if ($customerData) {
                $data['customer_id'] = $customerData->id;
            } else {
                // If is_car_rental is true but no customer data provided, set customer_id to null
                $data['customer_id'] = null;
            }
        } else {
            // If is_car_rental is false, always set customer_id to null
            $data['customer_id'] = null;
        }

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
        unset($vehicleData['customer_name']);
        unset($vehicleData['customer_phone']);
        unset($vehicleData['customer_email']);
        unset($vehicleData['customer_address']);

        // Remove temp document fields if present
        if (isset($vehicleData['_documentFile0_temp'])) unset($vehicleData['_documentFile0_temp']);
        if (isset($vehicleData['_documentFile1_temp'])) unset($vehicleData['_documentFile1_temp']);

        $vehicle = $this->vehicleRepository->update($vehicle->vehicle_id, $vehicleData);

        foreach ($documents as $doc) {
            if (!empty($doc['document_id'])) {
                $this->vehicleDocumentRepository->update($doc['document_id'], $doc);
            } else {
                $vehicle->documents()->create($doc);
            }
        }

        return $vehicle;
    }

    function getDeductionsByVehicleType(VehicleTypeEnum $vehicleType): array
    {
        $containerDeductions = [
            DeductionTypeDriver::SUNDAY_ALLOWANCE,
            DeductionTypeDriver::MOOC_SHORT_RUN,
            DeductionTypeDriver::LO_ALLOWANCE,
            DeductionTypeDriver::TOLL_FEE,
            DeductionTypeDriver::OTHER_COST,
            DeductionTypeDriver::LOADING_BONUS,
            DeductionTypeDriver::EXTRA_TOLL,
            DeductionTypeDriver::ADVANCE_MONEY,
            DeductionTypeDriver::POLICE_FEE,
            DeductionTypeDriver::EARLY_NIGHT_EXTRA,
        ];

        $truckDeductions = [
            DeductionTypeDriver::ALLOWANCE_DRIVER_2,
            DeductionTypeDriver::ALLOWANCE_DRIVER_3,
            DeductionTypeDriver::SUNDAY_ALLOWANCE,
            DeductionTypeDriver::EARLY_NIGHT_ALLOWANCE,
            DeductionTypeDriver::LONG_TRIP_ALLOWANCE,
            DeductionTypeDriver::LOADER,
            DeductionTypeDriver::LO_ALLOWANCE,
            DeductionTypeDriver::DAY_MEAL_ALLOWANCE,
            DeductionTypeDriver::DINNER_ALLOWANCE,
            DeductionTypeDriver::TOLL_FEE,
            DeductionTypeDriver::ADVANCE_MONEY,
            DeductionTypeDriver::OTHER_COST,
        ];

        $allDeductions = DeductionTypeDriver::cases();

        return match ($vehicleType) {
            VehicleTypeEnum::BOX_TRUCK => $truckDeductions,
            VehicleTypeEnum::CONTAINER => $containerDeductions,
            default => $allDeductions,
        };
    }
}
