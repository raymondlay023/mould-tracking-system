<?php

namespace App\Actions\Moulds;

use App\Models\Mould;
use App\Enums\MouldStatus;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreateMouldAction
{
    /**
     * Create a new mould.
     *
     * @param array $data
     * @return Mould
     * @throws ValidationException
     */
    public function execute(array $data): Mould
    {
        // 1. Validate basic rules
        Validator::make($data, [
            'code' => ['required', 'string', 'max:100', 'unique:moulds,code'],
            'name' => ['required', 'string', 'max:255'],
            'cavities' => ['required', 'integer', 'min:1'],
            'customer' => ['nullable', 'string', 'max:255'],
            'resin' => ['nullable', 'string', 'max:255'],
            'min_tonnage_t' => ['nullable', 'integer', 'min:0'],
            'max_tonnage_t' => ['nullable', 'integer', 'min:0'],
            'pm_interval_shot' => ['nullable', 'integer', 'min:0'],
            'pm_interval_days' => ['nullable', 'integer', 'min:0'],
            'commissioned_at' => ['nullable', 'date_format:Y-m-d'], // Input should be Y-m-d string
            'status' => ['required', Rule::enum(MouldStatus::class)],
        ])->validate();

        // 2. Business logic validation
        if (isset($data['min_tonnage_t'], $data['max_tonnage_t'])) {
            if ($data['min_tonnage_t'] > $data['max_tonnage_t']) {
                throw ValidationException::withMessages([
                    'min_tonnage_t' => ['Min tonnage cannot be greater than max tonnage.'],
                ]);
            }
        }

        // 3. Data Sanitization
        $data['code'] = trim($data['code']);
        
        // 4. Persistence
        return Mould::create($data);
    }
}
