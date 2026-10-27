<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveItemRequest extends FormRequest
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
            'sku' => ['required', 'string', 'max:80', Rule::unique('items', 'sku')->ignore($this->route('item'))],
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in(['supply', 'spare_part', 'equipment'])],
            'tracking_type' => ['required', Rule::in(['bulk', 'serialized'])],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:40'],
            'default_min_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'active' => ['sometimes', 'boolean'],
            'service_area_ids' => ['nullable', 'array'],
            'service_area_ids.*' => ['integer', 'exists:service_areas,id'],
        ];
    }
}
