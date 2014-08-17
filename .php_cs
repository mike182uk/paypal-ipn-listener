<?php

use Symfony\CS\Config\Config;
use Symfony\CS\Finder\DefaultFinder;

return Config::create()->finder(
    DefaultFinder::create()
        ->in(__DIR__ . '/src')
);