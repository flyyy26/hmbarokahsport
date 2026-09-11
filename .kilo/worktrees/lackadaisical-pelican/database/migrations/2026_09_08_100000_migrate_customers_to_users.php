<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 🔥 AMBIL SEMUA CUSTOMER DARI TABEL CUSTOMERS
        $customers = DB::table('customers')->get();
        
        foreach ($customers as $customer) {
            // 🔥 CEK APAKAH USER SUDAH ADA DI TABEL USERS
            $existingUser = DB::table('users')
                ->where('email', $customer->email)
                ->orWhere('phone', $customer->phone)
                ->first();
            
            if ($existingUser) {
                // 🔥 JIKA SUDAH ADA, GUNAKAN ID YANG SUDAH ADA
                $newUserId = $existingUser->id;
            } else {
                // 🔥 JIKA BELUM ADA, BUAT USER BARU
                $newUserId = DB::table('users')->insertGetId([
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'password' => $customer->password,
                    'avatar' => $customer->avatar,
                    'is_active' => $customer->is_active,
                    'role' => 'customer',
                    'email_verified_at' => $customer->email_verified_at,
                    'remember_token' => $customer->remember_token,
                    'created_at' => $customer->created_at,
                    'updated_at' => $customer->updated_at,
                ]);
            }
            
            // 🔥 UPDATE customer_addresses KE user_addresses
            $addresses = DB::table('customer_addresses')
                ->where('customer_id', $customer->id)
                ->get();
            
            foreach ($addresses as $address) {
                // CEK APAKAH ALAMAT SUDAH ADA DI user_addresses
                $existingAddress = DB::table('user_addresses')
                    ->where('user_id', $newUserId)
                    ->where('address', $address->address)
                    ->where('recipient_name', $address->recipient_name)
                    ->first();
                
                if (!$existingAddress) {
                    DB::table('user_addresses')->insert([
                        'user_id' => $newUserId,
                        'label' => $address->label,
                        'recipient_name' => $address->recipient_name,
                        'recipient_phone' => $address->recipient_phone,
                        'address' => $address->address,
                        'city' => $address->city,
                        'district' => $address->district ?? null,
                        'subdistrict' => $address->subdistrict ?? null,
                        'province' => $address->province,
                        'postal_code' => $address->postal_code,
                        'is_default' => $address->is_default,
                        'created_at' => $address->created_at,
                        'updated_at' => $address->updated_at,
                    ]);
                }
            }
        }
        
        // 🔥 HAPUS TABEL YANG TIDAK DIPERLUKAN LAGI
        Schema::dropIfExists('customer_addresses');
        Schema::dropIfExists('customers');
    }

    public function down(): void
    {
        // 🔥 REVERSE: BUAT KEMBALI TABEL CUSTOMERS DAN CUSTOMER_ADDRESSES
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('label')->nullable();
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->text('address');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index('customer_id');
        });
    }
};
