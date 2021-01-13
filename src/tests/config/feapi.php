<?php

return [

    /*
    |--------------------------------------------------------------------------
    | FEApi 配置文件
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    // apps 配置所有ic参数对应的类，也就是AbsFEApiParamMan 的相关子类
    // 配置方式 ic => 类名
    'apps' => [
        'example' => 'App\JkFEApiLaravel\tests\CFEApiExample',
        'examapi' => \App\CFEExamApi::class,
    ],

];
