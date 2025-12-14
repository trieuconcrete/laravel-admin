<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TripRoute;

class TripRouteController extends Controller
{
    /**
     * Display a listing of trip routes
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $filters = $request->only(['keyword']);

        $query = TripRoute::query();

        // Filter by keyword (origin or destination)
        if (!empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('origin_name', 'like', '%' . $filters['keyword'] . '%')
                    ->orWhere('destination_name', 'like', '%' . $filters['keyword'] . '%');
            });
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Order by origin name
        $query->orderBy('origin_name');

        $tripRoutes = $query->paginate(15);

        return view('admin.trip-routes.index', compact('tripRoutes', 'filters'));
    }

    /**
     * Show the form for creating a new trip route
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.trip-routes.create');
    }

    /**
     * Store a newly created trip route
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'origin_name' => 'required|string|max:255',
            'destination_name' => 'required|string|max:255',
            'tons' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        $tripRoute = TripRoute::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm lộ trình thành công',
                'data' => $tripRoute,
            ]);
        }

        return redirect()->route('admin.trip-routes.index')
            ->with('success', 'Thêm lộ trình thành công');
    }

    /**
     * Return a trip route as JSON (used for AJAX edit)
     *
     * @param TripRoute $tripRoute
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(TripRoute $tripRoute)
    {
        return response()->json(['data' => $tripRoute]);
    }

    /**
     * Show the form for editing the specified trip route
     *
     * @param TripRoute $tripRoute
     * @return \Illuminate\View\View
     */
    public function edit(TripRoute $tripRoute)
    {
        return view('admin.trip-routes.edit', compact('tripRoute'));
    }

    /**
     * Update the specified trip route
     *
     * @param Request $request
     * @param TripRoute $tripRoute
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, TripRoute $tripRoute)
    {
        $validated = $request->validate([
            'origin_name' => 'required|string|max:255',
            'destination_name' => 'required|string|max:255',
            'tons' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        $tripRoute->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật lộ trình thành công',
                'data' => $tripRoute,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật lộ trình thành công',
        ]);
    }

    /**
     * Delete the specified trip route
     *
     * @param TripRoute $tripRoute
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(TripRoute $tripRoute)
    {
        $tripRoute->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa lộ trình thành công'
        ]);
    }
}
