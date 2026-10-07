<?php
$f = 'assets/js/admin.js';
$c = file_get_contents($f);
$c = str_replace('#16a34a', 'var(--primary-color)', $c);
$c = str_replace('#15803d', 'var(--secondary-color)', $c);
$c = str_replace('rgba(22,163,74,.3)', 'rgba(0,0,0,0.1)', $c);
$c = str_replace('#dcfce7', 'var(--primary-light)', $c);
$c = str_replace('text-green-700', 'text-[var(--primary-color)]', $c);
$c = str_replace('text-green-600', 'text-[var(--primary-color)]', $c);
$c = str_replace('bg-green-600', 'bg-[var(--primary-color)]', $c);
$c = str_replace('text-green-400', 'text-[var(--primary-light)]', $c);
$c = str_replace('&background=16a34a', '&background=000000', $c);
file_put_contents($f, $c);
echo "Done";
