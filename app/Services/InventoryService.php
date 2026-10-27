<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Support\PlatformContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function record(array $data): StockMovement
    {
        return DB::transaction(function () use ($data): StockMovement {
            $type = $data['type'];
            $quantity = (int) $data['quantity'];
            $fromId = $data['from_location_id'] ?? null;
            $toId = $data['to_location_id'] ?? null;

            if (in_array($type, ['deliver', 'transfer', 'install', 'adjust_out'], true)) {
                $this->decrease((int) $data['item_id'], (int) $fromId, $quantity);
            }

            if (in_array($type, ['receive', 'transfer', 'remove', 'adjust_in'], true)) {
                $this->increase((int) $data['item_id'], (int) $toId, $quantity);
            }

            $actor = PlatformContext::user();
            $movement = StockMovement::query()->create([
                ...$data,
                'performed_by_email' => $actor->email,
                'performed_by_name' => $actor->name,
                'performed_by_external_id' => $actor->externalId,
            ]);

            if (! empty($data['asset_id'])) {
                $this->updateAsset((int) $data['asset_id'], $type, $fromId, $toId);
            }

            return $movement;
        });
    }

    public function updateReceipt(StockMovement $movement, array $data): StockMovement
    {
        return DB::transaction(function () use ($movement, $data): StockMovement {
            $movement->refresh();
            $this->decrease($movement->item_id, $movement->to_location_id, $movement->quantity);
            $this->increase((int) $data['item_id'], (int) $data['to_location_id'], (int) $data['quantity']);
            $actor = PlatformContext::user();
            $movement->update([
                ...$data,
                'reference' => null,
                'performed_by_email' => $actor->email,
                'performed_by_name' => $actor->name,
                'performed_by_external_id' => $actor->externalId,
            ]);

            return $movement;
        });
    }

    public function deleteReceipt(StockMovement $movement): void
    {
        DB::transaction(function () use ($movement): void {
            $movement->refresh();
            $this->decrease($movement->item_id, $movement->to_location_id, $movement->quantity);
            $movement->delete();
        });
    }

    private function increase(int $itemId, int $locationId, int $quantity): void
    {
        $stock = Stock::query()->firstOrCreate(
            ['item_id' => $itemId, 'location_id' => $locationId],
            ['quantity' => 0],
        );
        $stock->increment('quantity', $quantity);
    }

    private function decrease(int $itemId, int $locationId, int $quantity): void
    {
        $stock = Stock::query()
            ->where('item_id', $itemId)
            ->where('location_id', $locationId)
            ->lockForUpdate()
            ->first();

        if (! $stock || $stock->quantity < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'No existe stock suficiente en la ubicación de origen.',
            ]);
        }

        $stock->decrement('quantity', $quantity);
    }

    private function updateAsset(int $assetId, string $type, ?int $fromId, ?int $toId): void
    {
        $asset = Asset::query()->lockForUpdate()->findOrFail($assetId);
        $asset->location_id = in_array($type, ['deliver', 'adjust_out'], true) ? null : $toId;

        if (in_array($type, ['deliver', 'install'], true)) {
            $asset->status = 'assigned';
        } elseif (in_array($type, ['receive', 'remove'], true)) {
            $asset->status = 'in_stock';
        }

        $asset->save();
    }
}
