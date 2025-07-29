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

            // Xử lý monthly_rental_fee để loại bỏ dấu phẩy
            if (isset($data['monthly_rental_fee']) && is_string($data['monthly_rental_fee'])) {
                $data['monthly_rental_fee'] = str_replace(',', '', $data['monthly_rental_fee']);
            }

            // Xử lý overtime_fee_per_hour để loại bỏ dấu phẩy
            if (isset($data['overtime_fee_per_hour']) && is_string($data['overtime_fee_per_hour'])) {
                $data['overtime_fee_per_hour'] = str_replace(',', '', $data['overtime_fee_per_hour']);
            }

            // Xử lý max_distance để loại bỏ dấu phẩy
            if (isset($data['max_distance']) && is_string($data['max_distance'])) {
                $data['max_distance'] = str_replace(',', '', $data['max_distance']);
            }

            // Xử lý over_distance_fee_per_km để loại bỏ dấu phẩy
            if (isset($data['over_distance_fee_per_km']) && is_string($data['over_distance_fee_per_km'])) {
                $data['over_distance_fee_per_km'] = str_replace(',', '', $data['over_distance_fee_per_km']);
            }

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
