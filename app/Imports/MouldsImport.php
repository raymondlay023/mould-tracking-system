<?php

namespace App\Imports;

use App\Models\Mould;
use App\Models\Part;
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
            // MOLD No. harus diisi — tidak ada fallback ke PART NO. agar tabel moulds
            // tidak terkontaminasi dengan nomor part.
            $mouldCode = trim((string) ($row['mold_no'] ?? $row['mould_no'] ?? $row['mold_no_'] ?? $row['code'] ?? ''));
            $partNumber = trim((string) ($row['part_no'] ?? $row['part_number'] ?? $row['part_no_'] ?? ''));

            $partName = trim((string) ($row['part_name'] ?? $row['name'] ?? ''));

            // Parse tonnage dari MC NO (misal "1300T" -> 1300)
            $minTonnage = null;
            $mcNo = $row['mc_no'] ?? $row['mc_no_'] ?? $row['min_tonnage_t'] ?? null;
            if ($mcNo !== null && $mcNo !== '') {
                if (preg_match('/(\d+)/', (string) $mcNo, $matches)) {
                    $minTonnage = (int) $matches[1];
                }
            }
            $maxTonnage = isset($row['max_tonnage_t']) ? (int) $row['max_tonnage_t'] : $minTonnage;

            // Ideal Cycle Time (CT)
            $ctVal = $row['ct'] ?? $row['ideal_cycle_time'] ?? null;
            $idealCycleTime = ($ctVal !== null && $ctVal !== '') ? (float) $ctVal : null;

            $data = [
                'code' => $mouldCode,
                'name' => $partName,
                'part_number' => $partNumber,
                'cavities' => $row['cav'] ?? $row['cavities'] ?? null,
                'customer' => $row['cust2'] ?? $row['customer'] ?? $row['cust'] ?? null,
                'resin' => $row['material'] ?? $row['resin'] ?? null,
                'min_tonnage_t' => $minTonnage,
                'max_tonnage_t' => $maxTonnage,
                'pm_interval_shot' => $row['pm_interval_shot'] ?? null,
                'pm_interval_days' => $row['pm_interval_days'] ?? null,
                'ideal_cycle_time' => $idealCycleTime,
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

            // Pisahkan part_number dari $data untuk update/create Mould
            $mouldData = $data;
            unset($mouldData['part_number']);

            if ($existing) {
                if ($this->upsert) {
                    $existing->update($mouldData);
                    $this->updated++;
                }
            } else {
                // UUID biasanya auto di model via HasUuids, tapi aman:
                $mouldData['id'] = (string) Str::uuid();
                $existing = Mould::create($mouldData);
                $this->inserted++;
            }

            // Create atau update Part yang berasosiasi
            if ($partNumber !== '') {
                Part::updateOrCreate(
                    ['part_number' => $partNumber],
                    [
                        'mould_id' => $existing->id,
                        'part_name' => $partName,
                        'cavity_number' => $data['cavities'] !== null ? (int) $data['cavities'] : null,
                    ]
                );
            }
        }
    }

    private function validateRow(array $data, int $rowNumber): array
    {
        $errors = [];

        // Required
        if ($data['code'] === '') {
            $errors[] = 'MOLD No. wajib diisi — lengkapi kolom MOLD No. pada file Excel sebelum import';
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
