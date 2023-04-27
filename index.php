<?php

require_once "NewsBot.php";

use NewsBot\NewsBot;

$url = "https://edition.cnn.com/world";

$bot = new NewsBot($url, "box news-card3", "h3", "image");

print_r($bot->getNews());
