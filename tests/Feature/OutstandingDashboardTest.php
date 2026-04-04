<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AssetFinding;
use App\Models\BeritaAcara;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutstandingDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $spv;
    protected $cpm;
    protected $it;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with roles
        $this->spv = User::create([
            'name' => 'SPV User',
            'email' => 'spv@test.com',
            'password' => bcrypt('password'),
            'role' => 'SPV',
        ]);

        $this->cpm = User::create([
            'name' => 'CPM User',
            'email' => 'cpm@test.com',
            'password' => bcrypt('password'),
            'role' => 'CPM',
        ]);

        $this->it = User::create([
            'name' => 'IT User',
            'email' => 'it@test.com',
            'password' => bcrypt('password'),
            'role' => 'IT',
        ]);
    }

    /** @test */
    public function a_user_can_login()
    {
        $response = $this->post('/login', [
            'email' => 'spv@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->spv);
    }

    /** @test */
    public function dashboard_shows_correct_counts()
    {
        // Seed some data
        AssetFinding::create([
            'finding_code' => 'T-001',
            'finding_date' => now(),
            'location' => 'Gate 1',
            'asset_type' => 'Barrier',
            'description' => 'Broken',
            'status' => 'Open',
        ]);

        BeritaAcara::create([
            'ba_number' => 'BA-001',
            'ba_type' => 'Kehilangan',
            'incident_date' => now(),
            'customer_name' => 'John Doe',
            'chronology' => 'Lost something',
            'status' => 'Draft',
        ]);

        $this->actingAs($this->spv);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('1'); // Should see the counts
    }

    /** @test */
    public function spv_can_create_asset_finding()
    {
        $this->actingAs($this->spv);

        $response = $this->post('/findings', [
            'location' => 'Area A',
            'asset_type' => 'Gate Controller',
            'description' => 'Sensor malfunction',
            'finding_date' => '2026-04-03',
        ]);

        $response->assertRedirect('/findings');
        $this->assertDatabaseHas('asset_findings', [
            'location' => 'Area A',
            'asset_type' => 'Gate Controller',
        ]);
    }

    /** @test */
    public function cpm_can_assign_pic_to_finding()
    {
        $finding = AssetFinding::create([
            'finding_code' => 'T-002',
            'finding_date' => now(),
            'location' => 'Gate 2',
            'asset_type' => 'Barrier',
            'description' => 'Stuck',
            'status' => 'Open',
        ]);

        $this->actingAs($this->cpm);

        $response = $this->put("/findings/{$finding->id}", [
            'status' => 'On Progress',
            'pic_id' => $this->it->id,
            'estimated_completion_date' => '2026-04-10',
        ]);

        $response->assertRedirect('/findings');
        $this->assertDatabaseHas('asset_findings', [
            'id' => $finding->id,
            'status' => 'On Progress',
            'pic_id' => $this->it->id,
        ]);
    }

    /** @test */
    public function spv_can_create_berita_acara()
    {
        $this->actingAs($this->spv);

        $response = $this->post('/ba', [
            'ba_type' => 'Kehilangan',
            'incident_date' => '2026-04-03',
            'customer_name' => 'John Customer',
            'license_plate' => 'B 1234 ABC',
            'chronology' => 'Customer lost ticket.',
        ]);

        $response->assertRedirect('/ba');
        $this->assertDatabaseHas('berita_acaras', [
            'customer_name' => 'John Customer',
            'ba_type' => 'Kehilangan',
        ]);
    }

    /** @test */
    public function cpm_can_update_ba_status()
    {
        $ba = BeritaAcara::create([
            'ba_number' => 'BA-002',
            'ba_type' => 'Insiden',
            'incident_date' => now(),
            'customer_name' => 'Jane Snow',
            'chronology' => 'Scratched car',
            'status' => 'Submitted',
        ]);

        $this->actingAs($this->cpm);

        $response = $this->put("/ba/{$ba->id}", [
            'status' => 'Processed',
        ]);

        $response->assertRedirect('/ba');
        $this->assertDatabaseHas('berita_acaras', [
            'id' => $ba->id,
            'status' => 'Processed',
        ]);
    }
}
