<?php

session_start();
require_once('vendor/autoload.php');

\Classes\Cache::put('user', 'Ilon Mask', 1440);
echo \Classes\Cache::get('user');