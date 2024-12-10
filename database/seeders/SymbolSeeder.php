<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Symbol;

class SymbolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $symbols = [
            'sunce', 'ruža', 'ptica', 'ključ', 'mesec', 'cvet lotosa', 'duga', 'novčić', 'beli golub', 'srce',
            'anđeo', 'zvezda', 'feniks', 'puzavica', 'biser', 'planina', 'osmeh', 'školjka', 'detelina sa 4 lista', 'vodopad',
            'delfin', 'vitez', 'medalja', 'brod', 'pčela', 'kruna', 'zvono', 'vatra', 'dragulj', 'lav',
            'stabljika pšenice', 'drvo života', 'mesečina', 'olimpijski plamen', 'šišmiš', 'biljka aloje', 'izvor',
            'vetrenjača', 'trešnjin cvet', 'čarobna lampa', 'slamka', 'sat', 'lopta za balansiranje', 'beskonačnost',
            'munja', 'krov', 'ljubičica', 'leptir', 'sveća', 'zvezdani put',
            'perla na ogrlici', 'slon', 'orao', 'mačka', 'zec', 'medved', 'sova', 'vuk', 'vrabac', 'dabar',
            'lasta', 'auto', 'bicikl', 'avion', 'brodić', 'voz', 'motor', 'konj', 'tramvaj', 'raketa',
            'ajkula', 'ananas', 'balon', 'brkovi', 'cipela', 'crkva', 'čajnik', 'čarapa', 'čekić', 'drvo',
            'dugme', 'gitara', 'jabuka', 'jaje', 'kišobran', 'kuća', 'lampa', 'list', 'mač', 'miš',
            'kašika', 'palma', 'vaga', 'zmaj', 'žaba',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
        ];

        $symbol_codes = [];
        $total_symbols = count($symbols);

        for ($i = 0; $i < $total_symbols; $i++) {
            $symbol_name = $symbols[$i];

            if ($i < 50) {
                $code = 'a-' . str_pad($i + 1, 2, '0', STR_PAD_LEFT);
            } else {
                $code = 'b-' . str_pad($i - 49, 2, '0', STR_PAD_LEFT);
            }

            // Dodaj simbol i njegov code u novi niz
            $symbol_codes[] = [
                'name' => $symbol_name,
                'code' => $code
            ];
        }

        foreach ($symbol_codes as $symbol) {
            Symbol::create($symbol);
        }
    }
}
