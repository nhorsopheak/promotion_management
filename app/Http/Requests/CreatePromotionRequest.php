<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\PromotionType;
use App\Enums\PromotionStatus;

class CreatePromotionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255', 'unique:promotions,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', 'in:' . implode(',', array_column(PromotionType::cases(), 'value'))],
            'status' => ['required', 'string', 'in:' . implode(',', array_column(PromotionStatus::cases(), 'value'))],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time' => ['nullable', 'date_format:H:i:s'],
            'end_time' => ['nullable', 'date_format:H:i:s'],
            'days_of_week' => ['nullable', 'array'],
            'days_of_week.*' => ['integer', 'between:0,6'],
            'conditions' => ['nullable', 'array'],
            'benefits' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'The promotion code is required.',
            'code.unique' => 'This promotion code already exists.',
            'name.required' => 'The promotion name is required.',
            'type.required' => 'The promotion type is required.',
            'type.in' => 'The selected promotion type is invalid.',
            'status.required' => 'The promotion status is required.',
            'status.in' => 'The selected promotion status is invalid.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
        ];
    }
}
