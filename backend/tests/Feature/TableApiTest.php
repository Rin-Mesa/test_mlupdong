<?php

namespace Tests\Feature;

use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class TableApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_tables()
    {
        Table::factory()->count(3)->create();

        $response = $this->getJson('/api/tables');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_create_table()
    {
        $response = $this->postJson('/api/tables', ['number' => 'T1']);

        $response->assertStatus(201)
            ->assertJson(['number' => 'T1']);
        
        $this->assertDatabaseHas('restaurant_tables', ['number' => 'T1']);
    }

    public function test_can_generate_qr_code()
    {
        $table = Table::create(['number' => 'T2']);

        $response = $this->postJson("/api/tables/{$table->id}/generate-qr");

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'qr_code_url']);
        
        $table->refresh();
        $this->assertNotNull($table->qr_token);
        $this->assertNotNull($table->qr_code_path);
        
        $filePath = public_path($table->qr_code_path);
        $this->assertTrue(File::exists($filePath));
        
        // Cleanup
        File::delete($filePath);
    }

    public function test_can_delete_table()
    {
        $table = Table::create(['number' => 'T3']);
        
        $response = $this->deleteJson("/api/tables/{$table->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('restaurant_tables', ['id' => $table->id]);
    }
}
