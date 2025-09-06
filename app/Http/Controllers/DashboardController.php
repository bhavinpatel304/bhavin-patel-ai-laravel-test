<?php

namespace App\Http\Controllers;

use App\Models\Ticket;

class DashboardController extends Controller
{
    public function summary()
    {
        $statusCounts = Ticket::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $categoryCounts = Ticket::select('category')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category');

        return response()->json([
            'statusCounts' => $statusCounts,
            'categoryCounts' => $categoryCounts,
        ]);
    }
}
