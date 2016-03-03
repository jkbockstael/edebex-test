<?php
namespace EdebexTest;

class WorkweekParser
{
    // Get workweek from a JSON file
    public static function fromFile($json_file)
    {
        return json_decode(file_get_contents($json_file), true);
    }
}

