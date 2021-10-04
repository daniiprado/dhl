<?php

use App\Helpers\DotEnv;

$path = dirname('').'.env';
(new DotEnv($path))->load();