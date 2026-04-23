<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'nama_pemesan',
        'no_hp',
        'tanggal_reservasi',
        'jam_mulai',
        'jam_selesai',
        'nama_lapangan',
        'durasi',
        'harga',
        'status',
        'user_id',
    ];

    protected $casts = [
        'tanggal_reservasi' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'harga' => 'decimal:2',
    ];

    public const STATUS_OPTIONS = [
        'Pending' => 'Pending',
        'Dikonfirmasi' => 'Dikonfirmasi',
        'Selesai' => 'Selesai',
        'Dibatalkan' => 'Dibatalkan',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
