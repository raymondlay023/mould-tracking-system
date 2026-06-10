<?php

namespace Tests\Feature;

use App\Imports\MouldsImport;
use App\Models\Mould;
use App\Models\Part;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MouldPartsImportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_can_import_moulds_and_parts_from_excel_structure(): void
    {
        $rows = [
            [
                'mc_no' => '1300T',
                'part_no' => '401-76934B001P',
                'part_name' => 'GARNISH RR PLR RH 5H45 (SEMI)',
                'material' => 'PP-KF06 LD NC356LV (PP-TALC15%)',
                'cust' => 'IKUYO_5H45',
                'cust2' => 'IKUYO',
                'mold_no' => '',
                'cav' => 1,
                'ct' => 90,
            ],
            [
                'mc_no' => '1300T',
                'part_no' => '401-76935B001P',
                'part_name' => 'GARNISH RR PLR LH 5H45 (SEMI)',
                'material' => 'PP-KF06 LD NC356LV (PP-TALC15%)',
                'cust' => 'IKUYO_5H45',
                'cust2' => 'IKUYO',
                'mold_no' => '',
                'cav' => 2,
                'ct' => 90,
            ],
            [
                'mc_no' => '1300T',
                'part_no' => '401-62040B000P',
                'part_name' => 'BRKT FR BMPR 5H45 (SEMI)',
                'material' => 'PP-KF06 LD NC356LV (PP-TALC15%)',
                'cust' => 'IKUYO_5H45',
                'cust2' => 'IKUYO',
                'mold_no' => 'IK-MM-005-005',
                'cav' => 1,
                'ct' => 109.8,
            ],
        ];

        $importer = new MouldsImport(upsert: true);
        $importer->collection(collect($rows));

        // 1. Assert three moulds were created (2 from fallback to part_no, 1 from mold_no)
        $this->assertDatabaseCount('moulds', 3);
        $this->assertDatabaseCount('parts', 3);

        // 2. Assert specific fallback mould exists
        $mould1 = Mould::where('code', '401-76934B001P')->first();
        $this->assertNotNull($mould1);
        $this->assertEquals('GARNISH RR PLR RH 5H45 (SEMI)', $mould1->name);
        $this->assertEquals(1300, $mould1->min_tonnage_t);
        $this->assertEquals(90.0, $mould1->ideal_cycle_time);

        // 3. Assert specific part exists and is linked
        $part1 = Part::where('part_number', '401-76934B001P')->first();
        $this->assertNotNull($part1);
        $this->assertEquals($mould1->id, $part1->mould_id);
        $this->assertEquals(1, $part1->cavity_number);

        // 4. Assert mould with given MOLD No exists
        $mould3 = Mould::where('code', 'IK-MM-005-005')->first();
        $this->assertNotNull($mould3);
        $this->assertEquals('BRKT FR BMPR 5H45 (SEMI)', $mould3->name);

        $part3 = Part::where('part_number', '401-62040B000P')->first();
        $this->assertNotNull($part3);
        $this->assertEquals($mould3->id, $part3->mould_id);
    }
}
