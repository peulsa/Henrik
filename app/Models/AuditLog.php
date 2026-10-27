<?php

namespace App\Models;

use Database\Factories\AuditLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    /** @use HasFactory<AuditLogFactory> */
    use HasFactory;

    protected $fillable = [
        'auditable_type', 'auditable_id', 'action', 'before', 'after', 'actor_email',
        'actor_name', 'actor_external_id', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return ['before' => 'array', 'after' => 'array'];
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'created' => 'Creó',
            'updated' => 'Modificó',
            'deleted' => 'Eliminó',
            default => ucfirst($this->action),
        };
    }

    public function getSubjectLabelAttribute(): string
    {
        if (class_basename($this->auditable_type) === 'Stock' && $this->auditable) {
            return 'stock de “'.($this->auditable->item?->name ?? 'artículo eliminado').'” en '.($this->auditable->location?->name ?? 'ubicación eliminada');
        }

        $kind = match (class_basename($this->auditable_type)) {
            'Item' => 'artículo',
            'Location' => 'ubicación',
            'Stock' => 'stock',
            'Asset' => 'dispositivo',
            'StockMovement' => 'movimiento de stock',
            default => 'registro',
        };
        $name = $this->auditable?->name ?? $this->auditable?->asset_tag ?? null;

        return $name ? $kind.' “'.$name.'”' : $kind.' #'.$this->auditable_id;
    }

    /** @return array<int, string> */
    public function readableChanges(): array
    {
        if ($this->action === 'created') {
            return ['Registró '.$this->subject_label.'.'];
        }

        if ($this->action === 'deleted') {
            return ['Eliminó '.$this->subject_label.'; su historial permanece disponible.'];
        }

        $before = $this->before ?? [];
        $after = $this->after ?? [];
        $labels = [
            'name' => 'nombre', 'sku' => 'código', 'category' => 'categoría', 'type' => 'tipo',
            'location_id' => 'ubicación asignada', 'quantity' => 'cantidad', 'default_min_stock' => 'stock mínimo',
            'active' => 'estado activo', 'status' => 'estado', 'notes' => 'notas', 'address' => 'dirección',
            'item_id' => 'artículo', 'to_location_id' => 'ubicación de destino',
        ];

        return collect($after)->filter(fn ($value, $key) => array_key_exists($key, $labels) && ($before[$key] ?? null) != $value)
            ->map(fn ($value, $key) => 'Cambió '.$labels[$key].' de '.$this->displayValue($before[$key] ?? null, $key).' a '.$this->displayValue($value, $key).'.')
            ->values()->all() ?: ['Actualizó la información de '.$this->subject_label.'.'];
    }

    private function displayValue(mixed $value, string $key): string
    {
        if ($value === null || $value === '') {
            return 'sin información';
        }
        if ($key === 'active') {
            return filter_var($value, FILTER_VALIDATE_BOOL) ? 'sí' : 'no';
        }

        return '“'.(string) $value.'”';
    }
}
