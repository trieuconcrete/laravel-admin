<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalaryDetail;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.salary.index');
    }

    public function create()
    {
        return view('admin.salary.create');
    }

    public function store(Request $request)
    {
        try {
            return redirect()->route('admin.salary.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function show(SalaryDetail $SalaryDetail)
    {
        return view('admin.salary.show');
    }

    public function edit(SalaryDetail $SalaryDetail)
    {
        return view('admin.salary.edit', compact('user'));
    }

    public function update(Request $request, SalaryDetail $SalaryDetail)
    {
        try {
            return redirect()->route('admin.salary.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy(SalaryDetail $SalaryDetail)
    {
        return back()->with('success', 'User deleted successfully.');
    }
}
