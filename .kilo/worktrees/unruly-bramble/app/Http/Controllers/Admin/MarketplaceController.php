<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marketplace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MarketplaceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $marketplaces = Marketplace::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view(
            'admin.marketplaces.index',
            compact('marketplaces')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view(
            'admin.marketplaces.create'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request
    ) {

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'icon' => [
                'required',
                'string',
                'max:100',
            ],

            'url' => [
                'required',
                'url',
                'max:500',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

        ]);


        Marketplace::create([

            'name' =>
                $validated['name'],

            'slug' =>
                Str::slug(
                    $validated['name']
                ),

            'icon' =>
                $validated['icon'],

            'url' =>
                $validated['url'],

            'is_active' =>
                $request->boolean(
                    'is_active'
                ),

            'sort_order' =>
                $validated[
                    'sort_order'
                ] ?? 0,

        ]);


        return redirect()
            ->route(
                'admin.marketplaces.index'
            )
            ->with(
                'success',
                'Marketplace berhasil ditambahkan.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(
        Marketplace $marketplace
    ) {

        return view(
            'admin.marketplaces.edit',
            compact('marketplace')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Marketplace $marketplace
    ) {

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'icon' => [
                'required',
                'string',
                'max:100',
            ],

            'url' => [
                'required',
                'url',
                'max:500',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

        ]);


        $marketplace->update([

            'name' =>
                $validated['name'],

            'slug' =>
                Str::slug(
                    $validated['name']
                ),

            'icon' =>
                $validated['icon'],

            'url' =>
                $validated['url'],

            'is_active' =>
                $request->boolean(
                    'is_active'
                ),

            'sort_order' =>
                $validated[
                    'sort_order'
                ] ?? 0,

        ]);


        return redirect()
            ->route(
                'admin.marketplaces.index'
            )
            ->with(
                'success',
                'Marketplace berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Marketplace $marketplace
    ) {

        $marketplace->delete();


        return redirect()
            ->route(
                'admin.marketplaces.index'
            )
            ->with(
                'success',
                'Marketplace berhasil dihapus.'
            );
    }
}