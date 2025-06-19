<?php

namespace App\Services;

use App\Models\CarRental;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Helpers\ImageHelper;
use App\Repositories\Interface\CarRentalRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

            if (isset($data['file']) && $data['file'] instanceof \Illuminate\Http\UploadedFile) {
                $result = FileUploadService::upload(
                    $data['file'],
                    'car_rentals',
                    'original'
                );
                $data['file'] = $result["file_name"];
            }

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

            if (isset($data['file']) && $data['file'] instanceof \Illuminate\Http\UploadedFile) {
                $filePath = "uploads/car_rentals/" . $carRental->file;

                if (Storage::disk('public')->exists($filePath)) {
                    FileUploadService::delete($filePath);
                }

                $uploadResult = FileUploadService::upload(
                    $data['file'],
                    'car_rentals',
                    'original'
                );

                $data['file'] = $uploadResult['file_name'];
            }

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
