<?php

namespace Database\Seeders;

use App\Models\Penilaian;
use Illuminate\Database\Seeder;

/** Data contoh (opsional) supaya dashboard langsung terlihat hidup. */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $nama = ['Aditya', 'Bella', 'Citra', 'Dimas', 'Eka', 'Fajar', 'Gita', 'Hendra', 'Intan', 'Joko',
                 'Kiki', 'Lestari', 'Maulana', 'Nadia', 'Oki', 'Putri', 'Rizky', 'Salsa', 'Tegar', 'Wulan'];
        $belakang = ['Pratama', 'Saputra', 'Wijaya', 'Lestari', 'Ramadhan', 'Putri', 'Hidayat', 'Nugraha'];
        $hrd = ['Darren', 'Almira', 'Rafli'];
        $divisi = array_keys(config('hrd.divisi'));

        mt_srand(7);

        for ($i = 0; $i < 45; $i++) {
            Penilaian::create([
                'regional'         => config('hrd.regionals')[0],
                'divisi'           => $divisi[$i % count($divisi)],
                'nama_kandidat'    => $nama[array_rand($nama)].' '.$belakang[array_rand($belakang)],
                'nama_hrd'         => $hrd[array_rand($hrd)],
                'aspek_teknis'     => mt_rand(12, 25),
                'aspek_komunikasi' => mt_rand(12, 25),
                'aspek_sikap'      => mt_rand(14, 25),
                'aspek_motivasi'   => mt_rand(13, 25),
                'catatan'          => ['Komunikatif dan percaya diri.', 'Perlu pendalaman skill teknis.', 'Motivasi tinggi, siap belajar.', null][$i % 4],
            ]);
        }
    }
}
