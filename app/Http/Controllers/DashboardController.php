<?php

namespace App\Http\Controllers;

use App\Models\LampActivity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Nantinya di sini kita akan ambil data asli dari database
        // $activities = LampActivity::latest()->take(10)->get();
        
        return view('dashboard');
    }
}
