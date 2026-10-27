<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Database\Factories\StockFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    /** @use HasFactory<StockFactory> */
    use Auditable, HasFactory;

    protected $fillable = ['item_id', 'location_id', 'quantity', 'min_stock'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function getThresholdAttribute(): int
    {
        return $this->min_stock ?? $this->item->default_min_stock;
    }

    public function getIsLowAttribute(): bool
    {
        return $this->quantity <= $this->threshold;
    }
}
