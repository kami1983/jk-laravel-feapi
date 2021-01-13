<?php

namespace Tests;

use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application ;

trait CreatesApplication
{
  /**
   * Creates the application.
   *
   * @return \Illuminate\Foundation\Application
   */
  public function createApplication()
  {
      $app = require __DIR__ . '/bootstrap/app.php';

//    $app = new Application(
//      realpath(__DIR__ . '/')
//    );

//    $app->make(Kernel::class)->bootstrap();

    Hash::setRounds(4);

    return $app;
  }
}
