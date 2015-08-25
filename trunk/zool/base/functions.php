<?php


/**
 *
 * These functions sholdn't hang on any class.
 *
 * @param string $const name of constant
 */
function boolconst($const){
  return defined($const) && constant($const);
}