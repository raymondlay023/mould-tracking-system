<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceEventRequest extends FormRequest
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
            'mould_id' => ['required', 'exists:moulds,id'],
            'start_ts' => ['required', 'date'],
            'end_ts' => ['required', 'date', 'after:start_ts'],
            'type' => ['required', 'in:PM,CM'],
            'description' => ['nullable', 'string', 'max:255'],
            'parts_used' => ['nullable', 'string', 'max:5000'],
            'downtime_min' => ['required', 'integer', 'min:0'],
            'cost' => ['nullable', 'integer', 'min:0'],
            'next_due_shot' => ['nullable', 'integer', 'min:0'],
            'next_due_date' => ['nullable', 'date'],
            'performed_by' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:5000'],
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
            'mould_id.required' => 'Please select a mould.',
            'mould_id.exists' => 'Selected mould does not exist.',
            
            'start_ts.required' => 'Start time is required.',
            'start_ts.date' => 'Start time must be a valid date/time.',
            
            'end_ts.required' => 'End time is required.',
            'end_ts.date' => 'End time must be a valid date/time.',
            'end_ts.after' => 'End time must be after start time.',
            
            'type.required' => 'Maintenance type is required.',
            'type.in' => 'Maintenance type must be PM or CM.',
            
            'description.max' => 'Description cannot exceed 255 characters.',
            
            'parts_used.max' => 'Parts used list cannot exceed 5000 characters.',
            
            'downtime_min.required' => 'Downtime is required.',
            'downtime_min.integer' => 'Downtime must be a whole number.',
            'downtime_min.min' => 'Downtime cannot be negative.',
            
            'cost.integer' => 'Cost must be a whole number.',
            'cost.min' => 'Cost cannot be negative.',
            
            'next_due_shot.integer' => 'Next due shot must be a whole number.',
            'next_due_shot.min' => 'Next due shot cannot be negative.',
            
            'next_due_date.date' => 'Next due date must be a valid date.',
            
            'performed_by.max' => 'Performed by name cannot exceed 100 characters.',
            
            'notes.max' => 'Notes cannot exceed 5000 characters.',
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
            'mould_id' => 'mould',
            'start_ts' => 'start time',
            'end_ts' => 'end time',
            'type' => 'type',
            'description' => 'description',
            'parts_used' => 'parts used',
            'downtime_min' => 'downtime (minutes)',
            'cost' => 'cost',
            'next_due_shot' => 'next due shot',
            'next_due_date' => 'next due date',
            'performed_by' => 'performed by',
            'notes' => 'notes',
        ];
    }
}
