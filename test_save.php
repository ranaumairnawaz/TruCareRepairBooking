<?php
require 'api/db.php';
try {
    $current = getSetting('public');
    if (!$current) $current = [];
    $current['services'][0]['image'] = '/test.png';
    setSetting('public', $current);
    echo "SAVE SUCCESS\n";
    $c2 = getSetting('public');
    echo "IMAGE IS: " . $c2['services'][0]['image'] . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
