<?php

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
         WHERE  source IS NOT NULL"
    );

    $count = 1;
    while ($row = $result->fetch()) {
        $url = $row['source'];

        if (strpos($url, "youtube")) {
            echo getYoutubeHtml($count, $url);
        } elseif (stros($url, 'vimeo')) {
            echo getVimeoHtml($count, $url);
        } else {
            continue;
        }

    }
} catch (PDOException $e) {
    var_dump($e);
    $log->log('PDO Error - ' . $e->getMessage());
    exit;
}

function getYoutubeHtml($id, $url) {
    $iframe = 
    '<iframe id="ytplayer" type="text/html" width="720" height="405" src=' . $url . ' controls=0&enablejsapi=1&autohide=1" frameborder="0" allowfullscreen>';
    getHtml();
}

function getVimeoHtml($id, $url) {
    return getHtml();
}