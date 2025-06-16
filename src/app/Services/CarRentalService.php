<?php

namespace App\Services;

use App\Models\CarRental;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\ImageHelper;
use App\Repositories\Interface\CarRentalRepositoryInterface;
use Illuminate\Http\Request;

class CarRentalService
{
    protected $carRentalRepository;

    /**
     * Summary of __construct
     */
    public function __construct(CarRentalRepositoryInterface $carRentalRepository)
    {
        $this->carRentalRepository = $carRentalRepository;
    }

    public function create(array $data): CarRental
    {
        return DB::transaction(function () use ($data) {
            $vehicleData = $data['vehicles'] ?? [];
            unset($data['vehicles']);

            $carRental = $this->carRentalRepository->create($data);

            if (!empty($vehicleData)) {
                $carRental->carRentalVehicles()->createMany($vehicleData);
            }

            return $carRental;
        });
    }

    public function update(int $id, array $data): ?CarRental
    {
        return DB::transaction(function () use ($id, $data) {
            $carRental = $this->carRentalRepository->find($id);

            if (!$carRental) {
                return null;
            }

            $vehicleData = $data['vehicles'] ?? null;
            unset($data['vehicles']);

            $carRental->update($data);

            if ($vehicleData !== null) {
                $vehicleIdsFromRequest = collect($vehicleData)->pluck('id')->filter()->toArray();

                $carRental->carRentalVehicles()->whereNotIn('id', $vehicleIdsFromRequest)->delete();

                foreach ($vehicleData as $vehicle) {
                    if (isset($vehicle['id'])) {
                        $carRental->carRentalVehicles()->where('id', $vehicle['id'])->update($vehicle);
                    } else {
                        $carRental->carRentalVehicles()->create($vehicle);
                    }
                }
            }

            return $carRental;
        });
    }
}
