<?php

namespace Aerni\Snipcart\Http\Controllers\Cp;

use Statamic\Http\Controllers\CP\CpController;

class ProductController extends CpController
{
    public function index()
    {
        return view('snipcart::cp.products.index');
    }
}