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

foreach ($texts as $text) {
    $words = explode(' ', $text);

    foreach ($words as $word) {
        $word = preg_replace('/[,.\-:;!?`[\]="><()]+/', '', mb_strtolower($word, 'UTF-8'));

        if (empty($word)) {
            continue;
        }

        $word = trim($word);

        if (mb_strlen($word, 'UTF-8') < 5) {
            $cpt_min5++;
        } elseif (mb_strlen($word, 'UTF-8') >= 5 && mb_strlen($word, 'UTF-8') <= 9) {
            $cpt_bt5and9++;
        } elseif (mb_strlen($word, 'UTF-8') > 10) {
            $cpt_more10++;
        }
    }
}

dd($cpt_min5, $cpt_bt5and9, $cpt_more10, $cpt_min5 + $cpt_bt5and9 + $cpt_more10);

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
