<?php

namespace Aerni\Snipcart\Http\Controllers\CP;

use Aerni\Snipcart\Facades\Orders;
use Statamic\Http\Controllers\CP\CpController;

class OverviewController extends CpController
{
    public function index()
    {
        return view('snipcart::cp.overview.index', [
            'number' => Orders::number(),
            'sales' => Orders::sales(),
        ]);
    }
}
