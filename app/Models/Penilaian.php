<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Penilaian extends Model
{
    protected $table = 'penilaian';

    protected $fillable = [
        'regional',
        'divisi',
        'nama_kandidat',
        'nama_hrd',
        'aspek_teknis',
        'aspek_komunikasi',
        'aspek_sikap',
        'aspek_motivasi',
        'total',
        'catatan',
    ];

    protected $casts = [
        'aspek_teknis'     => 'integer',
        'aspek_komunikasi' => 'integer',
        'aspek_sikap'      => 'integer',
        'aspek_motivasi'   => 'integer',
        'total'            => 'integer',
    ];

    protected static function booted(): void
    {
        // Total selalu dihitung ulang dari 4 aspek (sama seperti simpan.php lama)
        static::saving(function (Penilaian $p) {
            $p->total = (int) $p->aspek_teknis
                + (int) $p->aspek_komunikasi
                + (int) $p->aspek_sikap
                + (int) $p->aspek_motivasi;
        });
    }

    public static function kuota(string $divisi): int
    {
        return (int) config("hrd.divisi.{$divisi}.kuota", 0);
    }

    public function getTierAttribute(): string
    {
        return match (true) {
            $this->total >= 90 => 'S',
            $this->total >= 80 => 'A',
            $this->total >= 70 => 'B',
            $this->total >= 60 => 'C',
            default            => 'D',
        };
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', trim($this->nama_kandidat)))
            ->filter()
            ->take(2)
            ->map(fn ($w) => Str::upper(Str::substr($w, 0, 1)))
            ->implode('');
    }
}
