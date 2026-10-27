<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Database\Factories\AssetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    /** @use HasFactory<AssetFactory> */
    use Auditable, HasFactory, SoftDeletes;

    protected $fillable = [
        'item_id', 'location_id', 'asset_tag', 'serial_number', 'status',
        'assigned_to_email', 'acquired_at', 'notes',
    ];

    protected function casts(): array
    {
        return ['acquired_at' => 'date'];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
