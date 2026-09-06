<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CctvLingkungan extends Model
{
    use HasFactory;

    protected $table = 'cctv_lingkungan';

    protected $fillable = [
        'rt',
        'nama_lokasi',
        'url_stream',
        'status',
    ];

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}
