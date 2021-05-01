<?php


namespace Classes;


class WordSplitter
{
    const DICT_FILE = 'dicts/rus_dict.dic';
    public static $dict = [];
    /**
     * @var int
     */

    public static function init($dict_file)
    {
        $cache = explode("\r\n", file_get_contents($dict_file));
        self::$dict = [];

        foreach ($cache as $key => $record) {
            $tmp = explode(" ", $record);

            // dictionary has more properties but for now we use only lemmas - index 0
            foreach (['number', 'lemma', 'part_speech', 'freq'] as $paramKey => $param) {
                if (isset($tmp[$paramKey]) && $paramKey === 1) {
                    self::$dict[$key] = $tmp[$paramKey];
                }
            }
        }

    }

    /**
     * @param $maxGotchas
     * @return array
     */
    protected static function reduceOddGotchas(array $maxGotchas): array
    {
        $keys = (array_keys($maxGotchas));
        foreach (array_keys($keys) as $index) {
            $current_key = current($keys); // or $current_key = $keys[$index];
            $current_value = $maxGotchas[$current_key]; // or $current_value = $a[$keys[$index]];
            $next_key = next($keys);
            $next_value = $maxGotchas[$next_key] ?? null;

            if ($next_key && mb_strlen($current_value) + (int)$current_key > (int)$next_key) {

                if (mb_strlen($current_value) > mb_strlen($next_value) /*|| $next_key === null*/) {
                    unset($maxGotchas[$next_key]);
                } else {
                    unset($maxGotchas[$current_key]);
                }
            }
        }

        return $maxGotchas;
    }

    public static function wordBreak($stringToSplit){
//        if (! isset(self::$dict)) {
            self::init(dirname(__DIR__) . DIRECTORY_SEPARATOR . self::DICT_FILE);
//        }

        $givenStringLengths = mb_strlen($stringToSplit);
        $givenStringLengthsWithoutSpaces = mb_strlen(preg_replace('/\s+/', '', $stringToSplit));

        for ($start = 0; $start < $givenStringLengths; $start++) {
            $end = $start + 1;

            while ($end <= $givenStringLengths) {
                if (in_array(mb_substr($stringToSplit, $start, $end-$start), self::$dict)) {
                    if(!isset($gotchas[$start])) {
                        $gotchas[$start] = [];
                    }

                    array_push($gotchas[$start], mb_substr($stringToSplit, $start, $end-$start));
                }
                $end++;
            }
        }

        $maxGotchas = [];

        if(!empty($gotchas)) {
            foreach ($gotchas as $key => $gotcha) {
                $maxGotchas[$key] = array_reduce($gotcha, function($carry, $item) {
                    return mb_strlen($carry, 'utf-8') < mb_strlen($item, 'utf-8') ? $item : $carry;
                }, '');
            }

            while ($givenStringLengthsWithoutSpaces < self::getCaughtWordsLength($maxGotchas)) {
                $maxGotchas = self::reduceOddGotchas($maxGotchas);
//                var_dump(($givenStringLengths).' '.self::getCaughtWordsLength($maxGotchas) .' '.implode(" ", $maxGotchas));
            }
            $maxGotchas = self::reduceOddGotchas($maxGotchas);
        }

        return implode(" ", $maxGotchas);
    }

    public static function mb_substr_replace($original, $replacement, $position, $length = 0)
    {
        $startString = mb_substr($original, 0, $position, "UTF-8");
        $endString = mb_substr($original, $position + $length, mb_strlen($original), "UTF-8");

        $out = $startString . $replacement . $endString;

        return $out;
    }

    /**
     * @param array $maxGotchas
     * @return float|int
     */
    protected static function getCaughtWordsLength(array $maxGotchas)
    {
        return array_sum(array_map(function ($word) {
            return mb_strlen($word);
        }, $maxGotchas));
    }
}