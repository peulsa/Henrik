<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __invoke(Request $request): View
    {
        $logs = AuditLog::query()
            ->with(['auditable' => function (MorphTo $morphTo): void {
                $morphTo->morphWith([
                    Stock::class => ['item', 'location'],
                ]);
            }])
            ->when($request->filled('actor'), fn ($query) => $query->where('actor_email', 'like', '%'.$request->string('actor')->trim().'%'))
            ->when($request->filled('action'), fn ($query) => $query->where('action', $request->string('action')))
            ->latest()
            ->paginate(40)
            ->withQueryString();

        return view('audit.index', compact('logs'));
    }
}
