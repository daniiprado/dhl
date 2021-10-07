<?php

use App\Helpers\DotEnv;

$path = dirname('').'.env';
(new DotEnv($path))->load();

include './vendor/quickbooks/v3-php-sdk/src/config.php';