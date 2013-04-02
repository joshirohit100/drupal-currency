<?php

/**
 * @file
 * Contains class \Drupal\currency\Input.
 */

namespace Drupal\currency;

/**
 * Helpers for parsing user input.
 */
class Input {

  public static $decimalSeparators = array(
    // A comma.
    ',',
    // A period (full stop).
    '.',
    // Arabic decimal separator.
    '٫',
    // A Persian Momayyez (forward slash).
    '/');

  /**
   * Parses an amount.
   *
   * @param string $amount
   *   Any optionally localized numeric string.
   *
   * @return string|false
   *   A numeric string, or FALSE in case of failure.
   */
  public static function parseAmount($amount, &$message = '') {
    if (!is_numeric($amount)) {
      $amount = static::parseAmountNegativeFormat($amount, $message);
    }
    if (!is_numeric($amount)) {
      $amount = static::parseAmountDecimalSeparator($amount, $message);
    }

    return is_numeric($amount) ? $amount : FALSE;
  }

  /**
   * Parses an amount's decimal separator.
   *
   * @param string $amount
   *   Any optionally localized numeric value.
   *
   * @return string|false
   *   The amount with its decimal separator replaced by a period, or FALSE in
   *   case of failure.
   */
  public static function parseAmountDecimalSeparator($amount) {
    $decimal_separator_counts = array();
    foreach (static::$decimalSeparators as $decimal_separator) {
      $decimal_separator_counts[$decimal_separator] = \mb_substr_count($amount, $decimal_separator);
    }
    $decimal_separator_counts_filtered = array_filter($decimal_separator_counts);
    if (count($decimal_separator_counts_filtered) > 1 || reset($decimal_separator_counts_filtered) !== FALSE && reset($decimal_separator_counts_filtered) != 1) {
      return FALSE;
    }
    return str_replace(static::$decimalSeparators, '.', $amount);
  }

  /**
   * Parses a negative amount.
   *
   * @param string $amount
   *
   * @return string
   *   The amount with negative formatting replaced by a minus sign prefix.
   */
  public static function parseAmountNegativeFormat($amount) {
    // An amount wrapped in parentheses.
    $amount = preg_replace('/^\((.*?)\)$/', '-\\1', $amount);
    // An amount suffixed by a minus sign.
    $amount = preg_replace('/^(.*?)-$/', '-\\1', $amount);
    // Double minus signs.
    $amount = preg_replace('/--/', '', $amount);

    return $amount;
  }
}
