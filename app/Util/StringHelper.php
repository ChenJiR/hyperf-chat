<?php


namespace App\Util;


class StringHelper
{
    static function escape($input, $urldecode = 0)
    {
        if (is_array($input)) {
            foreach ($input as $k => $v) {
                $input[$k] = self::escape($v, $urldecode);
            }
        } else {
            $input = trim($input);
            if ($urldecode == 1) {
                $input = str_replace(array('+'), array('{addplus}'), $input);
                $input = urldecode($input);
                $input = str_replace(array('{addplus}'), array('+'), $input);
            }
            $input = addslashes($input);
        }
        //防止最后一个反斜杠引起SQL错误如 'abc\'
        if (substr($input, -1, 1) == '\\') $input = $input . "'";//$input=substr($input,0,strlen($input)-1);
        return $input;
    }
}