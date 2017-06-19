<?php

/**
 * Created by PhpStorm.
 * User: ncdgh
 * Date: 23-May-17
 * Time: 08:41
 */
class Util {
  static function roundMinute($time) {
    if (is_numeric($time)) {
      $rounded = round($time / 60) * 60;
      return gmdate("H:i:s", $rounded);
    } else {
      return self::roundMinute(strtotime("1970-01-01 $time UTC"));
    }
  }

}