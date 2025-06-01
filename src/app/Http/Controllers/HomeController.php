<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|min:2|max:100',
            'email' => 'required|email',
            'subject' => 'required|min:3|max:100',
            'message' => 'required|min:10|max:1000'
        ]);
        
        // Save to database or send email
        Contact::create($validated);
        
        return response()->json([
            'message' => $request->language == 'en' 
            ? 'Message sent successfully! I will get back to you soon.' 
            : 'Tin nhắn đã được gửi thành công! Tôi sẽ phản hồi sớm nhất.'
        ]);
    }
}
