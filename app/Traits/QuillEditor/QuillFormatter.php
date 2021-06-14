<?php

namespace App\Traits\QuillEditor;

trait QuillFormatter
{
    /**
     * @param $message
     * @return string
     */
    private function formatQuillHtml($message, $upload_path): string
    {
        $htmlArr = [];
        foreach ($message->ops as $idx => $item) {
            $formatted = '';
            if (is_object($item->insert)) {
                switch (key($item->insert)) {
                    case 'image':
                        if (preg_match("/(\/storage)/", $item->insert->image))
                            $url = $item->insert->image;
                        else
                            $url = "/" . $this->uploadImage($item->insert->image, "$upload_path/$idx", 80);
                        $formatted = '<img src="' . $url . '" alt="image">';
                        $message->ops[$idx]->insert->image = $url;
                        break;
                    case 'video':
                        $formatted = '<iframe allowfullscreen="true" frameborder="0" src="' . $item->insert->video . '"></iframe>';
                        break;
                }
            } else if ($item->insert == "\n" && isset($item->attributes)) { // Meaning cases for weird formatting when, for example, heading is set and the attributes are passed after the text
                $formatted = $htmlArr[count($htmlArr) - 1] . "\n";
                array_pop($htmlArr);
            } else {
                $exploded = explode("\n", $item->insert);
                if (!preg_match("/^(\n)+$/", $item->insert) && count($exploded) > 1) {
                    $lastExploded = count($exploded) - 1;
                    foreach ($exploded as $i => $exp) {
                        if ($lastExploded == $i)
                            $formatted = $this->replaceText($exp);
                        else if (!$item != "\n")
                            $htmlArr[] = $exp . '<br>';
                    }
                } else {
                    $formatted = $this->replaceText($item->insert);
                }
            }

            if (isset($item->attributes)) {
                $style = null;
                foreach ($item->attributes as $key => $attribute) {
                    switch ($key) {
                        case 'align':
                            $style = "text-align: $attribute;";
                            break;
                        case 'direction':
                            $style = "direction: $attribute";
                            break;
                        case 'underline':
                            $formatted = "<u " . ($style ? "style='$style'" : null) . ">$formatted</u>";
                            break;
                        case 'italic':
                            $formatted = "<em " . ($style ? "style='$style'" : null) . ">$formatted</em>";
                            break;
                        case 'strike':
                            $formatted = "<s " . ($style ? "style=''$style'" : null) . ">$formatted</s>";
                            break;
                        case 'blockquote':
                            $formatted = "<blockquote " . ($style ? "style='$style'" : null) . ">$formatted</blockquote>";
                            break;
                        case 'code-block':
                            $formatted = "<pre " . ($style ? "style='$style'" : null) . " spellcheck='false'>$formatted</pre>";
                            break;
                        case 'header':
                            $formatted = "<h$attribute " . ($style ? "style='$style'" : null) . ">$formatted</h$attribute>";
                            break;
                        case 'bold':
                            $formatted = "<strong " . ($style ? "style='$style'" : null) . ">$formatted</strong>";
                            break;
                        case 'link':
                            $formatted = '<a href="' . $attribute . '" target="_blank"' . ($style ? "style='$style'" : null) . '>' . $item->insert . '</a>';
                            break;
                        case 'list':
                            $formatted = "<span " . ($style ? "style='$style'" : null) . ">&emsp;• $formatted</span>";
                            break;
                    }
                }
            }

            $htmlArr[] = $formatted;
        }
        $html = '';
        foreach ($htmlArr as $item) {
            $html .= $item;
        }

        return $html;
    }
}
