<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShipmentDetailResource;
use App\Http\Resources\ShipmentListItemResource;
use App\Models\Shipment;
use App\Services\DriverShipmentService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class DriverShipmentController extends Controller
{
    public function __construct(protected DriverShipmentService $shipmentService) {}

    public function index(Request $request)
    {
        $validated = $request->validate([
            'date' => [Rule::in(['today', 'tomorrow', 'yesterday'])],
            'from' => ['date'],
            'to' => ['date', 'after_or_equal:from'],
            'status' => ['string'],
            'type' => ['integer', 'in:1,2,3,4'],
            'q' => ['string', 'max:255'],
            'sort' => [Rule::in(['departure_time', 'run_date', 'created_at', 'status'])],
            'dir' => [Rule::in(['asc', 'desc'])],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ]);

        [$paginator, $summary] = $this->shipmentService->getShipments($request);

        return response()->json([
            'data' => ShipmentListItemResource::collection($paginator->items()),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'has_more' => $paginator->hasMorePages(),
            ],
            'summary' => $summary,
        ]);
    }

    public function show(Request $request, Shipment $shipment)
    {
        $user = $request->user();

        [$shipmentData, $error] = $this->shipmentService->getShipmentDetails($shipment, $user->id);

        if ($error) {
            return response()->json([
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => $error
                ]
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'shipment' => new ShipmentDetailResource($shipmentData),
        ]);
    }
}
