<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
}
