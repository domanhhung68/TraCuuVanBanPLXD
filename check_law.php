<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$law = App\Models\Law::find(3);
if ($law) {
    echo $law->id . PHP_EOL;
    echo $law->title . PHP_EOL;
} else {
    echo 'NOT_FOUND';
}
