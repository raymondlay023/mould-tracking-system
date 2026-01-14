<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMouldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $mouldId = $this->input('mouldId');

        return [
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('moulds', 'code')->ignore($mouldId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'cavities' => ['required', 'integer', 'min:1'],
            'customer' => ['nullable', 'string', 'max:255'],
            'resin' => ['nullable', 'string', 'max:255'],
            'min_tonnage_t' => ['nullable', 'integer', 'min:0'],
            'max_tonnage_t' => ['nullable', 'integer', 'min:0'],
            'pm_interval_shot' => ['nullable', 'integer', 'min:0'],
            'pm_interval_days' => ['nullable', 'integer', 'min:0'],
            'commissioned_at' => ['nullable', 'date_format:Y-m-d'],
            'status' => [
                'required',
                Rule::in(['AVAILABLE', 'IN_SETUP', 'IN_RUN', 'IN_MAINTENANCE', 'IN_TRANSIT']),
            ],
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
            'code.required' => 'Mould code is required.',
            'code.unique' => 'This mould code is already in use.',
            'code.max' => 'Mould code cannot exceed 100 characters.',
            
            'name.required' => 'Mould name is required.',
            'name.max' => 'Mould name cannot exceed 255 characters.',
            
            'cavities.required' => 'Number of cavities is required.',
            'cavities.integer' => 'Cavities must be a whole number.',
            'cavities.min' => 'Cavities must be at least 1.',
            
            'min_tonnage_t.integer' => 'Minimum tonnage must be a whole number.',
            'min_tonnage_t.min' => 'Minimum tonnage cannot be negative.',
            
            'max_tonnage_t.integer' => 'Maximum tonnage must be a whole number.',
            'max_tonnage_t.min' => 'Maximum tonnage cannot be negative.',
            
            'pm_interval_shot.integer' => 'PM interval (shots) must be a whole number.',
            'pm_interval_shot.min' => 'PM interval (shots) cannot be negative.',
            
            'pm_interval_days.integer' => 'PM interval (days) must be a whole number.',
            'pm_interval_days.min' => 'PM interval (days) cannot be negative.',
            
            'commissioned_at.date_format' => 'Commissioned date must be in YYYY-MM-DD format.',
            
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status selected.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'code' => 'mould code',
            'name' => 'mould name',
            'cavities' => 'cavities',
            'customer' => 'customer',
            'resin' => 'resin type',
            'min_tonnage_t' => 'minimum tonnage',
            'max_tonnage_t' => 'maximum tonnage',
            'pm_interval_shot' => 'PM interval (shots)',
            'pm_interval_days' => 'PM interval (days)',
            'commissioned_at' => 'commissioned date',
            'status' => 'status',
        ];
    }
}
