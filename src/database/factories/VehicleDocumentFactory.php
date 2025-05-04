<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\VehicleDocument;
use App\Models\Vehicle;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleDocument>
 */
class VehicleDocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VehicleDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $documentTypes = array_keys(VehicleDocument::getDocumentTypes());
        $documentType = $this->faker->randomElement($documentTypes);
        
        // Generate issue date between 2 years ago and now
        $issueDate = $this->faker->dateTimeBetween('-2 years', 'now');
        
        // Generate expiry date based on document type
        $expiryDate = null;
        switch ($documentType) {
            case VehicleDocument::TYPE_REGISTRATION:
                $expiryDate = clone $issueDate;
                $expiryDate->modify('+10 years');
                break;
            case VehicleDocument::TYPE_INSPECTION:
                $expiryDate = clone $issueDate;
                $expiryDate->modify('+1 year');
                break;
            case VehicleDocument::TYPE_INSURANCE:
                $expiryDate = clone $issueDate;
                $expiryDate->modify('+1 year');
                break;
            case VehicleDocument::TYPE_PERMIT:
                $expiryDate = clone $issueDate;
                $expiryDate->modify('+2 years');
                break;
            case VehicleDocument::TYPE_ROAD_TAX:
                $expiryDate = clone $issueDate;
                $expiryDate->modify('+1 year');
                break;
            case VehicleDocument::TYPE_EMISSION:
                $expiryDate = clone $issueDate;
                $expiryDate->modify('+6 months');
                break;
            default:
                $expiryDate = clone $issueDate;
                $expiryDate->modify('+1 year');
                break;
        }

        // Generate document number based on type
        $documentNumber = match ($documentType) {
            VehicleDocument::TYPE_REGISTRATION => 'DK' . $this->faker->numerify('########'),
            VehicleDocument::TYPE_INSPECTION => 'DKM' . $this->faker->numerify('#######'),
            VehicleDocument::TYPE_INSURANCE => 'BH' . $this->faker->numerify('########'),
            VehicleDocument::TYPE_PERMIT => 'GPVT' . $this->faker->numerify('######'),
            VehicleDocument::TYPE_ROAD_TAX => 'PDB' . $this->faker->numerify('#######'),
            VehicleDocument::TYPE_EMISSION => 'KT' . $this->faker->numerify('########'),
            default => 'DOC' . $this->faker->numerify('########'),
        };

        // Determine status based on expiry date
        $status = VehicleDocument::STATUS_VALID;
        if ($expiryDate < now()) {
            $status = VehicleDocument::STATUS_EXPIRED;
        } elseif ($expiryDate < now()->addDays(30)) {
            $status = VehicleDocument::STATUS_EXPIRING_SOON;
        }

        return [
            'vehicle_id' => Vehicle::factory(),
            'document_type' => $documentType,
            'issue_date' => $issueDate,
            'expiry_date' => $expiryDate,
            'document_number' => $documentNumber,
            'document_file' => $this->faker->optional(0.7)->lexify('document_????.pdf'),
            'status' => $status
        ];
    }

    /**
     * Indicate that the document is valid.
     */
    public function valid(): static
    {
        return $this->state(function (array $attributes) {
            $issueDate = $this->faker->dateTimeBetween('-6 months', 'now');
            $expiryDate = clone $issueDate;
            $expiryDate->modify('+1 year');
            
            return [
                'issue_date' => $issueDate,
                'expiry_date' => $expiryDate,
                'status' => VehicleDocument::STATUS_VALID
            ];
        });
    }

    /**
     * Indicate that the document is expired.
     */
    public function expired(): static
    {
        return $this->state(function (array $attributes) {
            $issueDate = $this->faker->dateTimeBetween('-2 years', '-1 year');
            $expiryDate = clone $issueDate;
            $expiryDate->modify('+1 year');
            
            return [
                'issue_date' => $issueDate,
                'expiry_date' => $expiryDate,
                'status' => VehicleDocument::STATUS_EXPIRED
            ];
        });
    }

    /**
     * Indicate that the document is expiring soon.
     */
    public function expiringSoon(): static
    {
        return $this->state(function (array $attributes) {
            $issueDate = $this->faker->dateTimeBetween('-11 months', '-10 months');
            $expiryDate = clone $issueDate;
            $expiryDate->modify('+1 year');
            
            return [
                'issue_date' => $issueDate,
                'expiry_date' => $expiryDate,
                'status' => VehicleDocument::STATUS_EXPIRING_SOON
            ];
        });
    }

    /**
     * Indicate that the document is revoked.
     */
    public function revoked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VehicleDocument::STATUS_REVOKED
        ]);
    }
}
