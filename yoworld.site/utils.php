<?php

class Utils
{
    public function get_item_count(): int
    {
        $file_data = file_get_contents("items.txt");
        $lines = explode("\n", $file_data);
        return count($lines);
    }
    public function log_visitor($ip): void
    {
        $visitorDB = fopen("visitor_logs.txt", "a");
        fwrite($visitorDB, "('$ip')\n");
        fclose($visitorDB);
    }

    public function get_search_count(): int
    {
        $file = file_get_contents("search_logs.txt");
        $lines = explode("\n", $file);
        return count($lines);
    }

    public function get_visitor_count(): int
    {
        $file = file_get_contents("visitor_logs.txt");
        $lines = explode("\n", $file);
        return count($lines);
    }
}