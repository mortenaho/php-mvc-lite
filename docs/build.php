<?php

declare(strict_types=1);

$layout = file_get_contents(__DIR__ . '/_layout.html');
$pages = require __DIR__ . '/_pages.php';

foreach ($pages as $file => $page) {
    $prev = $page['prev'] ?? null;
    $next = $page['next'] ?? null;

    $pager = '';
    if ($prev) {
        $pager .= '<a href="' . $prev[0] . '">← ' . $prev[1] . '</a>';
    } else {
        $pager .= '<span></span>';
    }
    if ($next) {
        $pager .= '<a href="' . $next[0] . '">' . $next[1] . ' →</a>';
    }

    $html = str_replace(
        ['__TITLE__', '__CONTENT__', '__PAGER__'],
        [$page['title'], $page['content'], $pager],
        $layout,
    );

    file_put_contents(__DIR__ . '/' . $file, $html);
    echo "Wrote {$file}\n";
}
