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
        "SELECT source, picture
         FROM   posts
         WHERE  source IS NOT NULL
	LIMIT 3"
    );
    $count = 1;
    echo '<html>';
    echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>';
    echo "<script>
	$(document).ready(function() {
	    $('img').click(function() {
		id = this.id;
		$.ajax({
		    url: 'api.php?' + id,
		}).done(function(data) {
		   $('#div-' + id).empty();
		   $('#div-' + id).append(data);
		});
	    });
	});
	</script>";

    echo '<body>';
    while ($row = $result->fetch()) {
        $url = $row['source'];

        if (strpos($url, "youtube")) {
            getYoutubeHtml($count, $url, $row['picture']);
        } elseif (strpos($url, 'vimeo')) {
            getVimeoHtml($count, $url, $row['picture']);
        }
	$count++;
    }
    echo '</body></html>';
} catch (PDOException $e) {
    var_dump($e);
    $log->log('PDO Error - ' . $e->getMessage());
    exit;
}

function getYoutubeHtml($id, $url, $img) {
echo '<div id="div-' . $id . '">';
echo '<img id="' . $id . '" src="' . $img . '" height="200" width="300">';
//echo '<iframe id="' . $id . '" type="text/html" width="300" height="200" src="' . $url . '?controls=0&enablejsapi=1&autohide=1&showinfo=0" frameborder="0" allowfullscreen autoplay="0"></iframe>';
echo '</div>';
}

function getVimeoHtml($id, $url, $img) {
echo '<div id="div-' . $id . '">';
echo '<img id="' . $id . '" src="' . $img . '" height="200" width="300">';
//echo '<iframe id="' . $id . '" src="' . $url . '?autoplay=0&badge=0&byline=0&title=0" height="200" width="300" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
echo '</div>';
}
