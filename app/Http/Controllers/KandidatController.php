<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Services\RankingService;
use Illuminate\Http\Request;

class KandidatController extends Controller
{
    public function index(Request $request)
    {
        $regional = $this->currentRegional($request);

        $rows = Penilaian::where('regional', $regional)
            ->orderByDesc('total')
            ->orderBy('id')
            ->get();

        $ranks = RankingService::ranks($rows);

        // hitung jumlah per divisi untuk chip filter
        $counts = $rows->groupBy('divisi')->map->count();

        return view('kandidat', compact('regional', 'rows', 'ranks', 'counts'));
    }
}
