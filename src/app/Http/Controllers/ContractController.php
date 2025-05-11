<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.contracts.index');
    }

    public function create()
    {
        return view('admin.contracts.create');
    }

    public function store(Request $request)
    {
        try {
            return redirect()->route('admin.contracts.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function show(Contract $contract)
    {
        return view('admin.contracts.show');
    }

    public function edit(Contract $contract)
    {
        return view('admin.contracts.edit', compact('user'));
    }

    public function update(Request $request, Contract $contract)
    {
        try {
            return redirect()->route('admin.contracts.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy(Contract $contract)
    {
        return back()->with('success', 'User deleted successfully.');
    }
}
