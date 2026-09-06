<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PanicAlert extends Model
{
    use HasFactory;

    protected $table = 'panic_alerts';

    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'kategori',
        'status',
        'catatan',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getKategoriBadgeAttribute(): string
    {
        return match ($this->kategori) {
            'pencurian' => 'Pencurian / Maling',
            'kebakaran' => 'Kebakaran',
            'medis' => 'Darurat Medis',
            default => 'Bahaya Lainnya',
        };
    }
}
