<?php

namespace App\Http\Requests;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveAssetRequest extends FormRequest
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
            'location_id' => ['nullable', 'exists:locations,id'],
            'asset_tag' => ['required', 'string', 'max:100', Rule::unique('assets', 'asset_tag')->ignore($this->route('asset'))],
            'serial_number' => ['nullable', 'string', 'max:150'],
            'status' => ['required', Rule::in(['in_stock', 'assigned', 'repair', 'retired'])],
            'assigned_to_email' => ['nullable', 'email', 'max:255'],
            'acquired_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $item = Item::query()->find($this->integer('item_id'));

                if ($item && $item->tracking_type !== 'serialized') {
                    $validator->errors()->add('item_id', 'Solo se pueden identificar artículos con seguimiento por serie.');
                }
            },
        ];
    }
}
