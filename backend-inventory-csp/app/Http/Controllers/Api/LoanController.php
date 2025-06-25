<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Menampilkan daftar peminjaman.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $query = Loan::with(['user:id,name', 'item:id,name,code']);

        if ($user->role === 'mahasiswa') {
            $loans = $query->where('user_id', $user->id)->latest()->paginate(10);
        } else {
            $loans = $query->latest()->paginate(10);
        }

        return response()->json([
            'message' => 'Riwayat peminjaman berhasil diambil.',
            'data' => $loans
        ]);
    }

    /**
     * Menyimpan data peminjaman baru.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'loan_date' => 'required|date|after_or_equal:today',
            'due_date' => 'required|date|after:loan_date',
            'quantity' => 'required|integer|min:1',
            'purpose' => 'required|string|max:500',
        ]);

        $user = Auth::user();

        // Penalti check
        if ($user->penalty_until && now()->lt($user->penalty_until)) {
            return response()->json([
                'message' => 'Anda sedang dalam masa penalti hingga ' . $user->penalty_until->translatedFormat('d F Y'),
            ], 403);
        }

        $item = Item::findOrFail($request->item_id);

        if ($item->condition !== 'TERSEDIA' || $item->quantity < $request->quantity) {
            return response()->json(['message' => 'Barang tidak tersedia atau stok tidak mencukupi.'], 422);
        }

        $loan = Loan::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'loan_date' => $request->loan_date,
            'due_date' => $request->due_date,
            'quantity' => $request->quantity,
            'purpose' => $request->purpose,
            'status' => 'Menunggu Konfirmasi',
        ]);

        return response()->json([
            'message' => 'Peminjaman berhasil dibuat.',
            'data' => $loan
        ], 201);
    }



    /**
     * Menampilkan detail satu peminjaman.
     */
    public function show(Loan $loan): JsonResponse
    {
        $user = Auth::user();

        if ($user->role === 'mahasiswa' && $loan->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'message' => 'Detail peminjaman berhasil diambil.',
            'data' => $loan->load(['user:id,name,email', 'item'])
        ]);
    }

    /**
     * Memperbarui status peminjaman (misalnya pengembalian).
     */
    public function update(Request $request, Loan $loan): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:Dikembalikan',
        ]);

        $user = Auth::user();

        // Logika akses mahasiswa
        if ($user->role === 'mahasiswa') {
            if ($loan->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            if (!in_array($loan->status, ['Dipinjam', 'Terlambat'])) {
                return response()->json(['message' => 'Peminjaman tidak dalam status aktif.'], 422);
            }
        }

        // Admin, asisten, atau mahasiswa
        if (!in_array($user->role, ['admin', 'asisten', 'mahasiswa'])) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($loan->status === 'Dikembalikan') {
            return response()->json(['message' => 'Barang sudah dikembalikan.'], 422);
        }

        // Update status sesuai input
        $loan->status = 'Dikembalikan';
        $loan->return_date = now();

        // Kembalikan stok barang
        if ($loan->item) {
            $loan->item->increment('quantity', $loan->quantity);
        }

        // Beri penalti jika terlambat
        if (now()->gt($loan->due_date)) {
            $borrower = $loan->user;
            $penaltyDays = 7;

            if ($borrower->penalty_until && $borrower->penalty_until->isFuture()) {
                $borrower->penalty_until = $borrower->penalty_until->addDays($penaltyDays);
            } else {
                $borrower->penalty_until = now()->addDays($penaltyDays);
            }

            $borrower->save();
        }

        $loan->save();

        return response()->json([
            'message' => 'Status peminjaman berhasil diperbarui.',
            'data' => $loan->load('item'),
        ]);
    }




    /**
     * Menghapus data peminjaman.
     */
    public function destroy(Loan $loan): JsonResponse
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'asisten'])) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $loan->delete();

        return response()->json([
            'message' => 'Data peminjaman berhasil dihapus.'
        ], 200);
    }

    /**
     * Menyetujui peminjaman.
     */
    public function approve(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'asisten'])) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $loan = Loan::with('item')->findOrFail($id);

        // Validasi kondisi dan stok
        if (
            $loan->item->condition !== 'TERSEDIA' ||
            $loan->item->quantity < $loan->quantity
        ) {
            return response()->json([
                'message' => 'Barang tidak tersedia atau stok tidak mencukupi.'
            ], 422);
        }

        // Update status dan kurangi stok
        $loan->status = 'Dipinjam';
        $loan->save();

        $loan->item->decrement('quantity', $loan->quantity); // safe update

        return response()->json([
            'message' => 'Peminjaman disetujui.',
            'data' => $loan->load('item')
        ]);
    }


    /**
     * Menolak peminjaman.
     */
    public function reject(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'asisten'])) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $loan = Loan::findOrFail($id);
        $loan->status = 'Ditolak';
        $loan->save();

        return response()->json([
            'message' => 'Peminjaman ditolak.',
            'data' => $loan
        ]);
    }

    public function active(): JsonResponse
    {
        $user = Auth::user();

        $query = Loan::with(['item:id,name,code'])
            ->whereIn('status', ['Menunggu Konfirmasi', 'Dipinjam', 'Terlambat']);

        if ($user->role === 'mahasiswa') {
            $query->where('user_id', $user->id);
        }

        $loans = $query->latest()->paginate(10);

        return response()->json([
            'message' => 'Daftar peminjaman aktif berhasil diambil.',
            'data' => $loans
        ]);
    }

    /**
     * Memperpanjang masa peminjaman (hanya oleh mahasiswa & status Dipinjam).
     */


    public function requestExtension($id): JsonResponse
    {
        $user = Auth::user();
        $loan = Loan::findOrFail($id);

        if ($user->role !== 'mahasiswa' || $loan->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($loan->status !== 'Dipinjam') {
            return response()->json(['message' => 'Hanya bisa mengajukan perpanjangan untuk pinjaman yang sedang berjalan.'], 422);
        }

        if ($loan->is_extended) {
            return response()->json(['message' => 'Peminjaman ini sudah pernah diperpanjang.'], 422);
        }

        if ($loan->extension_requested) {
            return response()->json(['message' => 'Anda sudah mengajukan perpanjangan.'], 422);
        }

        if ($loan->extension_approved === false) {
            return response()->json(['message' => 'Pengajuan sebelumnya sudah ditolak.'], 422);
        }

        $loan->extension_requested = true;
        $loan->extension_approved = null;
        $loan->save();

        return response()->json([
            'message' => 'Pengajuan perpanjangan berhasil dikirim. Menunggu persetujuan admin.',
            'data' => $loan
        ]);
    }



    public function approveExtension($id): JsonResponse
    {
        $user = Auth::user();
        $loan = Loan::findOrFail($id);

        if (!in_array($user->role, ['admin', 'asisten'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$loan->extension_requested || $loan->extension_approved !== null) {
            return response()->json(['message' => 'Tidak ada pengajuan perpanjangan yang perlu disetujui.'], 422);
        }

        $loan->due_date = Carbon::parse($loan->due_date)->addDays(7);
        $loan->is_extended = true;
        $loan->extension_approved = true;
        $loan->save();

        return response()->json([
            'message' => 'Perpanjangan peminjaman disetujui.',
            'data' => $loan
        ]);
    }

    public function rejectExtension($id): JsonResponse
    {
        $user = Auth::user();
        $loan = Loan::findOrFail($id);

        if (!in_array($user->role, ['admin', 'asisten'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$loan->extension_requested || $loan->extension_approved !== null) {
            return response()->json(['message' => 'Tidak ada pengajuan perpanjangan yang perlu ditolak.'], 422);
        }

        $loan->extension_approved = false;
        $loan->save();

        return response()->json([
            'message' => 'Pengajuan perpanjangan ditolak.',
            'data' => $loan
        ]);
    }

}
