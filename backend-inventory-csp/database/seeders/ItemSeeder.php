<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('items')->insert([
            // Kategori: Komputer & Laptop
            ['id' => 1, 'name' => 'PC Rakitan Core i7', 'code' => 'LAB-PC-001', 'description' => 'PC untuk praktikum grafika komputer. Spek: Core i7, 16GB RAM, RTX 3060', 'location' => 'Ruang Praktikum 1', 'condition' => 'TERSEDIA', 'quantity' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Laptop Dell Latitude', 'code' => 'LAB-LAP-001', 'description' => 'Laptop untuk peminjaman mobile programming.', 'location' => 'Lemari A1', 'condition' => 'TERSEDIA', 'quantity' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'iMac 24 inch', 'code' => 'LAB-PC-002', 'description' => 'iMac untuk lab desain UI/UX.', 'location' => 'Ruang Praktikum 2', 'condition' => 'DALAM_PERBAIKAN', 'quantity' => 3, 'created_at' => now(), 'updated_at' => now()],

            // Kategori: Prototyping & IoT
            ['id' => 4, 'name' => 'Raspberry Pi 4 Model B', 'code' => 'LAB-IOT-001', 'description' => 'Kit untuk praktikum Dasar Sistem Komputer. RAM 4GB.', 'location' => 'Lemari B2', 'condition' => 'TERSEDIA', 'quantity' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Arduino Uno R3', 'code' => 'LAB-IOT-002', 'description' => 'Kit mikrokontroler dasar.', 'location' => 'Lemari B2', 'condition' => 'TERSEDIA', 'quantity' => 25, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Breadboard & Jumper Kit', 'code' => 'LAB-KIT-001', 'description' => 'Satu set berisi breadboard, kabel jumper, resistor, dan LED.', 'location' => 'Lemari B3', 'condition' => 'TERSEDIA', 'quantity' => 30, 'created_at' => now(), 'updated_at' => now()],

            // Kategori: Jaringan
            ['id' => 7, 'name' => 'Switch Cisco 24 Port', 'code' => 'LAB-JAR-001', 'description' => 'Switch manageable untuk lab Jaringan Komputer.', 'location' => 'Rak Server', 'condition' => 'TERSEDIA', 'quantity' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'name' => 'Crimping Tool Set', 'code' => 'LAB-JAR-002', 'description' => 'Set berisi tang crimping, LAN tester, dan konektor RJ45.', 'location' => 'Kotak Perkakas 1', 'condition' => 'TERSEDIA', 'quantity' => 10, 'created_at' => now(), 'updated_at' => now()],

            // Kategori: Periferal & Lainnya
            ['id' => 9, 'name' => 'Proyektor Epson EB-S41', 'code' => 'LAB-PER-001', 'description' => 'Proyektor untuk presentasi kelas.', 'location' => 'Service Center', 'condition' => 'DALAM_PERBAIKAN', 'quantity' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'name' => 'Webcam Logitech C920', 'code' => 'LAB-PER-002', 'description' => 'Webcam HD untuk kebutuhan video conference.', 'location' => 'Lemari A1', 'condition' => 'TERSEDIA', 'quantity' => 8, 'created_at' => now(), 'updated_at' => now()],
            
            // Contoh Aset yang Dihapuskan
            ['id' => 11, 'name' => 'Printer HP LaserJet', 'code' => 'LAB-PER-OLD-001', 'description' => 'Rusak total dan tidak bisa diperbaiki.', 'location' => 'Gudang lantai 2', 'condition' => 'RUSAK', 'quantity' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}