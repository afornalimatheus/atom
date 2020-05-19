<?php

date_default_timezone_set('America/Fortaleza');

require_once __DIR__ . '/../vendor/autoload.php';

function d($data, $type = 'var_dump', $stop = true)
{
    echo '<pre>';
    $type($data);
    echo '</pre>';

    if ($stop) {
        exit();
    }
}

function met($data)
{
    echo '<pre>';
    var_dump(get_class_methods(get_class($data)));
    echo '</pre>';
    exit();
}

$app = new \App\LocalApplication();
$app->run();