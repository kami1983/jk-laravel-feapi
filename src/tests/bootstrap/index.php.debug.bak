<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/../../../vendor/autoload.php';

$app = require_once __DIR__.'/app.php';

$app->make(Kernel::class)->bootstrap();

Hash::setRounds(4);

echo 'RUN GO 2 ';
