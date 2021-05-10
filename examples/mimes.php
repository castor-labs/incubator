<?php

require_once __DIR__ . '/../vendor/autoload.php';

$registry = Castor\Mime\DefaultRegistry::get();

$mime = $registry->getExtensions('application/pdf');