<?php

class Logger
{
    private $cli        = false;
    private $filename   = false;

    public function __construct($cli = true, $filename = false)
    {
        if ($cli) {
            $this->cli = true;
        }

        if ($filename) {
            $this->filename = $filename;
        }
    }

    public function log($obj)
    {
        if (!is_array($obj)) {
            $obj = [$obj];
        }

        if ($this->cli) {
            print_r($obj);
        }

        if ($this->filename) {
            foreach ($this->obj as $o) {
                file_put_contents($this->filename, $o);
            }
        }
    }
}
