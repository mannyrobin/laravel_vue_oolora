<?php

spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'WhichBrowser\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/src/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});



function parseUA() {
  return new WhichBrowser\Parser(getallheaders());
}



/*
Polyfill for the getallheaders() function.
*/
if(!function_exists("getallheaders")) {
  function getallheaders() {
    $headers = [];
    foreach ($_SERVER as $name => $value) {
      if(substr($name, 0, 5) == "HTTP_") {
        $headers[str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($name, 5)))))] = $value;
      }
    }
    return $headers;
  }
}
