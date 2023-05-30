<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'email' => 'masteradmin@debtstore.com',
            'password' => Hash::make('admin.1234..'),
            'is_admin' => true,
            'is_customer' => false,
            'is_merchant' => false
        ]);
    }
}