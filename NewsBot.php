<?php

namespace NewsBot {

    use DOMDocument;
    use DOMXPath;

    class NewsBot
    {
        protected $url, $allNewsClass, $newsTitleClass, $newsImageClass;

        function __construct($url, $allNewsClass, $newsTitleClass, $newsImageClass)
        {
            $this->url = $url;
            $this->allNewsClass = $allNewsClass;
            $this->newsTitleClass = $newsTitleClass;
            $this->newsImageClass = $newsImageClass;
        }

        public function getNews()
        {
            $html = file_get_contents($this->url);
            $dom = new DOMDocument();
            @$dom->loadHTML($html);

            $newsArray = [];

            $boxes = $dom->getElementsByTagName('div');

            foreach ($boxes as $box) {
                if (strpos($box->getAttribute('class'), $this->allNewsClass) !== false) {
                    $title = $box->getElementsByTagName($this->newsTitleClass)[0]->nodeValue;
                    $link = $box->getElementsByTagName('a')[0]->getAttribute('href');

                    $innerHtml = file_get_contents($link);
                    $innerDom = new DOMDocument();
                    @$innerDom->loadHTML($innerHtml);
                    $xpath = new DOMXPath($innerDom);

                    $image = $xpath->query('//div[contains(@class, "' . $this->newsImageClass . '")]/img')->item(0)->getAttribute('src');
                    $content = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' post-content ')]//div[contains(concat(' ', normalize-space(@class), ' '), ' content ')]")->item(0)->textContent;

                    // Resimleri indirme işlemi
                    foreach ($newsArray as &$news) {
                        $imgUrl = $news['image'];
                        $imgName = basename($imgUrl);
                        $imgPath = './images/' . $imgName;
                        if (!file_exists($imgPath)) {
                            file_put_contents($imgPath, file_get_contents($imgUrl));
                        }
                        $news['image'] = $news['image'];
                    }

                    $newsArray[] = [
                        'title' => $title,
                        'link' => $link,
                        'image' => $image,
                        'content' => $content
                    ];
                }
            }

            return $newsArray;
        }
    }
}
