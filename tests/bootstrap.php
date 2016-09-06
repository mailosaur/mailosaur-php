<?php
if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}
$loader = include __DIR__.'/../vendor/autoload.php';