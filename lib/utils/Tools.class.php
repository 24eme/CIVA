<?php

class Tools {
    public static function array_diff_recursive($a1, $a2) {
        $diff = array_diff($a1, $a2);
        foreach($a1 as $key1 => $value1) {
            foreach($a2 as $key2 => $value2)
                if (is_array($value1) && isset($a2[$key1]) && is_array($a2[$key1])) {
                    if (isset($a2[$key1]) && is_array($a2[$key1])) {
                        $diff_rec = self::array_diff_recursive($a1[$key1], $a2[$key1]);
                        if (count($diff_rec) > 0) {
                            $diff[$key1] = $diff_rec;
                        }
                    } else {
                        $diff[$key1] = $value1;
                    }
                }
                if (is_array($value2) && !isset($a1[$key2])) {
                    $diff[$key2] = $value2;
                }
        }
        return $diff;
    }
}

