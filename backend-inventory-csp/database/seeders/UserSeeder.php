<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            // 1. Admin
            [
                'name' => 'Admin Lab',
                'email' => 'admin@lab.inforpcu.petra.ac.id',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 2. Asisten Laboratorium
            [
                'name' => 'Joko Santoso',
                'email' => 'asisten.joko@lab.inforpcu.petra.ac.id',
                'password' => Hash::make('joko123'),
                'role' => 'asisten',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 3. Mahasiswa
            [
                'name' => 'Fernando Hose',
                'email' => 'c14220151@john.petra.ac.id',
                'password' => Hash::make('hose123'),
                'role' => 'mahasiswa',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 4. Mahasiswa
            [
                'name' => 'Steven Oentoro',
                'email' => 'c14220127@john.petra.ac.id',
                'password' => Hash::make('oentoro123'),
                'role' => 'mahasiswa',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}