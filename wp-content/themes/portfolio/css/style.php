<?php

$directory = dirname(__FILE__);
require 'scssphp/scss.inc.php';

$scss = new scssc();
$scss->setFormatter("scss_formatter_compressed");

$server = new scss_server($directory, null, $scss);
$server->serve();