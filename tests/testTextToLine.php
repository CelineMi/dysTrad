<?php

function testTextToLine()
{
    $actual = textToLine('salut ça va bien ?', 9);
    $expected = [
        ['salut', 'ça'],
        ['va', 'bien', '?']
    ];

    if ($expected != $actual)
    {
        echo "\n" . 'fail'. "\n";
        echo 'expected'. "\n" . json_encode($expected) . "\n";
        echo 'actual'. "\n" . json_encode($actual) . "\n";
    }else{
        echo '.';
    }


}

function testTextToLine2()
{
    $actual = textToLine('aaaaaaaaa aaaaaaaaa', 9);
    $expected = [
        ['aaaaaaaaa'],
        ['aaaaaaaaa']
    ];

    if ($expected != $actual)
    {
        echo "\n" . 'fail'. "\n";
        echo 'expected'. "\n" . json_encode($expected) . "\n";
        echo 'actual'. "\n" . json_encode($actual) . "\n";
    }else{
        echo '.';
    }


}

function textToLine($content, $maxCharsPerLine)
{
    $words = explode(" ", $content);
    $arraySentence = [];
    $lengthline = 0;
    $totalSentence = [];
    $currentLine = [];
    $lengthline = $maxCharsPerLine;

    for($i=0; $i < count($words); $i++){

        $word = $words[$i];
//        echo "\n begin loop " . $word . "\n";
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
//        echo "end loop \n";
    }

    return $totalSentence;
}

testTextToLine();
testTextToLine2();