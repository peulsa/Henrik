<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    /** @use HasFactory<LocationFactory> */
    use Auditable, HasFactory, SoftDeletes;

    protected $fillable = [
        'parent_id', 'name', 'code', 'type', 'address', 'latitude', 'longitude', 'maps_url',
        'notes', 'map_x', 'map_y', 'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
