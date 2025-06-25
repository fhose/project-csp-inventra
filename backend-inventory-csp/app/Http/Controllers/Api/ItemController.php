<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule; // <-- Import Rule untuk validasi unik saat update
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{


    /**
     * Menampilkan daftar semua barang.
     * (Bisa diakses semua peran yang sudah login)
     */
   public function index(): JsonResponse
{
    $perPage = request()->query('per_page', 10);

    $items = Item::with([
            // Ambil hanya return_date agar ringan
            'loans:id,item_id,return_date'
        ])
        ->withCount([
            'loans as quantity_on_loan' => function ($q) {
                $q->whereIn('status', ['Dipinjam', 'Terlambat']);
            }
        ])
        ->latest()
        ->paginate($perPage);

    $items->setCollection(
        $items->getCollection()->transform(function ($item) {
            $item->available_quantity = max(0, $item->quantity - $item->quantity_on_loan);
            return $item;
        })
    );

    return response()->json([
        'message' => 'Daftar barang berhasil diambil.',
        'data' => $items
    ]);
}





    /**
     * Menyimpan barang baru.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'asisten'])) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:items,code',
            'location' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:TERSEDIA,DALAM_PERBAIKAN,RUSAK',
            'description' => 'nullable|string',
        ]);

        $item = Item::create($validated);
        // Menambahkan 'message' dan membungkus dalam 'data' untuk konsistensi
        return response()->json([
            'message' => 'Barang berhasil ditambahkan.',
            'data' => $item
        ], 201);
    }

    /**
     * Menampilkan satu barang spesifik.
     */
    public function show(Item $item): JsonResponse
    {
        // Dibungkus dalam 'data'
        return response()->json(['data' => $item]);
    }

    /**
     * Memperbarui barang yang sudah ada.
     */
    public function update(Request $request, Item $item): JsonResponse
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'asisten'])) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Saat update, aturan 'unique' harus mengabaikan data item itu sendiri
            'code' => ['required', 'string', Rule::unique('items')->ignore($item->id)],
            'location' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0', // Boleh 0 jika barang habis
            'condition' => 'required|in:TERSEDIA,DALAM_PERBAIKAN,RUSAK',
            'description' => 'nullable|string',
        ]);

        // Cek apakah barang sedang dipinjam
        // Jika ada peminjaman yang statusnya 'Dipinjam', tidak boleh diupdate
        // Menggunakan 'exists' untuk mengecek apakah ada peminjaman aktif
        // Menggunakan 'loans' relasi yang sudah didefinisikan di model Item
        if ($item->loans()->where('status', 'Dipinjam')->exists()) {
            return response()->json(['message' => 'Barang sedang dipinjam dan tidak dapat diedit atau dihapus.'], 403);
        }


        $item->update($validated);
        return response()->json([
            'message' => 'Data barang berhasil diperbarui.',
            'data' => $item
        ]);
    }

    /**
     * Menghapus barang dari penyimpanan.
     */
    public function destroy(Item $item): JsonResponse
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'asisten'])) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Cek apakah barang sedang dipinjam
        // Jika ada peminjaman yang statusnya 'Dipinjam', tidak boleh dihapus
        if ($item->loans()->where('status', 'Dipinjam')->exists()) {
            return response()->json(['message' => 'Barang sedang dipinjam dan tidak dapat diedit atau dihapus.'], 403);
        }

        $item->delete();
        return response()->json(['message' => 'Barang berhasil dihapus.'], 200);
        // Mengubah ke 200 dengan message agar frontend bisa memberi notifikasi.
        // 204 juga benar, tapi tidak bisa membawa body/message.
    }

    /**
     * Menyediakan daftar enum kondisi barang.
     */
    public function conditions(): JsonResponse
    {
        $conditions = ['TERSEDIA', 'DALAM_PERBAIKAN', 'RUSAK'];

        return response()->json([
            'data' => $conditions
        ]);
    }
}