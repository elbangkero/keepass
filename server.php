<?php

// Define passwords and corresponding directories
$passwords = [
    "cs123" => "cs",
    "dev123" => "dev",
    "itss123" => "itss",
    "qa123" => "qa"
];

// Path to kdbx databases
define("BASE_PATH", __DIR__ . "/databases");

function require_authorization($password) {
    global $passwords;

    // Check if password is valid
    if (!array_key_exists($password, $passwords)) {
        header('HTTP/1.0 401 Unauthorized');
        die ("Not authorized");
    }

    
}

if (isset($_GET["path"])) {
    $password = !empty($_SERVER["HTTP_AUTHORIZATION"]) ? $_SERVER["HTTP_AUTHORIZATION"] : "";
    require_authorization($password);

    $directory = $passwords[$password];
    $files = glob(BASE_PATH . "/$directory/*.kdbx");
    $list = [];
    foreach($files as $file) {
        $list[] = [
            "name" => basename($file),
            "path" => basename($file),
            "rev" => filemtime($file),
            "dir" => false
        ];
    }
    header("Content-Type: text/json");
    print json_encode($list);
    return;
}

if (isset($_GET["file"])) {
    $password = !empty($_SERVER["HTTP_AUTHORIZATION"]) ? $_SERVER["HTTP_AUTHORIZATION"] : "";
    require_authorization($password);

    $directory = $passwords[$password];
    $file = BASE_PATH . "/$directory/" . clean_filename($_GET["file"]);
    validate_file($file);

    header("Content-Type: application/binary");
    clearstatcache();
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT', true, 200);
    echo file_get_contents($file);
    return;
}

if (isset($_GET["stat"])) {
    $password = !empty($_SERVER["HTTP_AUTHORIZATION"]) ? $_SERVER["HTTP_AUTHORIZATION"] : "";
    require_authorization($password);

    $directory = $passwords[$password];
    $file = BASE_PATH . "/$directory/" . clean_filename($_GET["stat"]);
    validate_file($file);

    clearstatcache();
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT', true, 200);
    echo "";
    return;
}

if (isset($_GET["save"])) {
    $password = !empty($_SERVER["HTTP_AUTHORIZATION"]) ? $_SERVER["HTTP_AUTHORIZATION"] : "";
    require_authorization($password);

    $directory = $passwords[$password];
    $file = BASE_PATH . "/$directory/" . clean_filename($_GET["save"]);
    validate_file($file);

    $rev = $_GET["rev"];

    clearstatcache();
    $current_rev = gmdate('D, d M Y H:i:s', filemtime($file)).' GMT';

    if ($current_rev !== $rev) {
        header('HTTP/1.0 500 Revision mismatch');
        return;
    }

    $contents = file_get_contents("php://input");
    if (strlen($contents) > 0)
        file_put_contents($file, $contents);

    clearstatcache();
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT', true, 200);
    echo "";
    return;
}

function clean_filename($file) {
    $file = basename($file);
    $file = preg_replace("/[^\w\s\d\-_~,;\[\]\(\)\.]/", '', $file);
    return $file;
}

function validate_file($path) {
    if (!file_exists($path) || is_dir($path)) {
        header('HTTP/1.0 404 Not Found');
        die("Not Found");
    }
}

