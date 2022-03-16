<?php

// == | classGenerateContent | ========================================================================================

class classAddons { 
  /********************************************************************************************************************
  * Class constructor that sets initial state of things
  ********************************************************************************************************************/
  function __construct() {
    //gfEnsureModules(__CLASS__, 'database');
  }

  /********************************************************************************************************************
  * TBD
  ********************************************************************************************************************/
  public function processAddon($aManifests) {
    global $gmContent;

    if ($aManifests === null || !is_array($aManifests)) {
      return null;
    }

    $manifests = $aManifests;

    if (!array_is_list($manifests)) {
      $manifests = [$aManifest];
    }

    $bools = ['enabled', 'reviewed', 'blocked', 'userDisabled', 'hasIcon', 'hasPreview'];
    $ints = ['owner', 'type', 'release', 'beta', 'targetClient', 'category'];
    $nullableStrings = ['tags', 'homepageURL', 'repositoryURL', 'supportURL', 'supportEmail', 'content'];

    foreach ($manifests as $_key => $_value) {
      // Perform type fix-ups
      foreach ($_value as $_key2 => $_value2) {
        if (in_array($_key2, $bools)) {
          $manifests[$_key][$_key2] = (bool)$_value2;
        }

        if (in_array($_key2, $ints)) {
          $manifests[$_key][$_key2] = (int)$_value2;
        }

        if (in_array($_key2, $nullableStrings)) {
          $manifests[$_key][$_key2] = gfSuperVar('check', $_value2);
        }
      }

      // Trim and HTML Encode Name and Description
      $manifests[$_key]['name'] = htmlentities(trim($manifests[$_key]['name']), ENT_XHTML);
      $manifests[$_key]['description'] = htmlentities(trim($manifests[$_key]['description']), ENT_XHTML);

      // Parse content (if applicable)
      if (array_key_exists('contentType', $manifests[$_key]) && array_key_exists('content', $manifests[$_key])) {
        if (!$manifests[$_key]['content'] || !$gmContent) {
          $manifests[$_key]['contentType'] = 'fallback';
          $manifests[$_key]['content'] = $manifests[$_key]['description'];
        }
  
        if ($manifests[$_key]['contentType'] != 'fallback') {         
          $manifests[$_key]['content'] = $gmContent->parseCodeTags($manifests[$_key]['contentType'],
                                                                   $manifests[$_key]['content']);
        }
      }
    }

    if (count($manifests) <= 1) {
      return $manifests[0] ?? null;
    }

    return $manifests;
  }

  /********************************************************************************************************************
  * TBD
  ********************************************************************************************************************/
  public function processXPI($aManifests) {
    if ($aManifests === null || !is_array($aManifests)) {
      return null;
    }

    $manifests = $aManifests;

    if (!array_is_list($manifests)) {
      $manifests = [$aManifests];
    }

    $bools = ['enabled', 'reviewed', 'blocked', 'busted', 'hasIcon', 'hasPreview'];
    $ints = ['id', 'targetClient', 'epoch'];
    $nullableStrings = ['changelog'];
    $jsonEncoded = ['license', 'installManifest'];

    foreach ($manifests as $_key => $_value) {
      // Perform type fix-ups
      foreach ($_value as $_key2 => $_value2) {
        if (in_array($_key2, $bools)) {
          $manifests[$_key][$_key2] = (bool)$_value2;
        }

        if (in_array($_key2, $ints)) {
          $manifests[$_key][$_key2] = (int)$_value2;
        }

        if (in_array($_key2, $nullableStrings)) {
          $manifests[$_key][$_key2] = gfSuperVar('check', $_value2);
        }

        if (in_array($_key2, $jsonEncoded)) {
          $manifests[$_key][$_key2] = json_decode($_value2, true);
        }
      }
    }

    if (count($manifests) == 1) {
      return $manifests[0] ?? null;
    }

    return uasort($manifests, function ($a, $b) { return $b['id'] <=> $a['id']; });
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