<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\Interface\CustomerRepositoryInterface;
use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CustomerService
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * CustomerService constructor.
     *
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Summary of getFilteredCustomer
     * @param array $filters
     */
    public function getFilteredCustomer(array $filters)
    {
        return $this->customerRepository->getCustomerWithFilters($filters);
    }
    
    /**
     * Get monthly shipments for a customer
     * @param int $customerId
     * @param string $month Format: 'YYYY-MM'
     * @return \Illuminate\Support\Collection
     */
    public function getMonthlyShipments(int $customerId, string $month)
    {
        $shipments = $this->customerRepository->getMonthlyShipments($customerId, $month);
        
        return $shipments->map(function ($shipment) {
            // Calculate total deductions
            $combinedFees = $shipment->shipmentDeductions->sum('amount');
            
            // Calculate total amount
            $totalAmount = (($shipment->trip_count ?? 1) * $shipment->unit_price) - $combinedFees;
            
            // Format the departure time using application's standardized date formatting
            $departureDate = $shipment->departure_time ? DateHelper::format($shipment->departure_time) : '';
            
            return [
                'id' => $shipment->id,
                'shipment_code' => $shipment->shipment_code,
                'departure_time' => $departureDate,
                'origin' => $shipment->origin,
                'destination' => $shipment->destination,
                'trip_count' => $shipment->trip_count ?? 1,
                'cargo_weight' => $shipment->cargo_weight ?? 0,
                'unit_price' => $shipment->unit_price,
                'combined_fees' => $combinedFees,
                'total_amount' => $totalAmount,
                'notes' => $shipment->notes
            ];
        });
    }

    /**
     * Summary of store
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $data['is_active'] = $data['is_active'] == Customer::STATUS_ACTIVE ? true : false;
        
        // Handle document file upload
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $path = $file->store('customer-documents', 'public');
            $data['document_file'] = $path;
            
            // Store the path in session in case validation fails
            session(['_documentFile_temp' => $path]);
        } elseif ($request->has('_documentFile_temp')) {
            // Use the temporary file from a previous validation failure
            $data['document_file'] = $request->input('_documentFile_temp');
        }

        $customer = $this->customerRepository->create($data);
        
        // Clear the temporary file session after successful creation
        if (session()->has('_documentFile_temp')) {
            session()->forget('_documentFile_temp');
        }
        
        return $customer;
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Customer $customer
     * @return void
     */
    public function update(Request $request, Customer $customer)
    {
        $data = $request->all();
        
        // Handle document file upload
        if ($request->hasFile('document_file')) {
            // Delete old file if exists
            if ($customer->document_file) {
                Storage::disk('public')->delete($customer->document_file);
            }
            
            $file = $request->file('document_file');
            $path = $file->store('customer-documents', 'public');
            $data['document_file'] = $path;
            
            // Store the path in session in case validation fails
            session(['_documentFile_temp' => $path]);
        } elseif ($request->has('_documentFile_temp')) {
            // Use the temporary file from a previous validation failure
            $data['document_file'] = $request->input('_documentFile_temp');
        }
        
        $result = $this->customerRepository->update($customer->id, $data);
        
        // Clear the temporary file session after successful update
        if (session()->has('_documentFile_temp')) {
            session()->forget('_documentFile_temp');
        }
        
        return $result;
    }
}
