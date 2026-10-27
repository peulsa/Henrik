<?php

namespace App\Models;

use Database\Factories\ItemTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemType extends Model
{
    /** @use HasFactory<ItemTypeFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'type', 'slug');
    }
}
