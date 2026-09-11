<?php

namespace App\Http\ViewComposers;

use App\Models\Marketplace;
use App\Models\Setting;
use Illuminate\View\View;

class MarketplaceComposer
{
    public function compose(View $view)
    {
        $marketplaces = Marketplace::active()->get();
        $setting = Setting::first();
        
        $view->with([
            'marketplaces' => $marketplaces,
            'setting' => $setting,
        ]);
    }
}