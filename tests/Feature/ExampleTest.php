<?php

namespace Tests\Feature;

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

    public function test_register_page_is_accessible(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertSee('Register');
        $response->assertSee('Daftar');
    }

    public function test_register_creates_user_with_session_login_and_user_role(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'User Baru',
            'email' => 'baru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'User Baru',
            'email' => 'baru@example.com',
            'role' => 'user',
        ]);
    }

    public function test_session_login_allows_user_to_access_dashboard(): void
    {
        $password = 'secret123';
        $user = User::factory()->create([
            'role' => 'user',
            'password' => $password,
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_password_is_stored_in_hashed_format(): void
    {
        $user = User::factory()->create([
            'password' => 'plain-password',
        ]);

        $this->assertNotSame('plain-password', $user->getRawOriginal('password'));
        $this->assertTrue(Hash::check('plain-password', $user->getRawOriginal('password')));
    }

    public function test_guest_cannot_access_reservation_crud_pages(): void
    {
        $response = $this->get(route('reservations.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_created_reservation_is_always_pending(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post(route('reservations.store'), [
            'nama_pemesan' => 'Budi',
            'no_hp' => '081234567890',
            'tanggal_reservasi' => now()->addDay()->format('Y-m-d'),
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'nama_lapangan' => 'Lapangan A',
            'durasi' => 2,
            'harga' => 200000,
            'status' => 'Dikonfirmasi',
        ]);

        $response->assertRedirect(route('reservations.index'));
        $this->assertDatabaseHas('reservations', [
            'nama_pemesan' => 'Budi',
            'user_id' => $user->id,
            'status' => 'Pending',
        ]);
    }

    public function test_admin_can_confirm_user_reservation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $reservation = Reservation::create([
            'user_id' => $user->id,
            'nama_pemesan' => 'Budi',
            'no_hp' => '081234567890',
            'tanggal_reservasi' => now()->addDay()->toDateString(),
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'nama_lapangan' => 'Lapangan A',
            'durasi' => 2,
            'harga' => 900000,
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($admin)->patch(route('reservations.confirm', $reservation));

        $response->assertRedirect(route('reservations.index'));
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'Dikonfirmasi',
        ]);
    }

    public function test_regular_user_cannot_open_other_users_reservation(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $otherUser = User::factory()->create(['role' => 'user']);
        $reservation = Reservation::create([
            'user_id' => $owner->id,
            'nama_pemesan' => 'Budi',
            'no_hp' => '081234567890',
            'tanggal_reservasi' => now()->addDay()->toDateString(),
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'nama_lapangan' => 'Lapangan A',
            'durasi' => 2,
            'harga' => 900000,
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($otherUser)->get(route('reservations.show', $reservation));

        $response->assertForbidden();
    }
}
