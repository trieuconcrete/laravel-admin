<?php

namespace App\Http\Controllers\Admin;

use App\Models\Quote;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Interface\CustomerRepositoryInterface as CustomerRepository;
use App\Http\Requests\Quote\UpdateQuoteRequest;
use App\Services\QuoteService;
use App\Http\Requests\Quote\StoreQuoteRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CarRentalController extends Controller
{
    /**
     * Summary of __construct
     * @param \App\Services\QuoteService $quoteService
     * @param \App\Repositories\Interface\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        protected QuoteService $quoteService,
        protected CustomerRepository $customerRepository
    ) {}

    /**
     * Summary of index
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $customers = $this->customerRepository->all()->pluck('name', 'id');
        $quoteStatuses = Quote::getStatuses();
        $quotes = $this->quoteService->getFilteredQuotes($request->all());

        return view('admin.car_rental.index', compact(
            'customers',
            'quoteStatuses',
            'quotes'
        ));
    }

    /**
     * Summary of create
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('admin.car_rental.create');
    }

    /**
     * Summary of store
     * @param \App\Http\Requests\Quote\StoreQuoteRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreQuoteRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->quoteService->store($request);

            DB::commit();
            return response()->json(['message' => 'Quote created successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vehicle creation failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $quote = Quote::with(['customer', 'attachments'])->findOrFail($id);
        $customers = $this->customerRepository->all()->pluck('name', 'id');
        $quoteStatuses = Quote::getStatuses();
    
        if (request()->ajax()) {
            return view('admin.car_rental.partials.detail', compact('quote', 'customers', 'quoteStatuses'))->render();
        }
        
        return abort(404);    }

    public function edit(Quote $contract)
    {
        return view('admin.car_rental.edit', compact('user'));
    }


    /**
     * Summary of update
     * @param \App\Http\Requests\Quote\UpdateQuoteRequest $request
     * @param \App\Models\Quote $quote
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateQuoteRequest $request, Quote $quote)
    {
        DB::beginTransaction();
        try {
            $this->quoteService->update($request, $quote);

            DB::commit();

            return response()->json(['message' => 'Quote update successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quote update failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Summary of destroy
     * @param \App\Models\Quote $quote
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Quote $quote)
    {
        DB::beginTransaction();
        try {
            $quote->histories()->delete();
            $quote->attachments()->delete();
            $quote->delete();
            DB::commit();
            return back()->with('success', 'Quote deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vehicle creation failed', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
