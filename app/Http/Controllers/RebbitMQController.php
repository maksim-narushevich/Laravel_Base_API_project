<?php

namespace App\Http\Controllers;


use App\Jobs\TestJob;
use App\Models\Product;


class RebbitMQController extends Controller
{


    protected function dispatch()
    {
        $product=Product::findOrFail(10);
        //dispatch test job
        TestJob::dispatch($product);
    }

}
