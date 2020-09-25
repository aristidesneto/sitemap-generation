<?php

include __DIR__ . '/vendor/autoload.php';

use Aristides\Sitemap\Sitemap;

$url = 'http://localhost';

$sitemap = new Sitemap();

for ($i = 1; $i <= 10; $i++) {
    $sitemap->add($url);
}
var_dump($sitemap);
$sitemap->write();
