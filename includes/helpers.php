<?php

/**
 * Wise Widget Manager - Compatibility Helpers 
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Polyfill: str_contains (PHP 8)
 */
if (!function_exists('wisewima_str_contains')) {
  function wisewima_str_contains($haystack, $needle)
  {
    if ($needle === '') return false;
    return strpos($haystack, $needle) !== false;
  }
}

/**
 * Polyfill: str_starts_with (PHP 8)
 */
if (!function_exists('wisewima_str_starts_with')) {
  function wisewima_str_starts_with($haystack, $needle)
  {
    return strpos($haystack, $needle) === 0;
  }
}


/**
 * Safe array filter helper (clean alternative to fn())
 */
if (!function_exists('wisewima_array_filter_not')) {
  function wisewima_array_filter_not(array $items, $value)
  {
    return array_values(array_filter($items, function ($item) use ($value) {
      return $item !== $value;
    }));
  }
}


if (!function_exists('wisewima_array_filter_not_in')) {
  function wisewima_array_filter_not_in(array $items, array $values)
  {
    return array_values(array_filter($items, function ($item) use ($values) {
      return !in_array($item, $values, true);
    }));
  }
}
