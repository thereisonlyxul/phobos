<?php

// == | classAddonManifest | ==========================================================================================

class classAddonManifest { 
  /********************************************************************************************************************
  * Class constructor that sets initial state of things
  ********************************************************************************************************************/
  function __construct() {
    //gfEnsureModules(__CLASS__, 'database');
  }

  /********************************************************************************************************************
  * TBD
  ********************************************************************************************************************/
  public function implodeSelect($aSelect) {
    global $gaRuntime;
    global $gmDatabase;

    if ($aSelect == WILDCARD || $gaRuntime['qComponent'] == 'panel' ||
        ($gaRuntime['debugMode'] &&  $gaRuntime['qComponent'] == 'special')) {
      return WILDCARD;
    }

    $select = $gmDatabase->parse(implode("," . SPACE, array_fill(0, count($aSelect), '?n')), ...$aSelect);
    return $select;
  }

  /********************************************************************************************************************
  * TBD
  ********************************************************************************************************************/
  public function getOneByID($aID) {
    global $gaRuntime;
    global $gmDatabase;

    $client = ($gaRuntime['qComponent'] == 'panel' ||
               ($gaRuntime['debugMode'] && $gaRuntime['qComponent'] == 'special')) ?
              gfGetClientBits(array_column(TARGET_APPLICATION, 'id')) :
              gfGetClientBits([TARGET_APPLICATION[$gaRuntime['currentApplication']]['id']]);

    $addonCols = $this->implodeSelect(['id', 'slug', 'enabled', 'reviewed', 'release']);
    $addon = $gmDatabase->get('row', "SELECT ?p FROM ?n WHERE `id` = ?s AND `targetClient` & ?i",
                              $addonCols, 'addons', $aID, $client);

    $addon = $this->processAddon($addon);

    if (!$addon) {
      return null;
    }

    $xpiCols = $this->implodeSelect(['enabled', 'reviewed', 'filename', 'epoch', 'hash', 'installManifest']);
    $xpi = $gmDatabase->get('row', "SELECT ?p FROM ?n WHERE `id` = ?s",
                            $xpiCols, 'xpinstall', $addon['release']);

    $xpi = $this->processXPI($xpi);

    $rv = ['addon' => $addon, 'xpinstall' => $xpi];

    return $rv;
  }

  /********************************************************************************************************************
  * TBD
  ********************************************************************************************************************/
  public function getOneBySlug($aSlug, $allVersions = null) {
    global $gaRuntime;
    global $gmDatabase;

    $client = ($gaRuntime['qComponent'] == 'panel' ||
               ($gaRuntime['debugMode'] && $gaRuntime['qComponent'] == 'special')) ?
              gfGetClientBits(array_column(TARGET_APPLICATION, 'id')) :
              gfGetClientBits([TARGET_APPLICATION[$gaRuntime['currentApplication']]['id']]);

    $addon = $gmDatabase->get('row', "SELECT * FROM ?n WHERE `slug` = ?s AND `targetClient` & ?i",
                              'addons', $aSlug, $client);

    $addon = $this->processAddon($addon);

    if (!$addon) {
      return null;
    }

    $xpi = $gmDatabase->get('row', "SELECT * FROM ?n WHERE `id` = ?s", 'xpinstall', $addon['release']);

    if (!$xpi) {
      return null;
    }

    $xpi = $this->processXPI($xpi);

    $rv = ['addon' => $addon, 'xpinstall' => $xpi];

    return $rv;
  }

  /********************************************************************************************************************
  * TBD
  ********************************************************************************************************************/
  public function getAllByType($aType) {
    global $gaRuntime;
    global $gmDatabase;

    $client = ($gaRuntime['qComponent'] == 'panel' ||
               ($gaRuntime['debugMode'] && $gaRuntime['qComponent'] == 'special')) ?
              gfGetClientBits(array_column(TARGET_APPLICATION, 'id')) :
              gfGetClientBits([TARGET_APPLICATION[$gaRuntime['currentApplication']]['id']]);

    $addonCols = $this->implodeSelect(['id', 'slug', 'type', 'enabled', 'reviewed', 'hasIcon', 'hasPreview',
                                        'owner', 'name', 'description', 'addonURL']);
    $addon = $gmDatabase->get('all', "SELECT ?p FROM ?n WHERE `type` & ?i AND `targetClient` & ?i ORDER BY `name` ASC",
                              $addonCols, 'addons', $aType, $client);

    $addon = $this->processAddon($addon);

    if (!$addon) {
      return null;
    }

    $rv = $addon;
    return $rv;
  }

  /********************************************************************************************************************
  * TBD
  ********************************************************************************************************************/
  public function getAllByCategory($aCat) {
    gfHeader(501);
  }

  /********************************************************************************************************************
  * TBD
  ********************************************************************************************************************/
  public function getAllBySearch($aTerms) {
    gfHeader(501);
  }

  /********************************************************************************************************************
  * TBD
  ********************************************************************************************************************/
  private function processAddon($aManifests) {
    global $gmDatabase;
    global $gmContent;

    if ($aManifests === null || !is_array($aManifests)) {
      return null;
    }

    $manifests = $aManifests;

    if (!array_is_list($manifests)) {
      $manifests = [$aManifests];
    }

    $bools            = ['enabled', 'reviewed', 'blocked', 'userDisabled', 'hasIcon', 'hasPreview'];
    $ints             = ['owner', 'type', 'release', 'beta', 'targetClient', 'category'];
    $nullableStrings  = ['tags', 'homepageURL', 'repositoryURL', 'supportURL', 'supportEmail', 'content'];

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

      // Get Owner Metadata
      if (array_key_exists('owner', $manifests[$_key])) {
        $_owner = $gmDatabase->get('row', "SELECT `username`, `displayName` FROM `users` WHERE `id` = ?i",
                                   $manifests[$_key]['owner']);

        $manifests[$_key]['ownerUsername'] = $_owner['username'] ?? null;
        $manifests[$_key]['ownerDisplayName'] = $_owner['displayName'] ?? null;

        if ($manifests[$_key]['ownerDisplayName']) {
          // Trim and HTML Encode Display Name
          $manifests[$_key]['ownerDisplayName'] = htmlentities(trim($manifests[$_key]['ownerDisplayName']), ENT_XHTML);
        }
      }

      // Trim and HTML Encode Name
      if (array_key_exists('name', $manifests[$_key])) {
        $manifests[$_key]['name'] = htmlentities(trim($manifests[$_key]['name']), ENT_XHTML);
      }

      // Trim and HTML Encode Description
      if (array_key_exists('description', $manifests[$_key])) {
        $manifests[$_key]['description'] = htmlentities(trim($manifests[$_key]['description']), ENT_XHTML);
      }

      // Parse content (if applicable)
      if (array_key_exists('contentType', $manifests[$_key]) && array_key_exists('content', $manifests[$_key])) {
        if (!$manifests[$_key]['content'] || !$gmContent) {
          $manifests[$_key]['contentType'] = 'description';
          $manifests[$_key]['content'] = $manifests[$_key]['description'];
        }
  
        if ($manifests[$_key]['contentType'] != 'description') {         
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
  private function processXPI($aManifests) {
    if ($aManifests === null || !is_array($aManifests)) {
      return null;
    }

    $manifests = $aManifests;

    if (!array_is_list($manifests)) {
      $manifests = [$aManifests];
    }

    $bools            = ['enabled', 'reviewed', 'blocked', 'busted', 'hasIcon', 'hasPreview'];
    $ints             = ['id', 'targetClient', 'epoch'];
    $nullableStrings  = ['changelog'];
    $jsonEncoded      = ['license', 'installManifest'];

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
  private function toolkitVersionSanity($aVersion) {
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