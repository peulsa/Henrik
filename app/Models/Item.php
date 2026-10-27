<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    use Auditable, HasFactory, SoftDeletes;

    protected $fillable = [
        'location_id', 'sku', 'name', 'category', 'type', 'tracking_type', 'brand', 'model', 'unit',
        'default_min_stock', 'description', 'active',
    ];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function serviceAreas(): BelongsToMany
    {
        return $this->belongsToMany(ServiceArea::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function itemType(): BelongsTo
    {
        return $this->belongsTo(ItemType::class, 'type', 'slug');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getTotalStockAttribute(): int
    {
        return (int) $this->stocks->sum('quantity');
    }
}
