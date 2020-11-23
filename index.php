<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/simplehtmldom/simplehtmldom/simple_html_dom.php';

$cpt_min5 = 0;
$cpt_bt5and9 = 0;
$cpt_more10 = 0;

$texts = [];
$path = 'https://booknode.com/le_seigneur_des_anneaux_tome_1_la_communaute_de_l_anneau_010229/extraits?offset=';

for ($i = 1; $i <= 10; $i++) {
    $texts = array_merge($texts, get_texts($path . $i));
}

$texts = preg_replace('/[,.\-:;!?`[\]=><()\r\t\n]/', ' ', $texts);
$texts = preg_grep("/[(\p{L}*)'’]*/", $texts);

foreach ($texts as $text) {
    $words = explode(' ', $text);

    foreach ($words as $word) {
        if (empty($word)) {
            continue;
        }

        $word = mb_strtolower(trim($word), 'UTF-8');
        $length = mb_strlen($word, 'UTF-8');

        if ($length < 5) {
            $cpt_min5++;
        } elseif ($length >= 5 && $length <= 9) {
            $cpt_bt5and9++;
        } elseif ($length >= 10) {
            $cpt_more10++;
        }
    }
}

echo 'Mots -5 caractéres : ' . $cpt_min5 . PHP_EOL;
echo 'Mots +5 ou -9 caractéres : ' . $cpt_bt5and9 . PHP_EOL;
echo 'Mots +10 caractéres : ' . $cpt_more10 . PHP_EOL;

echo 'Total : ' . ($cpt_min5 + $cpt_bt5and9 + $cpt_more10);

exit(0);

/*
 * FUNCTIONS
 */

function get_texts($url)
{
    $html = file_get_html($url);
    $contents = [];
    $texts = [];

    foreach ($html->find('span') as $element) {
        $contents[] = (string)$element;
    }

    foreach ($contents as $text) {
        if (startsWith($text, '<span class="actual-text">')) {
            $texts[] = strip_tags($text);
        }
    }

    return $texts;
}

function startsWith($haystack, $needles)
{
    foreach ((array)$needles as $needle) {
        if ((string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0) {
            return true;
        }
    }

    return false;
}
