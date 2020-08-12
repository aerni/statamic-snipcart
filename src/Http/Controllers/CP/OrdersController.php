<?php

namespace Aerni\Snipcart\Http\Controllers\CP;

use Aerni\Snipcart\Facades\Orders;
use Statamic\Http\Controllers\CP\CpController;

class OrdersController extends CpController
{
    public function index()
    {
        return view('snipcart::cp.orders.index', [
            'orders' => Orders::overview()
        ]);
    }
}