<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        
        // Ambil FAQ yang aktif dan diurutkan, dengan relasi category
        $faqs = Faq::with('category')
            ->active()
            ->ordered()
            ->get();
        
        // Ambil kategori dari database
        $categories = FaqCategory::active()
            ->ordered()
            ->get();
        
        return view('customer.contact.index', compact('setting', 'faqs', 'categories'));
    }
}