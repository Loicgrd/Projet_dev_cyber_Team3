<?php
/**
 * Dispatcher
 * @author Aélion <jean-luc.aubert@aelion.fr>
 * @version 1.0.0
 *  - Instanciate Kernel
 *  - Generate Response object
 *  - Send Http Response to web server
*/
require_once('./../vendor/autoload.php');

use Aelion\Kernel;

ini_set('display_errors', 1);
error_reporting(E_ALL^E_NOTICE);


$kernel = Kernel::create();
$response = $kernel->processRequest();
echo $response->send();


