<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Services\RankingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenilaianController extends Controller
{
    private function rules(): array
    {
        $max = (int) config('hrd.max_score', 25);

        return [
            'regional'         => ['required', Rule::in(config('hrd.regionals'))],
            'divisi'           => ['required', Rule::in(array_keys(config('hrd.divisi')))],
            'nama_kandidat'    => ['required', 'string', 'max:150'],
            'nama_hrd'         => ['required', 'string', 'max:150'],
            'aspek_teknis'     => ['required', 'integer', "between:0,{$max}"],
            'aspek_komunikasi' => ['required', 'integer', "between:0,{$max}"],
            'aspek_sikap'      => ['required', 'integer', "between:0,{$max}"],
            'aspek_motivasi'   => ['required', 'integer', "between:0,{$max}"],
            'catatan'          => ['nullable', 'string', 'max:2000'],
        ];
    }

    private function result(Penilaian $p, string $message): JsonResponse
    {
        $rows  = Penilaian::where('regional', $p->regional)->where('divisi', $p->divisi)->get();
        $rank  = RankingService::ranks($rows)[$p->id] ?? null;
        $kuota = Penilaian::kuota($p->divisi);

        return response()->json([
            'ok'      => true,
            'message' => $message,
            'nama'    => $p->nama_kandidat,
            'divisi'  => $p->divisi,
            'total'   => $p->total,
            'tier'    => $p->tier,
            'rank'    => $rank,
            'kuota'   => $kuota,
            'in_top'  => $rank !== null && $rank <= $kuota,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $p = Penilaian::create($request->validate($this->rules()));

        return $this->result($p, 'Penilaian berhasil disimpan');
    }

    public function update(Request $request, Penilaian $penilaian): JsonResponse
    {
        $penilaian->update($request->validate($this->rules()));

        return $this->result($penilaian->fresh(), 'Penilaian berhasil diperbarui');
    }

    public function destroy(Penilaian $penilaian): JsonResponse
    {
        $penilaian->delete();

        return response()->json(['ok' => true, 'message' => 'Data kandidat dihapus']);
    }

    public function destroyRegional(Request $request): JsonResponse
    {
        $data = $request->validate([
            'regional' => ['required', Rule::in(config('hrd.regionals'))],
        ]);

        $n = Penilaian::where('regional', $data['regional'])->delete();

        return response()->json(['ok' => true, 'message' => "{$n} data regional {$data['regional']} dihapus"]);
    }

    /** Pengganti top_divisi.php: daftar kandidat TOP satu divisi (HTML parsial untuk popup). */
    public function top(Request $request)
    {
        $regional = $request->query('regional');
        $divisi   = $request->query('divisi');

        abort_unless(in_array($regional, config('hrd.regionals'), true), 422);
        abort_unless(array_key_exists($divisi, config('hrd.divisi')), 422);

        $kuota = Penilaian::kuota($divisi);

        $list = Penilaian::where('regional', $regional)
            ->where('divisi', $divisi)
            ->orderByDesc('total')
            ->orderBy('id')
            ->limit($kuota)
            ->get();

        return view('partials.top-list', compact('list', 'kuota', 'divisi', 'regional'));
    }
}
