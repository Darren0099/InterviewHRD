<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    /** Regional aktif: dari ?regional=..., lalu diingat di session. */
    protected function currentRegional(Request $request): string
    {
        $list = config('hrd.regionals');
        $reg  = $request->query('regional', session('regional', $list[0]));

        if (! in_array($reg, $list, true)) {
            $reg = $list[0];
        }

        session(['regional' => $reg]);

        return $reg;
    }
}
