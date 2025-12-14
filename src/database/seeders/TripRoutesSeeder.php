<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TripRoute;

class TripRoutesSeeder extends Seeder
{
    public function run()
    {
        // Skip truncation if called from a master data seeder
        if (!app()->has('seeder.skip_truncate')) {
            TripRoute::truncate();
        }

        // generate ~100 unique sample routes programmatically
        $origins = [
            'Hà Nội','Hồ Chí Minh','Đà Nẵng','Hải Phòng','Nam Định','Ninh Bình','Quảng Nam',
            'Bắc Ninh','Thanh Hóa','Vũng Tàu','Cần Thơ','Bình Dương','Hòa Bình','Thái Nguyên',
            'Phú Thọ','Bắc Giang','Đắk Lắk','Gia Lai','Kon Tum','Quảng Ninh'
        ];
        $destinations = [
            'Hải Phòng','Nam Định','Long An','Quảng Nam','Thừa Thiên Huế','Bình Thuận','Khánh Hòa',
            'Phú Yên','Bến Tre','Kiên Giang','An Giang','Sóc Trăng','Tiền Giang','Lâm Đồng',
            'Bình Phước','Nghệ An','Hà Tĩnh','Yên Bái','Lào Cai','Sơn La'
        ];

        $max = 100;
        $count = 0;
        $seen = [];

        // mix origins and destinations randomly to create unique pairs
        while ($count < $max) {
            $o = $origins[array_rand($origins)];
            $d = $destinations[array_rand($destinations)];
            if ($o === $d) continue;
            $key = $o . '|' . $d;
            if (isset($seen[$key])) continue;
            $seen[$key] = true;

            $tons = rand(1, 500); // random tons between 1 and 500
            // price roughly proportional to distance/tons; use random between 200k and 2M
            $price = rand(200000, 2000000);

            TripRoute::updateOrCreate([
                'origin_name' => $o,
                'destination_name' => $d,
            ], [
                'origin_name' => $o,
                'destination_name' => $d,
                'tons' => $tons,
                'price' => $price,
            ]);

            $count++;
        }
    }
}
