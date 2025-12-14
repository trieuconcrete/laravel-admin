<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $s = $this;

        return [
            'id' => $s->id,
            'shipment_code' => $s->shipment_code,
            'status' => $s->status,
            'status_label' => $s->status_label,
            'run_date' => optional($s->run_date)->format('Y-m-d'),
            'departure_time' => optional($s->departure_time)->toDateTimeString(),
            'estimated_arrival_time' => optional($s->estimated_arrival_time)->toDateTimeString(),

            // Điểm đi / đến
            'origin' => $s->origin,
            'origin2' => $s->origin2,
            'origin3' => $s->origin3,
            'destination' => $s->destination,
            'destination2' => $s->destination2,
            'destination3' => $s->destination3,

            // Địa chỉ chi tiết
            'address_origin' => $s->address_origin,
            'address_origin2' => $s->address_origin2,
            'address_origin3' => $s->address_origin3,
            'address_destination' => $s->address_destination,
            'address_destination2' => $s->address_destination2,
            'address_destination3' => $s->address_destination3,

            // Tên hàng hóa
            'product_name' => $s->product_name,
            'product_name2' => $s->product_name2,
            'product_name3' => $s->product_name3,

            // Trip-specific fields
            'trip_ton' => (float) $s->trip_ton,
            'trip_ton2' => (float) $s->trip_ton2,
            'trip_ton3' => (float) $s->trip_ton3,
            'trip_price' => (float) $s->trip_price,
            'trip_price2' => (float) $s->trip_price2,
            'trip_price3' => (float) $s->trip_price3,

            // Thông tin liên quan
            'vehicle' => $this->when($this->relationLoaded('vehicle') && $s->vehicle, [
                'id' => $s->vehicle->vehicle_id,
                'plate_number' => $s->vehicle->plate_number,
                'is_car_rental' => (bool) $s->vehicle->is_car_rental,
                'status' => $s->vehicle->status,
            ]),
            'customer' => $this->when($this->relationLoaded('customer') && $s->customer, [
                'id' => $s->customer->id,
                'name' => $s->customer->name,
            ]),
            'driver' => $this->when($this->relationLoaded('driver') && $s->driver, [
                'id' => $s->driver->id,
                'name' => $s->driver->full_name,
            ]),
            'co_driver' => $this->when($this->relationLoaded('coDriver') && $s->coDriver, [
                'id' => $s->coDriver->id ?? null,
                'name' => $s->coDriver->full_name ?? null,
            ]),

            // Số liệu giá trị theo yêu cầu
            'distance' => (float) $s->distance,
            'unit_price_for_driver' => (float) $s->unit_price_for_driver,
        ];
    }
}
