<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Services\RankingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    public function index(Request $request)
    {
        $regional = $this->currentRegional($request);

        $counts = Penilaian::selectRaw('regional, count(*) as n')
            ->groupBy('regional')
            ->pluck('n', 'regional');

        return view('unduh', compact('regional', 'counts'));
    }

    /** Ambil data + waktu unduh saat ini. */
    private function dataset(Request $request): array
    {
        $request->validate([
            'regional' => ['required', Rule::in(array_merge(['semua'], config('hrd.regionals')))],
        ]);

        $regional = $request->query('regional');

        $rows = Penilaian::query()
            ->when($regional !== 'semua', fn ($q) => $q->where('regional', $regional))
            ->get();

        $rows  = RankingService::sorted($rows);
        $ranks = RankingService::ranks($rows);

        $now   = now();
        $label = $regional === 'semua' ? 'Semua Regional' : $regional;
        $stamp = $now->translatedFormat('l, d F Y H:i:s').' '.$now->format('T');

        return [$rows, $ranks, $label, $stamp, $now];
    }

    private function filename(string $label, $now, string $ext): string
    {
        return 'Interview_HRD_'.Str::slug($label, '_').'_'.$now->format('Ymd_His').'.'.$ext;
    }

    public function pdf(Request $request)
    {
        [$rows, $ranks, $label, $stamp, $now] = $this->dataset($request);

        $pdf = Pdf::loadView('exports.pdf', compact('rows', 'ranks', 'label', 'stamp'))
            ->setPaper('a4', 'landscape');

        return $pdf->download($this->filename($label, $now, 'pdf'));
    }

    public function excel(Request $request)
    {
        [$rows, $ranks, $label, $stamp, $now] = $this->dataset($request);

        // Hosting tanpa ekstensi zip tidak bisa membuat .xlsx -> beri file Excel (.xls) berbasis tabel
        if (! class_exists(\ZipArchive::class)) {
            return response(
                view('exports.xls', compact('rows', 'ranks', 'label', 'stamp'))->render(),
                200,
                [
                    'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="'.$this->filename($label, $now, 'xls').'"',
                ]
            );
        }

        $heads  = ['No', 'Waktu Input', 'Regional', 'Divisi', 'Peringkat', 'Nama Kandidat', 'HRD',
                   'Teknis', 'Komunikasi', 'Sikap', 'Motivasi', 'Total', 'Status', 'Catatan'];
        $widths = [6, 20, 20, 24, 11, 28, 22, 10, 13, 10, 11, 10, 12, 50];
        $last   = Coordinate::stringFromColumnIndex(count($heads)); // N
        $numericCols = [0, 4, 7, 8, 9, 10, 11];                     // indeks kolom bertipe angka

        $ss = new Spreadsheet();
        $ss->getProperties()->setCreator('Interview HRD')->setTitle('Penilaian Interview HRD');
        $ws = $ss->getActiveSheet();
        $ws->setTitle('Penilaian');

        // Judul + waktu unduh
        $ws->setCellValue('A1', 'LAPORAN PENILAIAN INTERVIEW HRD');
        $ws->setCellValue('A2', 'Youth Ranger Indonesia  |  '.$label);
        $ws->setCellValue('A3', 'Diunduh pada: '.$stamp.'  |  Total data: '.$rows->count().' kandidat');
        foreach ([1, 2, 3] as $r) {
            $ws->mergeCells("A{$r}:{$last}{$r}");
        }
        $ws->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '1B1432']]]);
        $ws->getStyle('A2')->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);
        $ws->getStyle('A3')->applyFromArray(['font' => ['italic' => true, 'color' => ['rgb' => '666666']]]);

        // Header tabel
        $hr = 5;
        foreach ($heads as $i => $h) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $ws->setCellValueExplicit($col.$hr, $h, DataType::TYPE_STRING);
            $ws->getColumnDimension($col)->setWidth($widths[$i]);
        }
        $ws->getStyle("A{$hr}:{$last}{$hr}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B1432']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $ws->getRowDimension($hr)->setRowHeight(26);

        // Isi
        $r = $hr + 1;
        $no = 1;
        foreach ($rows as $row) {
            $rank  = $ranks[$row->id] ?? 0;
            $isTop = $rank > 0 && $rank <= Penilaian::kuota($row->divisi);

            $vals = [
                $no++,
                $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '-',
                $row->regional,
                $row->divisi,
                $rank,
                $row->nama_kandidat,
                $row->nama_hrd,
                $row->aspek_teknis,
                $row->aspek_komunikasi,
                $row->aspek_sikap,
                $row->aspek_motivasi,
                $row->total,
                $isTop ? 'TOP' : '-',
                (string) $row->catatan,
            ];

            foreach ($vals as $i => $v) {
                $cell = Coordinate::stringFromColumnIndex($i + 1).$r;
                if (in_array($i, $numericCols, true)) {
                    $ws->setCellValueExplicit($cell, $v, DataType::TYPE_NUMERIC);
                } else {
                    // diset sebagai teks agar isi seperti "=..." tidak dianggap rumus
                    $ws->setCellValueExplicit($cell, (string) $v, DataType::TYPE_STRING);
                }
            }

            if ($isTop) {
                $ws->getStyle("A{$r}:{$last}{$r}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EDE9FE');
            }
            $r++;
        }

        $endRow = max($r - 1, $hr);

        $ws->getStyle("A{$hr}:{$last}{$endRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDD5']]],
        ]);
        if ($endRow > $hr) {
            $body = "A".($hr + 1).":{$last}{$endRow}";
            $ws->getStyle($body)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
            $ws->getStyle("N".($hr + 1).":N{$endRow}")->getAlignment()->setWrapText(true);
            foreach (['A', 'E', 'H', 'I', 'J', 'K', 'L', 'M'] as $c) {
                $ws->getStyle("{$c}".($hr + 1).":{$c}{$endRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            $ws->getStyle("L".($hr + 1).":L{$endRow}")->getFont()->setBold(true);
            $ws->setAutoFilter("A{$hr}:{$last}{$endRow}");
        }

        $ws->freezePane('A'.($hr + 1));
        $ws->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

        $name = $this->filename($label, $now, 'xlsx');

        return response()->streamDownload(function () use ($ss) {
            (new Xlsx($ss))->save('php://output');
        }, $name, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
