<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('user_id');
            $table->string('district')->nullable()->after('city');
            $table->string('subdistrict')->nullable()->after('district');
            $table->index('customer_id');
        });

        $legacyAddresses = DB::table('customer_addresses')->get();

        foreach ($legacyAddresses as $legacyAddress) {
            DB::table('user_addresses')
                ->where('user_id', $legacyAddress->customer_id)
                ->update([
                    'customer_id' => $legacyAddress->customer_id,
                    'label' => $legacyAddress->label,
                    'recipient_name' => $legacyAddress->recipient_name,
                    'recipient_phone' => $legacyAddress->recipient_phone,
                    'address' => $legacyAddress->address,
                    'city' => $legacyAddress->city,
                    'district' => $legacyAddress->district ?? null,
                    'subdistrict' => $legacyAddress->subdistrict ?? null,
                    'province' => $legacyAddress->province,
                    'postal_code' => $legacyAddress->postal_code,
                    'is_default' => $legacyAddress->is_default,
                    'updated_at' => $legacyAddress->updated_at,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
            $table->dropColumn(['customer_id', 'district', 'subdistrict']);
        });
    }
};
