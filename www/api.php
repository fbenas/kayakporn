<?php
require '../lib/PDO_Database.php';
require '../lib/Logger.php';

if (!isset($_GET['id']) || !($id = $_GET['id'])) {
    exit;
}

// INIT
$config     = parse_ini_file('../config/config.ini', true);

$dConf      = (object) $config['database'];
$log        = new Logger(false, '../logs/runtime-errors.php_logo_guid(oid)');
$db         = new PDO_Database($dConf->user, $dConf->password, $dConf->database, $dConf->host);

$result     = $db->query(
    "SELECT source, site
     FROM   posts
     WHERE  uid = :id
     LIMIT 1"
    , ['id' => $id]
    );
$post = $result->fetch();
if ($post['site'] == 'youtube') {
    echo '<iframe class="hidden" id="' . $id . '" type="text/html" width="300" height="200" src="' . $post['source'] . '?autoplay=1&controls=1&enablejsapi=1&autohide=1&showinfo=0" frameborder="0" allowfullscreen="allowfullscreen" mozallowfullscreen="mozallowfullscreen"></iframe>';
} elseif ($post['site'] == 'vimeo') {
    echo '<iframe class="hidden" id="' . $id . '" src="' . $post['source'] . '?autoplay=1&badge=0&byline=0&title=0" height="200" width="300" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
}



