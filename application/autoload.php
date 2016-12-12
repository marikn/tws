<?php

spl_autoload_register(function($className)
{
    $namespace = str_replace('\\', '/', __NAMESPACE__);
    $className = str_replace('\\', '/', $className);
    $class = '../src/' . (empty($namespace) ? '' : $namespace . '/') . "$className.php";

    include_once($class);
});