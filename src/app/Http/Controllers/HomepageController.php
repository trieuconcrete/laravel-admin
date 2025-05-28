<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HomepageController extends Controller
{
    public function index()
    {
        $lang = app()->getLocale();
        return view('frontend.homepage', compact('lang'));
    }
}
