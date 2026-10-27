<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveLocationRequest extends FormRequest
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
            'parent_id' => ['nullable', 'exists:locations,id', Rule::notIn(array_filter([$this->route('location')?->id]))],
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', Rule::unique('locations', 'code')->ignore($this->route('location'))],
            'type' => ['required', Rule::in(['site', 'building', 'floor', 'office', 'warehouse', 'zone'])],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'map_x' => ['nullable', 'integer', 'between:0,100'],
            'map_y' => ['nullable', 'integer', 'between:0,100'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}
