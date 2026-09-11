<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use Illuminate\Http\Request;

class CustomerAboutController extends Controller
{
    public function index()
    {
        $about = AboutUs::where('is_active', true)->first();
        
        if (!$about) {
            $about = (object) [
                'title' => 'Tentang Kami',
                'content' => 'Kami adalah toko yang berdedikasi untuk memberikan produk berkualitas terbaik.',
                'vision' => null,
                'mission' => null,
            ];
        }

        return view('customer.about', compact('about'));
    }
}