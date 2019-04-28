<?php

namespace App\Utils;

class TextTransform
{

    public function textToLine($content, $maxCharsPerLine)
    {
        $words = explode(" ", $content);
        $arraySentence = [];
        $lengthline = 0;
        $totalSentence = [];
        $currentLine = [];
        $lengthline = $maxCharsPerLine;

        for($i=0; $i < count($words); $i++){

            $word = $words[$i];
            $lengthWord = strlen($word);
//        $word = preg_split('//u', strtolower($word), -1, PREG_SPLIT_NO_EMPTY);

            if (($lengthline - $lengthWord) >= 0 ) {
                $currentLine [] = $word;
                $lengthline -= $lengthWord;

                if ($i == count($words) - 1) {
                    $totalSentence [] = $currentLine;
                }
            } else {
                $totalSentence [] = $currentLine;
                $currentLine = [];
                $lengthline = $maxCharsPerLine;
                $currentLine [] = $word;
                if ($i == count($words) - 1) {
                    $totalSentence [] = $currentLine;
                }
            }
        }

        return $totalSentence;
    }
}