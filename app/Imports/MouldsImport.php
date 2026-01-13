<?php

namespace App\Imports;

use App\Models\Mould;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MouldsImport implements ToCollection, WithHeadingRow
{
    public array $errors = [];

    public int $inserted = 0;

    public int $updated = 0;

    public function __construct(private bool $upsert = true) {}

    public function collection(Collection $rows)
    {
        $rowNumber = 1; // headingRow sudah dipisah, ini untuk tracking manual

        foreach ($rows as $row) {
            $rowNumber++;

            // Normalisasi key (heading)
            $data = [
                'code' => trim((string) ($row['code'] ?? '')),
                'name' => trim((string) ($row['name'] ?? '')),
                'cavities' => $row['cavities'] ?? null,
                'customer' => $row['customer'] ?? null,
                'resin' => $row['resin'] ?? null,
                'min_tonnage_t' => $row['min_tonnage_t'] ?? null,
                'max_tonnage_t' => $row['max_tonnage_t'] ?? null,
                'pm_interval_shot' => $row['pm_interval_shot'] ?? null,
                'pm_interval_days' => $row['pm_interval_days'] ?? null,
                'commissioned_at' => $row['commissioned_at'] ?? null,
                'status' => strtoupper(trim((string) ($row['status'] ?? 'AVAILABLE'))),
            ];

            // Validasi per baris (manual supaya bisa kumpulin error tanpa stop)
            $rowErrors = $this->validateRow($data, $rowNumber);

            if (! empty($rowErrors)) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'code' => $data['code'],
                    'errors' => $rowErrors,
                ];

                continue;
            }

            // Upsert by code (lebih natural untuk import master)
            $existing = Mould::where('code', $data['code'])->first();

            if ($existing) {
                if ($this->upsert) {
                    $existing->update($data);
                    $this->updated++;
                }
            } else {
                // UUID biasanya auto di model via HasUuids, tapi aman:
                $data['id'] = (string) Str::uuid();
                Mould::create($data);
                $this->inserted++;
            }
        }
    }

    private function validateRow(array $data, int $rowNumber): array
    {
        $errors = [];

        // Required
        if ($data['code'] === '') {
            $errors[] = 'code wajib diisi';
        }
        if ($data['name'] === '') {
            $errors[] = 'name wajib diisi';
        }

        // cavities
        if (! is_numeric($data['cavities']) || (int) $data['cavities'] < 1) {
            $errors[] = 'cavities harus integer >= 1';
        }

        // tonnage
        if ($data['min_tonnage_t'] !== null && $data['min_tonnage_t'] !== '' && (! is_numeric($data['min_tonnage_t']) || (int) $data['min_tonnage_t'] < 0)) {
            $errors[] = 'min_tonnage_t harus integer >= 0';
        }
        if ($data['max_tonnage_t'] !== null && $data['max_tonnage_t'] !== '' && (! is_numeric($data['max_tonnage_t']) || (int) $data['max_tonnage_t'] < 0)) {
            $errors[] = 'max_tonnage_t harus integer >= 0';
        }
        if (is_numeric($data['min_tonnage_t']) && is_numeric($data['max_tonnage_t'])) {
            if ((int) $data['min_tonnage_t'] > (int) $data['max_tonnage_t']) {
                $errors[] = 'min_tonnage_t tidak boleh > max_tonnage_t';
            }
        }

        // pm interval
        foreach (['pm_interval_shot', 'pm_interval_days'] as $f) {
            if ($data[$f] !== null && $data[$f] !== '' && (! is_numeric($data[$f]) || (int) $data[$f] < 0)) {
                $errors[] = "$f harus integer >= 0";
            }
        }

        // date format (YYYY-MM-DD)
        if ($data['commissioned_at'] !== null && $data['commissioned_at'] !== '') {
            if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $data['commissioned_at'])) {
                $errors[] = 'commissioned_at harus format YYYY-MM-DD';
            }
        }

        // status
        $allowed = ['AVAILABLE', 'IN_SETUP', 'IN_RUN', 'IN_MAINTENANCE', 'IN_TRANSIT'];
        if (! in_array($data['status'], $allowed, true)) {
            $errors[] = 'status harus salah satu: '.implode(',', $allowed);
        }

        return $errors;
    }
}
