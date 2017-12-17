<?php

namespace Bleidd\Util;

class Inflector
{
    
    /**
     * @param $name
     * @return string
     */
    public static function createSlug($name): string
    {
        if (empty($name)) {
            return '';
        }
        $slug = strtr($name, [
            'ą' => 'a',
            'ę' => 'e',
            'ś' => 's',
            'ć' => 'c',
            'ł' => 'l',
            'ó' => 'o',
            'ź' => 'z',
            'ż' => 'z',
            'ń' => 'n',
            'Ą' => 'a',
            'Ę' => 'e',
            'Ś' => 's',
            'Ć' => 'c',
            'Ł' => 'l',
            'Ó' => 'o',
            'Ź' => 'z',
            'Ż' => 'z',
            'Ń' => 'n',
        ]);
        $replaced = str_replace([' ', ',', '.', '*', '&', '!', '^', '%', '$', '#', '@', '(', ')', '\'', '"', '[', ']', ';', '?', '~', '`', ':', '+', '='], '-', $slug);
        $replaced = str_replace(['---', '--', ' ', '-'], '-', $replaced);
        if (in_array($replaced[strlen($replaced) - 1], ['-', '.', ',', ' '])) {
            $replaced = substr($replaced, 0, strlen($replaced) - 1);
        }
        
        return strtolower($replaced);
    }
    
    /**
     * @param int  $length
     * @param bool $onlyLetters
     * @return bool|string
     */
    public static function randomString(int $length = 8, bool $onlyLetters = false): string
    {
        $alphaNumeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . ($onlyLetters ? '' : '0123456789');
        return substr(str_shuffle($alphaNumeric), 0, $length);
    }
    
    /**
     * $mode = 1 - add random prefix (before) fileName, 2 - add random postfix (after) fileName
     *
     * @param string $name
     * @param int    $maxLength
     * @param int    $mode
     * @param int    $randomLength
     * @param string $separator
     * @return string;
     */
    public static function normalizeFileName(
        string $name, int $maxLength = 20, int $mode = 2, int $randomLength = 3, string $separator = '_'): string
    {
        $slug = self::createSlug($name);
        
        if (strlen($slug) > $maxLength) {
            $tmp = substr($slug, 0, 20);
        } else {
            $tmp = $slug;
        }
        
        $random = self::randomString($randomLength);
        
        if ($mode == 1) {
            $fileName = $random . $separator . $tmp;
        } else if ($mode == 2) {
            $fileName = $tmp . $separator . $random;
        }
        
        return $fileName;
    }
    
    /**
     * @param string $string
     * @param bool   $capitalizeFirstCharacter
     * @return mixed|string
     */
    public static function toCamelCase(string $string, bool $capitalizeFirstCharacter = false): string
    {
        if (empty($string)) {
            return $string;
        }
        $str = str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $string)));
        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }
        
        return $str;
    }
    
    /**
     * @param string $input
     * @param bool   $capitalizeFirstCharacter
     * @return string
     */
    public static function to_underscore(string $input, bool $capitalizeFirstCharacter = false): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];

        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        
        $result = implode('_', $ret);
        if ($capitalizeFirstCharacter) {
            return ucfirst($result);
        }
        
        return $result;
    }
    
}
