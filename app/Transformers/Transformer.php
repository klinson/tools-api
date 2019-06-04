<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class Transformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform($model)
    {
        return json_decode(json_encode($model), true);
    }
}