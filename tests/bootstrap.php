<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author kisiel
 */
// TODO: check include path
//ini_set('include_path', ini_get('include_path'));
//$vendor = realpath(__DIR__ . '/../vendor');
//
//if (file_exists($vendor . "/autoload.php")) {
//    require $vendor . "/autoload.php";
//} else {
//    $vendor = realpath(__DIR__ . '/../../../');
//    if (file_exists($vendor . "/autoload.php")) {
//        require $vendor . "/autoload.php";
//    } else {
//        throw new Exception("Unable to load dependencies");
//    }
//}
define('SHELL_CORE_PATH', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));
include "../vendor/autoload.php";
spl_autoload_register(function ($className) {
    $fileName = implode(DIRECTORY_SEPARATOR, [
        SHELL_CORE_PATH,"src",str_replace("\\", DIRECTORY_SEPARATOR, $className) . '.php'
    ]);
	
    if (file_exists($fileName)) {
        if (is_readable($fileName)) {
            echo $fileName." ok\n";
            require_once $fileName;
        } else {

        }
    } else {
        echo $fileName." dont exists\n";
    }
});

define("SERVER_IP","127.0.0.1");
define("SERVER_PORT","8080");
define("SERVER_PATH","/");
?>
