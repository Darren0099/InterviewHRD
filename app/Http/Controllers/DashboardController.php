<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $regional = $this->currentRegional($request);

        $rows = Penilaian::where('regional', $regional)
            ->orderByDesc('total')
            ->orderBy('id')
            ->get();

        $byDivisi = $rows->groupBy('divisi');

        $divisi = collect(config('hrd.divisi'))->map(function ($meta, $nama) use ($byDivisi) {
            $list   = $byDivisi->get($nama, collect());
            $jumlah = $list->count();
            $kuota  = (int) $meta['kuota'];

            return [
                'nama'     => $nama,
                'icon'     => $meta['icon'],
                'color'    => $meta['color'],
                'kuota'    => $kuota,
                'jumlah'   => $jumlah,
                'terisi'   => min($jumlah, $kuota),
                'progress' => $kuota > 0 ? min(100, (int) round($jumlah / $kuota * 100)) : 0,
                'penuh'    => $jumlah >= $kuota,
                'kurang'   => max(0, $kuota - $jumlah),
                'best'     => $list->first(),
                'avg'      => $jumlah ? round($list->avg('total'), 1) : 0,
            ];
        })->values();

        $n         = $rows->count();
        $totalSlot = (int) $divisi->sum('kuota');
        $filled    = (int) $divisi->sum('terisi');
        $penuh     = $divisi->where('penuh', true)->count();

        $stat = [
            'total' => $n,
            'top'   => $filled,
            'slot'  => $totalSlot,
            'avg'   => $n ? round($rows->avg('total'), 1) : 0,
            'penuh' => $penuh,
            'divisi' => $divisi->count(),
            'misi'  => $totalSlot ? (int) round($filled / $totalSlot * 100) : 0,
        ];

        // Gamifikasi: tiap penilaian = +20 XP, tiap 100 XP naik level
        $xp     = $n * 20;
        $lv     = intdiv($xp, 100) + 1;
        $titles = ['Rookie Ranger', 'Scout Ranger', 'Field Ranger', 'Elite Ranger', 'Legend Ranger'];
        $level  = [
            'lv'    => $lv,
            'title' => $titles[min($lv, count($titles)) - 1],
            'xp'    => $xp % 100,
            'total' => $xp,
        ];

        $badges = [
            ['icon' => 'send', 'nama' => 'Langkah Pertama', 'desc' => 'Input 1 penilaian',           'on' => $n >= 1],
            ['icon' => 'zap', 'nama' => 'On Fire',         'desc' => '10 kandidat dinilai',          'on' => $n >= 10],
            ['icon' => 'award', 'nama' => 'Kuota Penuh',     'desc' => 'Minimal 1 divisi penuh',       'on' => $penuh >= 1],
            ['icon' => 'layers', 'nama' => 'Semua Divisi',    'desc' => 'Setiap divisi punya pendaftar', 'on' => $divisi->every(fn ($d) => $d['jumlah'] > 0)],
            ['icon' => 'hexagon', 'nama' => 'Skor 90+',        'desc' => 'Ada kandidat skor 90 ke atas',  'on' => ($rows->max('total') ?? 0) >= 90],
            ['icon' => 'trophy', 'nama' => 'Misi Selesai',    'desc' => 'Semua slot TOP terisi',         'on' => $totalSlot > 0 && $filled >= $totalSlot],
        ];

        $hrBoard = $rows->groupBy('nama_hrd')
            ->map(fn ($g, $nama) => [
                'nama'   => $nama,
                'jumlah' => $g->count(),
                'avg'    => round($g->avg('total'), 1),
            ])
            ->sortByDesc('jumlah')
            ->take(5)
            ->values();

        $best = $rows->first();

        return view('dashboard', compact('regional', 'divisi', 'stat', 'level', 'badges', 'hrBoard', 'best'));
    }
}
