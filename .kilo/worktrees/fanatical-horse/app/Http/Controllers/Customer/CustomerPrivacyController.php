<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;

class CustomerPrivacyController extends Controller
{
    public function index()
    {
        $privacy = PrivacyPolicy::where('is_active', true)->first();
        return view('customer.privacy', compact('privacy'));
    }
}