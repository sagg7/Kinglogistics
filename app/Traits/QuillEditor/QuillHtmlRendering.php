<?php

namespace App\Traits\QuillEditor;

use App\Traits\Storage\S3Functions;
use DOMDocument;

trait QuillHtmlRendering
{
    use S3Functions;

    private function renderHtmlString($htmlString)
    {
        $dom = new DOMDocument();
        $dom->loadHTML($htmlString);

        foreach ($dom->getElementsByTagName('img') as $img) {
            $img->setAttribute('src', $this->getTemporaryFile(substr($img->getAttribute('src'),1)));
            $img->setAttribute('class', 'img-fluid');
        }

        foreach ($dom->getElementsByTagName('blockquote') as $item) {
            $item->setAttribute('class', 'blockquote pl-1 border-left-primary border-left-3');
        }
        return $dom->saveHTML();
    }

    private function renderForJsonMessage($arr)
    {
        foreach ($arr as $key => $item) {
            foreach($item as $element) {
                if (isset($element['image'])) {
                    $arr[$key]['insert']['image'] = $this->getTemporaryFile(substr($element['image'], 1));
                }
            }
        }
        return $arr;
    }
}
