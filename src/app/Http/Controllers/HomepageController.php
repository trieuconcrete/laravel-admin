<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;

class HomepageController extends Controller
{
    public function index(Request $request)
    {
        return view('homepage2');
    }

    public function index1(Request $request)
    {
        return view('homepage1');
    }

    public function index2(Request $request)
    {
        return view('homepage');
    }
}
