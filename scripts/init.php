<?php

require '../vendor/autoload.php';
require '../lib/PDO_Database.php';
require '../lib/Logger.php';

try {

    // INIT
    $config     = parse_ini_file('../config/config.ini', true);

    $dConf      = (object) $config['database'];
    $fConf      = (object) $config['facebook'];
    $log        = new Logger();
    $db         = new PDO_Database($dConf->user, $dConf->password, $dConf->database, $dConf->host);

    $tables     = json_decode(file_get_contents('../config/tables.json'), true);

    $existing   = [];
    $surplus    = [];
    $result     = $db->query('show tables');

    while ($row = $result->fetch()) {
        $table = $row['Tables_in_' . $dConf->database];
        if (array_key_exists($table, $tables)) {
            $existing[] = $table;
        } else {
            $surplus [] = $table;
        }
    }
    foreach ($tables as $name => $id) {
        if (in_array($name, $existing)) {
            $log->log("skipping past table '" . $name . "' as it already exists");
            continue;
        }

        // Create the table
        if (count($tables[$name]) > 0) {
            $sql = "CREATE TABLE " . $name . " (\n";
            $lines = [];
            foreach ($tables[$name] as $field) {
                $lines[] = implode(" ", $field);
            }
            $sql .= implode(",\n", $lines);
            $sql .= "\n)\n";
            $log->log($sql);
            $result = $db->query($sql);
            $log->log($result);
        } else {
            $log->log('No fields found for table ' . $name);
        }
    }

    $fb = new Facebook\Facebook([
      'app_id' => $fConf->appId,
      'app_secret' => $fConf->appSecret,
      'default_graph_version' => 'v2.4',
      'default_access_token' => $fConf->accessToken,
    ]);

    // Get the Facebook\GraphNodes\GraphUser object for the current user.
    // If you provided a 'default_access_token', the '{access-token}' is optional.
    $response = $fb->get($fConf->groupId . '/feed?limit=9999&fields=id,caption,created_time,description,from,icon,link,name,message,message_tags,picture,source,type,updated_time');
    $posts = $response->getGraphEdge();
    $count = 1;
    foreach ($posts as $post) {
        // Grab the inner array
        $params = (array)$post;
	    $params = $params[key($params)];

        $site = "other";
        if (strpos($url, "youtube") !== false) {
            $site = "youtube";
        } elseif (strpos($url, 'vimeo') !== false) {
            $site = "vimeo";
        }
        // Remove any get params
        $url = explode('?', $url)[0];
        $params['source'] = $url;
        $params['site'] = $site;
        try {
            $db->insertFromFb('posts', $params);
        } catch (PDOException $e) {
            // Check this isn't a primary key issue
            if ($e->errorInfo[1] == 1062) {
                // Just a duplicate so log and continue
                $log->log('Found Duplicate for `' . $params['id'] . '`');
            } else {
                throw $e;
            }
        }
    }

} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage() . "\n";
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage() . "\n";
    exit;
} catch (PDOException $e) {
    var_dump($e);
    $log->log('PDO Error - ' . $e->getMessage());
    exit;
}
