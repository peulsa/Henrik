<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Item;
use App\Models\Location;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $allLowStocks = Stock::query()
            ->with(['item', 'location'])
            ->whereHas('item', fn ($query) => $query->where('active', true))
            ->get()
            ->filter->is_low
            ->sortBy(fn (Stock $stock) => $stock->quantity - $stock->threshold);

        return view('dashboard', [
            'itemCount' => Item::query()->where('active', true)->count(),
            'locationCount' => Location::query()->where('active', true)->count(),
            'assetCount' => Asset::query()->count(),
            'lowStockCount' => $allLowStocks->count(),
            'lowStocks' => $allLowStocks->take(8),
            'recentMovements' => StockMovement::query()
                ->with(['item', 'fromLocation', 'toLocation'])
                ->latest()
                ->take(8)
                ->get(),
        ]);
    }
}
