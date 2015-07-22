<?php

/**
 * 
 */
class PDO_Database
{
    private $connection = false;

    /**
     * Constructor for  our PDO DB wrapper
     * pass in user/pass/database/host and we then setup the connection
     * and we make sure to turn on exception handling
     *
     * @param  String           $username
     * @param  String           $password
     * @param  String           $database
     * @param  boolean|string   $host
     * @author Phil Burton <phil@d3r.com>
     * @throws
     */
    public function __construct($username, $password, $database, $host = false)
    {
        if (!$host) {
            $host = 'localhost';
        }

        $this->connection = new PDO('mysql:host=' . $host . ';dbname=' . $database, $username, $password);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Simple query function that uses prepared statements to set-up a query
     * and then executes the command, returning the pdo query.
     *
     * @param  String $sql
     * @param  array  $params
     * @return PDO Query
     * @author Phil Burton <phil@d3r.com>
     */
    public function query($sql, $params = array())
    {
        $query = $this->connection->prepare($sql);
        $query->execute($params);
        return $query;
    }

    /**
     * Insert into a table using a facebook returned array as the data
     *
     * @param  String   $table
     * @param  array    $params
     * @return PDO Query
     * @author Phil Burton <phil@d3r.com>
     */
    public function insertFromFb($table, $params)
    {
        // Grab the inner array
        $params = $params[key($params)];

        // For values map each elem so we can handle dates/graphnodes properly
        $values = array_map(
            function ($elem) {
                if ($elem instanceof DateTime) {
                    return $elem->format('Y-m-d H:i:s');
                }
                if ($elem instanceof Facebook\GraphNodes\GraphNode) {
                    return 'test';
                }
                return $elem;
            },
            $params
        );

        // Map keys so we avoid using mysql resereved keywords
        $keys = array_map(
            function ($elem) {
                return "`" . $elem . "`";
            },
            array_keys($values)
        );

        // Map our query parameters foo becomes :foo
        $pars = array_map(
            function ($elem) {
                return ":" . $elem;
            },
            array_keys($values)
        );

        // build the set values
        $sql  = "INSERT INTO " . $table . "\n";
        $sql .= "(" . implode(", ", $keys) . ")\n";
        $sql .= "VALUES (" . implode(", ", $pars) .  ")\n";

        return $this->query($sql, $values);
    }
}
