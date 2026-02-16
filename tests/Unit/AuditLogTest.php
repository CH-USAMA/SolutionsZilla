<?php

namespace Tests\Unit;

use App\Models\ActivityLog;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a temporary table for the test model
        Schema::create('test_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('clinic_id')->nullable();
            $table->timestamps();
        });
    }

    public function test_activity_is_logged_on_created()
    {
        $clinic = \App\Models\Clinic::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        $model = TestModel::create(['name' => 'Test Item', 'clinic_id' => $clinic->id]);

        $this->assertDatabaseHas('activity_logs', [
            'clinic_id' => $clinic->id,
            'user_id' => $user->id,
            'action' => 'created',
            'loggable_type' => TestModel::class,
            'loggable_id' => $model->id,
            // 'details' field structure varies, avoiding exact match on json
        ]);
    }

    public function test_activity_is_logged_on_updated()
    {
        $clinic = \App\Models\Clinic::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        $model = TestModel::create(['name' => 'Original Name', 'clinic_id' => $clinic->id]);

        // Update
        $model->update(['name' => 'New Name']);

        $this->assertDatabaseHas('activity_logs', [
            'clinic_id' => $clinic->id,
            'user_id' => $user->id,
            'action' => 'updated',
            'loggable_type' => TestModel::class,
            'loggable_id' => $model->id,
        ]);

        // Retrieve log and check details for changes
        $log = \App\Models\ActivityLog::where('action', 'updated')->latest()->first();
        $details = $log->changes; // 'changes' is cast to array in model

        $this->assertArrayHasKey('before', $details);
        $this->assertArrayHasKey('after', $details);
        $this->assertEquals('Original Name', $details['before']['name']);
        $this->assertEquals('New Name', $details['after']['name']);
    }
}

// Helper Class
class TestModel extends Model
{
    use LogsActivity;

    protected $table = 'test_models';
    protected $fillable = ['name', 'clinic_id'];

    // We need to implement abstract if any? No.
}
