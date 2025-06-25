<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('loans')->insert([
            [
                'item_id' => 5,
                'user_id' => 3,
                'loan_date' => Carbon::now()->subDays(10),
                'due_date' => Carbon::now()->subDays(3),
                'return_date' => Carbon::now()->subDays(4),
                'status' => 'Dikembalikan',
                'quantity' => 1,
                'purpose' => 'Latihan modul praktikum',
                'is_extended' => false,
                'extension_requested' => false,
                'extension_approved' => null,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(4),
            ],
            [
                'item_id' => 10,
                'user_id' => 4,
                'loan_date' => Carbon::now()->subDays(2),
                'due_date' => Carbon::now()->addDays(5),
                'return_date' => null,
                'status' => 'Menunggu Konfirmasi',
                'quantity' => 2,
                'purpose' => 'Presentasi tugas akhir',
                'is_extended' => false,
                'extension_requested' => false,
                'extension_approved' => null,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'item_id' => 8,
                'user_id' => 3,
                'loan_date' => Carbon::now()->subDays(15),
                'due_date' => Carbon::now()->subDays(8),
                'return_date' => null,
                'status' => 'Terlambat',
                'quantity' => 1,
                'purpose' => 'Perakitan kabel jaringan',
                'is_extended' => false,
                'extension_requested' => true,
                'extension_approved' => false, // ditolak oleh admin
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            [
                'item_id' => 2,
                'user_id' => 2,
                'loan_date' => Carbon::now()->subDay(),
                'due_date' => Carbon::now()->addDays(6),
                'return_date' => null,
                'status' => 'Dipinjam',
                'quantity' => 1,
                'purpose' => 'Backup OS Lab',
                'is_extended' => false,
                'extension_requested' => true,
                'extension_approved' => null, // menunggu persetujuan admin
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
        ]);
    }
}
