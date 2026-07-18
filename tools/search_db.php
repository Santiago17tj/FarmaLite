<?php
$db = new PDO('sqlite:www/farmacia.db');
$tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll();
foreach ($tables as $row) {
    $table = $row['name'];
    $cols = $db->query("PRAGMA table_info($table)")->fetchAll();
    foreach ($cols as $col) {
        $colName = $col['name'];
        $res = $db->query("SELECT * FROM $table WHERE $colName LIKE '%Project Develop%'")->fetchAll();
        if (count($res) > 0) {
            echo "Found in $table.$colName\n";
        }
    }
}
echo "Done searching DB\n";
