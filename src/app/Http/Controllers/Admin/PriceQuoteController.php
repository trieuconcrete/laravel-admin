<?php

namespace App\Http\Controllers\Admin;

use App\Models\Quote;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PriceQuoteController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.price_quotes.index');
    }

    public function create()
    {
        return view('admin.price_quotes.create');
    }

    public function store(Request $request)
    {
        try {
            return redirect()->route('admin.price_quotes.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function show(Quote $contract)
    {
        return view('admin.price_quotes.show');
    }

    public function edit(Quote $contract)
    {
        return view('admin.price_quotes.edit', compact('user'));
    }

    public function update(Request $request, Quote $contract)
    {
        try {
            return redirect()->route('admin.price_quotes.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy(Quote $contract)
    {
        return back()->with('success', 'User deleted successfully.');
    }
}
