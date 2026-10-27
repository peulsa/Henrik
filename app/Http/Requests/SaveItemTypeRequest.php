<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SaveItemTypeRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100', Rule::unique('item_types', 'name')->ignore($this->route('item_type'))],
            'slug' => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('item_types', 'slug')->ignore($this->route('item_type'))],
            'description' => ['nullable', 'string', 'max:1000'],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $slug = $this->string('slug')->value() ?: $this->string('name')->value();
        $this->merge(['slug' => Str::slug($slug)]);
    }
}
