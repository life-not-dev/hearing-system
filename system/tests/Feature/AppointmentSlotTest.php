<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Appointment;
use Illuminate\Support\Facades\Schema;

class AppointmentSlotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure migrations run for sqlite
        $this->artisan('migrate');
    }

    public function test_rejects_out_of_hours_and_enforces_grid()
    {
        $date = now()->addDay()->format('Y-m-d');
        // 07:00 should be rejected
        $resp = $this->json('GET', route('appointments.checkSlot'), [
            'appointment_date' => $date,
            'appointment_time' => '07:00',
        ]);
        $resp->assertStatus(200)->assertJson(['within_hours' => false]);

        // 09:00 within hours but not allowed grid
        $resp = $this->json('GET', route('appointments.checkSlot'), [
            'appointment_date' => $date,
            'appointment_time' => '09:00',
        ]);
        $resp->assertStatus(200)->assertJson(['allowed_start' => false]);
    }

    public function test_prevents_overlap_and_suggests_next()
    {
        $date = now()->addDay()->toDateString();
        // Seed an appointment at 10:00
        Appointment::create([
            'fname' => 'Test User',
            'services' => 'PTA',
            'email' => 't@example.com',
            'appointment_date' => $date,
            'appointment_time' => '10:00:00',
            'status' => Schema::hasColumn('tbl_appointment','status') ? 'confirmed' : null,
        ]);

        // Check 10:00 should be unavailable
        $resp = $this->json('GET', route('appointments.checkSlot'), [
            'appointment_date' => $date,
            'appointment_time' => '10:00',
        ]);
        $resp->assertStatus(200)->assertJson(['available' => false]);

        // Next suggestion should be 12:00
        $json = $resp->json();
        $this->assertEquals('12:00', $json['next_available']);
    }
}
