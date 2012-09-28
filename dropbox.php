<?php

/**
 * A bootstrap for the Dropbox SDK usage examples
* @link https://github.com/BenTheDesigner/Dropbox/tree/master/examples
*/

// Prevent access via command line interface
if (PHP_SAPI === 'cli') {
    exit('bootstrap.php must not be run via the command line interface');
}

// Don't allow direct access to the bootstrap
if(basename($_SERVER['REQUEST_URI']) == 'dropbox.php'){
    exit('bootstrap.php does nothing on its own. Please see the examples provided');
}

//// Set error reporting
//error_reporting(-1);
//ini_set('display_errors', 'On');
//ini_set('html_errors', 'On');

// Register a simple autoload function
spl_autoload_register(function($class){
    $class = str_replace('\\', '/', $class);
    require_once('Dropbox/' . $class . '.php');
});

function make_dropbox_api($key, $secret, $userID = 1) {
    
    // Check whether to use HTTPS and set the callback URL
    $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
    $callback = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    
    // Instantiate the Encrypter and storage objects
    $encrypter = new \Dropbox\OAuth\Storage\Encrypter('XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
    $storage = new \Dropbox\OAuth\Storage\Filesystem($encrypter, $userID);
    $storage->setDirectory('tokens');
    
    // Instantiate the filesystem store and set the token directory
    // Note: If you use this, comment out line 39
    //$storage = new \Dropbox\OAuth\Storage\Filesystem($encrypter, $userID);
    //$storage->setDirectory('tokens');
    
    // Instantiate the database data store and connect
    // Note: If you use this, comment out line 39, 46 and 47
    // Note: Ensure you import oauth_tokens.sql to your database
    //$storage = new \Dropbox\OAuth\Storage\PDO($encrypter, $userID);
    //$storage->connect('localhost', 'dropbox', 'username', 'password', 3306);
    
    $OAuth = new \Dropbox\OAuth\Consumer\Curl($key, $secret, $storage, $callback);
    $dropbox = new \Dropbox\API($OAuth);
    
    return $dropbox;
}