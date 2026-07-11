<?php
require_once 'core.php';
require_once __DIR__ . '/../core/backup.php';

$result = create_sqlite_backup($connect);

$response = [
    'success' => $result['success'],
    'messages' => $result['message']
];

if ($result['success']) {
    $response['backup_file'] = $result['file'];
}

echo json_encode($response);
