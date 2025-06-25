<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $stats = [
            'total_items' => Item::count(),
            'total_users' => User::where('role', 'mahasiswa')->count(),
            'total_admins' => User::whereIn('role', ['admin', 'asisten'])->count(),
            'items_on_loan' => Loan::whereIn('status', ['Dipinjam', 'Terlambat'])->sum('quantity'),
            'items_needing_repair' => Item::whereIn('condition', ['RUSAK', 'DALAM_PERBAIKAN'])->count(),
            'overdue_loans' => Loan::where('status', 'Terlambat')->count(),
        ];

        $recent_activities = Loan::with(['user:id,name', 'item:id,name'])
                                ->latest()
                                ->take(10)
                                ->get();

        return response()->json([
            'stats' => $stats,
            'recent_activities' => $recent_activities,
        ]);
    }
}
