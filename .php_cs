<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__ . '/src')
;

return Symfony\CS\Config\Config::create()
    ->finder($finder)
;