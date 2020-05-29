<?php
/**
 * User: qbhy
 * Date: 2019/1/16
 * Time: 下午2:14
 */

return [
    'use'          => env('BAIDU_AIP_USE', 'default'),
    'debug'        => env('BAIDU_AIP_DEBUG', false),
    'applications' => [
        'default' => [
            'app_id'     => env('BAIDU_AIP_APP_ID'),
            'api_key'    => env('BAIDU_AIP_API_KEY'),
            'secret_key' => env('BAIDU_AIP_SECRET_KEY'),
        ],
        'nlp'  => [
            'app_id'     => env('BAIDU_AIP_NLP_APP_ID'),
            'api_key'    => env('BAIDU_AIP_NLP_API_KEY'),
            'secret_key' => env('BAIDU_AIP_NLP_SECRET_KEY'),
        ],
    ]
];