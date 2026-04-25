<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Lapangan extends Model
{
    protected $fillable = [
        'nama_lapangan',
        'jenis_lapangan',
        'harga_per_jam',
        'status',
    ];

    protected $casts = [
        'harga_per_jam' => 'decimal:2',
    ];

    public const STATUS_OPTIONS = [
        'tersedia' => 'Tersedia',
        'maintenance' => 'Maintenance',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
