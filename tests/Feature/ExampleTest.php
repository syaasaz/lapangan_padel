<?php

namespace Tests\Feature;

use App\Models\Lapangan;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_homepage(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Login');
        $response->assertSee('Masuk');
    }

    public function test_admin_can_login_with_session(): void
    {
        $password = 'secret123';
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => $password,
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => $admin->email,
            'password' => $password,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_non_admin_cannot_login(): void
    {
        $password = 'secret123';
        $user = User::factory()->create([
            'role' => 'user',
            'password' => $password,
        ]);

        $response = $this->from(route('login'))->post(route('login.submit'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_password_is_stored_in_hashed_format(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => 'plain-password',
        ]);

        $this->assertNotSame('plain-password', $admin->getRawOriginal('password'));
        $this->assertTrue(Hash::check('plain-password', $admin->getRawOriginal('password')));
    }

    public function test_guest_cannot_access_admin_features(): void
    {
        $this->get(route('reservations.index'))->assertRedirect(route('login'));
        $this->get(route('lapangans.index'))->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_admin_features_even_if_authenticated(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('dashboard'))->assertForbidden();
        $this->actingAs($user)->get(route('reservations.index'))->assertForbidden();
        $this->actingAs($user)->get(route('lapangans.index'))->assertForbidden();
    }

    public function test_admin_can_create_lapangan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('lapangans.store'), [
            'nama_lapangan' => 'Lapangan A',
            'jenis_lapangan' => 'Futsal',
            'harga_per_jam' => 450000,
            'status' => 'tersedia',
        ]);

        $response->assertRedirect(route('lapangans.index'));
        $this->assertDatabaseHas('lapangans', [
            'nama_lapangan' => 'Lapangan A',
            'jenis_lapangan' => 'Futsal',
            'status' => 'tersedia',
        ]);
    }

    public function test_admin_can_create_and_confirm_reservation_using_selected_lapangan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lapangan = Lapangan::create([
            'nama_lapangan' => 'Lapangan A',
            'jenis_lapangan' => 'Futsal',
            'harga_per_jam' => 450000,
            'status' => 'tersedia',
        ]);

        $createResponse = $this->actingAs($admin)->post(route('reservations.store'), [
            'lapangan_id' => $lapangan->id,
            'nama_pemesan' => 'Budi',
            'no_hp' => '081234567890',
            'tanggal_reservasi' => now()->addDay()->format('Y-m-d'),
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'durasi' => 2,
            'harga' => 200000,
        ]);

        $createResponse->assertRedirect(route('reservations.index'));

        $reservation = Reservation::firstOrFail();

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'lapangan_id' => $lapangan->id,
            'nama_lapangan' => 'Lapangan A',
            'status' => 'Pending',
        ]);

        $confirmResponse = $this->actingAs($admin)->patch(route('reservations.confirm', $reservation));

        $confirmResponse->assertRedirect(route('reservations.index'));
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'Dikonfirmasi',
        ]);
    }
}
