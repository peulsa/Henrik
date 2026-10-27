<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreStockMovementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'item_id' => ['required', 'exists:items,id'],
            'asset_id' => ['nullable', 'exists:assets,id'],
            'type' => ['required', Rule::in(['receive', 'deliver', 'transfer', 'install', 'remove', 'adjust_in', 'adjust_out'])],
            'quantity' => ['required', 'integer', 'min:1'],
            'from_location_id' => ['nullable', 'required_if:type,deliver,transfer,install,remove,adjust_out', 'exists:locations,id'],
            'to_location_id' => ['nullable', 'required_if:type,receive,transfer,install,remove,adjust_in', 'exists:locations,id', 'different:from_location_id'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (! $this->filled('asset_id')) {
                    return;
                }

                $asset = Asset::query()->find($this->integer('asset_id'));
                if (! $asset || $asset->item_id !== $this->integer('item_id') || $this->integer('quantity') !== 1) {
                    $validator->errors()->add('asset_id', 'El equipo debe corresponder al artículo y moverse de a una unidad.');

                    return;
                }

                if ($this->filled('from_location_id') && $asset->location_id !== $this->integer('from_location_id')) {
                    $validator->errors()->add('from_location_id', 'El equipo no se encuentra en la ubicación de origen indicada.');
                }
            },
        ];
    }
}
