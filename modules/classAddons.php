<?php

// == | classGenerateContent | ========================================================================================

class classAddons { 
  /********************************************************************************************************************
  * Class constructor that sets initial state of things
  ********************************************************************************************************************/
  function __construct() {
  }

  /********************************************************************************************************************
  * Checks a Toolkit Version for some kind of sanity (does not apply to a maxVersion form with wildcards)
  ********************************************************************************************************************/
  public function toolkitVersionSanity($aVersion) {
    $version = str_contains($aVersion, DOT) ? gfExplodeString(DOT, $aVersion) : [$aVersion];
    $count = count($version);
    $lastIndex = $count - 1;

    if ($count == 1) {
      $length = strlen($version[0]);

      if (str_contains($version[0], WILDCARD)) {
        return false;
      }

      if ($length < 4 || $length > 12) {
        return false;
      }

      if (($length == 12  && ((int)$version[0] < 200501010000 || (int)$version[0] > 203012311259)) ||
          ($length == 8   && ((int)$version[0] < 20050101     || (int)$version[0] > 20301231)) ||
          ($length == 4   && ((int)$version[0] < 2005         || (int)$version[0] > 2030))) {
        return false;
      }

      if (preg_match("/(a|b|pre|\+|\-)/", $version[0]) === 1) {
        return false;
      }

      return true;
    }

    foreach ($version as $_key => $_value) {
      if ($_key == $lastIndex) {
        continue;
      }

      if (!is_numeric($_value)) {
        return false;
      }
    }

    if (!is_numeric($version[$lastIndex])) {
      if (preg_match("/(\+|\-|\*)/", $version[$lastIndex]) === 1 ||
          preg_match("/(\d+?(a|b|pre)\d+?|\d+?pre)/", $version[$lastIndex]) !== 1) {
        return false;
      }
    }

    return true;
  } 
}

// ====================================================================================================================

?>