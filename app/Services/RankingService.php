<?php

namespace App\Services;

use Illuminate\Support\Collection;

class RankingService
{
    /**
     * Peringkat tiap kandidat di dalam (regional + divisi).
     * Urutan: total terbesar dulu; kalau seri, yang diinput lebih dulu menang.
     *
     * @return array<int,int> [id => peringkat]
     */
    public static function ranks(Collection $rows): array
    {
        $ranks = [];

        foreach ($rows->groupBy(fn ($r) => $r->regional.'|'.$r->divisi) as $group) {
            $i = 0;
            foreach ($group->sort(fn ($a, $b) => [$b->total, $a->id] <=> [$a->total, $b->id]) as $r) {
                $ranks[$r->id] = ++$i;
            }
        }

        return $ranks;
    }

    /** Urutkan: regional -> divisi (urutan config) -> total desc -> id. */
    public static function sorted(Collection $rows): Collection
    {
        $reg = array_flip(config('hrd.regionals'));
        $div = array_flip(array_keys(config('hrd.divisi')));

        return $rows->sort(function ($a, $b) use ($reg, $div) {
            return [$reg[$a->regional] ?? 99, $div[$a->divisi] ?? 99, $b->total, $a->id]
               <=> [$reg[$b->regional] ?? 99, $div[$b->divisi] ?? 99, $a->total, $b->id];
        })->values();
    }
}
