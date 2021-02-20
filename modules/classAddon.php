<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

/*
  xpi-metadata    - id, slug, type, subType, active, reviewed, blocked, blockLevel, latestRelease, compatibilityBits,
                    owner, url, name, creator, category, description, content, contentType, tags, homepageURL,
                    repositoryURL, supportURL, supportEmail

  xpi-versions    - autoID, addonID, thisVersion, versionBlocked, versionBusted, targetBits, hash, epoch, xpi,
                    targetApplication, technology, licenseType, licenseContent

  search-plugins  - autoID, legacyID, slug, name, icon, content
*/

class classAddon {
  const TABLE_METADATA          = 'xpi-metadata';
  const TABLE_VERSIONS          = 'xpi-versions';
  const TABLE_SEARCH_PLUGINS    = 'search-plugins';
  const TABLE_PERSONAS          = 'personas';

  private $querySelect;

  /********************************************************************************************************************
  * Class constructor that sets inital state of things
  ********************************************************************************************************************/
  function __construct($aEnableWrite = null) {
    gfEnsureModules(__METHOD__, 'database');
  }

  /*******************************************************************************************************************/

  public function getAddonsByType($aType) {
    global $gaRuntime;
    global $gmDatabase;

    switch ($aType) {
      case XPINSTALL_TYPES['extension']:
      case XPINSTALL_TYPES['theme']:
      case XPINSTALL_TYPES['locale']:
      case XPINSTALL_TYPES['dictionary']:
        $this->querySelect = [self::TABLE_METADATA => ['*']];

        $query = "SELECT ?p FROM ?n WHERE (`type` = ?i OR `subType` = ?i)";

        if ($gaRuntime['requestComponent'] == 'site') {
          $query .= SPACE . "AND `active` = 1 AND `reviewed` = 1";

          if ($aType == XPINSTALL_TYPES['extension']) {
            $query .= SPACE . "`AND NOT category` = 0";
          }
        }

        $rv = $gmDatabase->get('all', $query, $this->genSelect(), self::TABLE_METADATA, $aType, $aType);
        break;
      case XPINSTALL_TYPES['external']:
        $this->querySelect = [self::TABLE_METADATA => ['*']];
        $query = "SELECT ?p FROM ?n WHERE `type` = ?i";
        $rv = $gmDatabase->get('all', $query, $this->genSelect(), self::TABLE_METADATA, XPINSTALL_TYPES['external']);
        break;
      default:
        gfError(__METHOD__ . ' - Unknown type: ' . $aType);
    }

    return $rv;
  }

 /********************************************************************************************************************
  * Internal method to generate a select string from $this->querySelect
  *******************************************************************************************************************/
  private function genSelect() {
    global $gmDatabase;

    if (!gfSuperVar('var', $this->querySelect)) {
      gfError(__METHOD__ . ' - $this->querySelect cannot be empty/null');
    }

    $querySelect = '';
    foreach ($this->querySelect as $_key => $_value) {
      if ($_value[0] == '*') {
        $querySelect .= $gmDatabase->parse("?n.*", $_key) . ', ';
        continue;
      }

      foreach ($_value as $_value2) {
        $querySelect .= $gmDatabase->parse("?n.?n", $_key, $_value2) . ', '; 
      }
    }

    $this->querySelect = null;
    
    $rv = substr($querySelect, 0, -2);

    return $rv;
  }


}

?>
