<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloseProductionRunRequest extends FormRequest
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
        return [
            'shot_total' => ['required', 'integer', 'min:0'],
            'ok_part' => ['required', 'integer', 'min:0'],
            'ng_part' => ['required', 'integer', 'min:0'],
            'cycle_time_avg_sec' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'defects' => ['array'],
            'defects.*.defect_code' => ['nullable', 'string', 'max:50'],
            'defects.*.qty' => ['nullable', 'integer', 'min:0'],
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
            'shot_total.required' => 'Shot total is required.',
            'shot_total.integer' => 'Shot total must be a whole number.',
            'shot_total.min' => 'Shot total cannot be negative.',
            
            'ok_part.required' => 'OK parts count is required.',
            'ok_part.integer' => 'OK parts must be a whole number.',
            'ok_part.min' => 'OK parts cannot be negative.',
            
            'ng_part.required' => 'NG parts count is required.',
            'ng_part.integer' => 'NG parts must be a whole number.',
            'ng_part.min' => 'NG parts cannot be negative.',
            
            'cycle_time_avg_sec.integer' => 'Cycle time must be a whole number.',
            'cycle_time_avg_sec.min' => 'Cycle time must be at least 1 second.',
            
            'notes.max' => 'Notes cannot exceed 2000 characters.',
            
            'defects.array' => 'Defects must be an array.',
            'defects.*.defect_code.string' => 'Defect code must be text.',
            'defects.*.defect_code.max' => 'Defect code cannot exceed 50 characters.',
            'defects.*.qty.integer' => 'Defect quantity must be a whole number.',
            'defects.*.qty.min' => 'Defect quantity cannot be negative.',
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
            'shot_total' => 'shot total',
            'ok_part' => 'OK parts',
            'ng_part' => 'NG parts',
            'cycle_time_avg_sec' => 'average cycle time',
            'notes' => 'notes',
            'defects.*.defect_code' => 'defect code',
            'defects.*.qty' => 'defect quantity',
        ];
    }
}
