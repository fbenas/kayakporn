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
        "SELECT uid, source, picture, site
         FROM   posts
         WHERE  source IS NOT NULL
	     LIMIT 100"
    );
    echo '<html>';
    echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>';
    echo '<script src="../js/index.js"></script>';
    echo '<link href="../css/index.css" type="text/css" rel="stylesheet"/>';

    echo '<body>';
    while ($row = $result->fetch()) {
        $url = $row['source'];
        if ($row['site'] != 'other') {
            getVimeoHtml($row['uid'], $url, $row['picture']);
        }
    }
    echo '</body></html>';
} catch (PDOException $e) {
    var_dump($e);
    $log->log('PDO Error - ' . $e->getMessage());
    exit;
}


function getVimeoHtml($id, $url, $img) {
    echo '<div id="div-' . $id . '">';
    echo '<img title="' . $url . '" id="' . $id . '" src="' . $img . '" height="200" width="300">';
    echo '</div>';
}
