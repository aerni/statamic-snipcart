<?php

namespace Aerni\Snipcart\Http\Controllers\CP;

use Statamic\Http\Controllers\CP\CpController;

class OrderController extends CpController
{
    public function index()
    {
        return view('snipcart::cp.orders.index');
    }
}