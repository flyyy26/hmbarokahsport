<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerTermController extends Controller
{
    public function index()
    {
        $term = Term::active()->latest()->first();
        return view('customer.terms', compact('term'));
    }
}