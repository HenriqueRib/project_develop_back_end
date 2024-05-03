<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientController extends Controller
{
    public function home(Request $request)
    {
        try {
            return response()->json(['message' => 'Welcome']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $errorData], 500);
        }
    }
}