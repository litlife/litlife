<?php

error_reporting(E_ALL & ~E_NOTICE);
// Assuming default values for session.upload_progress.prefix
// and session.upload_progress.name:
$s = $_SESSION['upload_progress_'.intval($_GET['PHP_SESSION_UPLOAD_PROGRESS'])];
$progress = array(
    'lengthComputable' => true,
    'loaded' => $s['bytes_processed'],
    'total' => $s['content_length']
);
echo json_encode($progress);
?>