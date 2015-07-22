<?php

require '../vendor/autoload.php';
require '../lib/PDO_Database.php';
require '../lib/Logger.php';

try {

    // INIT
    $config     = parse_ini_file('../config/config.ini', true);

    $dConf      = (object) $config['database'];
    $log        = new Logger(false, '../logs/runtime-errors.php_logo_guid(oid)');
    $db         = new PDO_Database($dConf->user, $dConf->password, $dConf->database, $dConf->host);

    $existing   = [];
    $surplus    = [];
    $result     = $db->query(
        "SELECT source 
         FROM   posts 
         WHERE  source IS NOT NULL
         LIMIT  3
        ");

    while ($row = $result->fetch()) {
        $url = $row['source'];

        if (strpos($url, "youtube")) {
            getYoutubeHtml($url);
        } elseif (stros($url, 'vimeo')) {
            getVimeoHtml($url);
        } else {
            continue;
        }

    }
} catch (PDOException $e) {
    var_dump($e);
    $log->log('PDO Error - ' . $e->getMessage());
    exit;
}

function getYoutubeHtml($url) {
    echo "getYoutubeHtml";
}

function getVimeoHtml($url) {
    echo "getVimeoHtml";
}