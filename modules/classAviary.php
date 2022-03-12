<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

class classAviary {
  // XML Stuff and Things
  const RDF_NS        = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
  const EM_NS         = 'http://www.mozilla.org/2004/em-rdf#';
  const MF_RES        = 'urn:mozilla:install-manifest';
  const ANON_PREFIX   = '#genid';

  // ------------------------------------------------------------------------------------------------------------------

  // Single properties
  // em:multiprocessCompatible' em:hasEmbeddedWebExtension are considered invalid for gregoriantojd
  // The following props only currently matter to Phobos. We /may/ add these to the Add-ons Manager at some point.
  // em:slug, em:category, em:license, em:repositoryURL, em:supportURL, and em:supportEmail
  const SINGLE_PROPS    = ['id', 'type', 'version', 'creator', 'homepageURL', 'updateURL', 'updateKey', 'bootstrap',
                           'skinnable', 'strictCompatibility', 'iconURL', 'icon64URL', 'optionsURL', 'optionsType',
                           'aboutURL', 'iconURL', 'unpack', 'multiprocessCompatible', 'hasEmbeddedWebExtension',
                           'slug', 'category', 'license', 'repositoryURL', 'supportURL', 'supportEmail'];

  // Multiple properties (because this is shared with other methods we use a class constant)
  // According to documentation, em:file is supposed to be used as a fallback when no chrome.manifest exists.
  // It would then use em:file and old style contents.rdf to generate a chrome manifest but I cannot find
  // any existing code to facilitate this at our level.
  // em:additionalLicenses is a Phobos-only multi-prop
  const MULTI_PROPS     = ['contributor', 'developer', 'translator', 'otherLicenses',
                           'targetPlatform', 'localized', 'targetApplication'];

  /********************************************************************************************************************
  * Parses install.rdf using Rdf_parser class
  *
  * @param string     $aManifestData
  * @return array     $data["manifest"]
  ********************************************************************************************************************/
  public function parseInstallManifest($aManifestData) {
    $data = EMPTY_ARRAY;

    // ----------------------------------------------------------------------------------------------------------------

    // Setup the repat rdf parser
    require_once(LIBRARIES['rdfParser']);
    $rdf = new Rdf_parser();
    $rdf->rdf_parser_create(null);
    $rdf->rdf_set_user_data($data);
    $rdf->rdf_set_statement_handler(['classAviary', 'mfStatementHandler']);
    $rdf->rdf_set_base(EMPTY_STRING);

    // If the install manifest can't be parsed return why as a string.
    if (!$rdf->rdf_parse($aManifestData, strlen($aManifestData), true)) {
      $parseError = 'RDF Parsing Error' . COLON . SPACE .
                    xml_error_string(xml_get_error_code($rdf->rdf_parser['xml_parser'])) . NEW_LINE .
                    'Line Number' . SPACE . 
                    xml_get_current_line_number($rdf->rdf_parser['xml_parser']) . SPACE .
                    ', Column' . SPACE . xml_get_current_column_number($rdf->rdf_parser['xml_parser']) . DOT;
      return $parseError;
    }

    // ----------------------------------------------------------------------------------------------------------------

    // We need to resolve em:localized by attaching the associated genid data into the manifest data
    if (array_key_exists('localized', $data['manifest']) &&
        is_array($data['manifest']['localized'])) {
      $localized = ['name' => EMPTY_ARRAY, 'description' => EMPTY_ARRAY, 'contributor' => EMPTY_ARRAY,
                    'developer' => EMPTY_ARRAY, 'translator'  => EMPTY_ARRAY];

      foreach ($data['manifest']['localized'] as $_value) {
        if (!array_key_exists(self::EM_NS . 'locale', $data[$_value])) {
          continue;
        }

        if ($data[$_value][self::EM_NS . 'locale'] == 'en-US') {
          continue;
        }

        foreach ($data[$_value] as $_key2 => $_value2) {
          switch ($_key2) {
            case self::EM_NS . 'name':
            case self::EM_NS . 'description':
              if ($_value2 != $data['manifest'][str_replace(self::EM_NS, EMPTY_STRING, $_key2)]['en-US']) {
                $localized[str_replace(self::EM_NS, EMPTY_STRING, $_key2)]
                          [$data[$_value][self::EM_NS . 'locale']] = $_value2;
              }
              break;
            case self::EM_NS . 'contributor':
            case self::EM_NS . 'developer':
            case self::EM_NS . 'translator':
              $localized[str_replace(self::EM_NS, EMPTY_STRING, $_key2)] =
                array_merge($localized[str_replace(self::EM_NS, EMPTY_STRING, $_key2)], $_value2);
              break;
          }
        }
      }

      unset($data['manifest']['localized']);

      foreach($localized as $_key => $_value) {
        if ($_value == EMPTY_ARRAY) {
          continue;
        }

        $data['manifest'][$_key] = array_key_exists($_key, $data['manifest']) ? 
                                                    array_merge($data['manifest'][$_key], $_value) :
                                                    $_value;

        if (!in_array($_key, ['name', 'description'])) {
          $data['manifest'][$_key] = array_values(array_unique($data['manifest'][$_key]));
        }
      }
    }

    // ----------------------------------------------------------------------------------------------------------------

    // em:developer is no longer supported. Merge it with em:contributors
    if (array_key_exists('developer', $data['manifest'])) {
      if (array_key_exists('contributor', $data['manifest'])) {
       $data['manifest']['contributor'] = array_values(array_unique(array_merge($data['manifest']['contributor'],
                                                                                $data['manifest']['developer'])));
      }
      else {
        $data['manifest']['contributor'] = $data['manifest']['developer'];
      }

      unset($data['manifest']['developer']);
    }

    // ----------------------------------------------------------------------------------------------------------------

    // Set the targetApplication data
    if (array_key_exists('targetApplication', $data['manifest']) &&
        is_array($data['manifest']['targetApplication'])) {
      $targetApplication = EMPTY_ARRAY;

      foreach ($data['manifest']['targetApplication'] as $_value) {
        $id = $data[$_value][self::EM_NS . "id"];
        $targetApplication[$id]['minVersion'] = $data[$_value][self::EM_NS . 'minVersion'];
        $targetApplication[$id]['maxVersion'] = $data[$_value][self::EM_NS . 'maxVersion'];
        unset($data[$_value]);
      }

      unset($data['manifest']['targetApplication']);
      $data['manifest']['targetApplication'] = $targetApplication;
    }

    // ----------------------------------------------------------------------------------------------------------------

    // Tell the repat rdf parser to fuck off
    $rdf->rdf_parser_free();

    // Return the manifest
    return $data['manifest'];
  }

  /********************************************************************************************************************
  * Parses install.rdf for our desired properties
  *
  * @param array      &$aData
  * @param string     $aSubjectType
  * @param string     $aSubject
  * @param string     $aPredicate
  * @param int        $aOrdinal
  * @param string     $aObjectType
  * @param string     $aObject
  * @param string     $aXmlLang
  ********************************************************************************************************************/
  static function mfStatementHandler(&$aData, $aSubjectType, $aSubject, $aPredicate,
                                     $aOrdinal, $aObjectType, $aObject, $aXmlLang) {
    // Look for properties on the install manifest itself
    if ($aSubject == self::MF_RES && $aObject != 'false') {
      // we're only really interested in EM properties
      if (str_starts_with($aPredicate, self::EM_NS)) {
        $emProp = str_replace(self::EM_NS, EMPTY_STRING, $aPredicate);

        if (in_array($emProp, self::SINGLE_PROPS)) {
          $aData['manifest'][$emProp] = $aObject;
        }
        elseif (in_array($emProp, self::MULTI_PROPS)) {
          $aData['manifest'][$emProp][] = $aObject;
        }
        elseif (in_array($emProp, ['name', 'description'])) {
          $aData['manifest'][$emProp][($aXmlLang ? $aXmlLang  : 'en-US')] = $aObject;
        }
      }
    }
    else {
      // Previously, Mozilla did not BOTHER to even ATTEMPT to handle em:localized props
      // Here we will attempt it. Though it does mean any multi-prop with localized-props
      // COULD have these set but it /GENERALLY/ is not the job of the install manifest
      // parser or the statement handler to say if that is right or wrong..
      // Just make it possble.
      if (in_array(str_replace(self::EM_NS, EMPTY_STRING, $aPredicate),
                   ['contributor', 'developer', 'translator'])) {
        $aData[$aSubject][$aPredicate][] = $aObject;
      }
      else {
        // We don't know what it is so save it anyway as Mozilla always did.
        $aData[$aSubject][$aPredicate] = $aObject;
      }
    }

    // And return
    return $aData;
  }

  /********************************************************************************************************************
  * Parses manifest array into install.rdf
  ********************************************************************************************************************/
  public function createInstallManifest($aManifest, $aDirectOutput = null) {
    // The Root Element of an install manifest
    $installManifest = array(
      '@element' => 'RDF',
      '@attributes' => array(
        'xmlns' => self::RDF_NS,
        'xmlns:em' => self::EM_NS,
      )
    );

    // The main description of an install manifest
    $mainDescription = array(
      '@element' => 'Description',
      '@attributes' => array(
        'about' => self::MF_RES,
      )
    );

    // ----------------------------------------------------------------------------------------------------------------

    // XXXTobin: aboutURL is the add-on's about box NOT website
    if (array_key_exists('aboutURL', $aManifest)) {
      if (!str_starts_with($aManifest['aboutURL'], 'chrome://')) {
        unset($aManifest['aboutURL']);
      }
    }

    // XXXTobin: multiprocessCompatible means nothing to us
    if (array_key_exists('multiprocessCompatible', $aManifest)) {
      unset($aManifest['multiprocessCompatible']);
    }

    // XXXTobin: We tend to mangle homepageURL to repositoryURL when it is a known forge
    // However, we should mangle back unless both are used.
    // This should be removed after the launch of Phobos since we are introducing an em:repositoryURL
    if (!array_key_exists('homepageURL', $aManifest)) {
      if (array_key_exists('repositoryURL', $aManifest)) {
        $aManifest['homepageURL'] = $aManifest['repositoryURL'];
        unset($aManifest['repositoryURL']);
      }
    }
    // ----------------------------------------------------------------------------------------------------------------

    // Add single props as attributes to the main description
    foreach ($aManifest as $_key => $_value) {
      if (in_array($_key, self::MULTI_PROPS)) {
        continue;
      }

      if (in_array($_key, ['name', 'description'])) {
        $mainDescription['@attributes']['em:' . $_key] = $_value['en-US'];
        continue;
      }

      $mainDescription['@attributes']['em:' . $_key] = $_value;
    }

    // ----------------------------------------------------------------------------------------------------------------

    // Add multiprops as elements
    foreach (['em:contributor'      => $aManifest['contributor'] ?? null,
              'em:developer'        => $aManifest['developer'] ?? null,
              'em:translator'       => $aManifest['translator'] ?? null,
              'em:otherLicenses'    => $aManifest['otherLicenses'] ?? null,
              'em:targetPlatform'   => $aManifest['targetPlatform'] ?? null]
             as $_key => $_value) {
      if (!$_value) {
        continue;
      }

      foreach ($_value as $_value2) {
        $mainDescription[] = ['@element' => $_key, '@content' => $_value2];
      }
    }

    // ----------------------------------------------------------------------------------------------------------------

    $locales = array_unique(array_merge(array_keys($aManifest['name']), array_keys($aManifest['description'])));
    sort($locales);

    foreach ($locales as $_value) {
      $_name = $aManifest['name'][$_value] ?? null;
      $_desc = $aManifest['description'][$_value] ?? null;
      $_attrs = ['em:locale' => $_value];

      if ($_name) {
        $_attrs['em:name'] = $_name;
      }

      if ($_desc) {
        $_attrs['em:description'] = $_desc;
      }

      $mainDescription[] = ['@element' => 'em:localized', ['@element' => 'Description', '@attributes' => $_attrs]];
    }

    // ----------------------------------------------------------------------------------------------------------------

    // Add targetApplications as elements with attrs of the targetApplication description
    foreach ($aManifest['targetApplication'] as $_key => $_value) {
      $mainDescription[] = array(
        '@element' => 'em:targetApplication',
        array(
          '@element' => 'Description',
          '@attributes' => array(
            'em:id' => $_key,
            'em:minVersion' => $_value['minVersion'],
            'em:maxVersion' => $_value['maxVersion'],
          )
        )
      );
    }

    // ----------------------------------------------------------------------------------------------------------------

    // Attach the main description to the root element
    $installManifest[] = $mainDescription;

    // Generate XML (or RDF in this case)
    $installManifest = gfCreateXML($installManifest, $aDirectOutput);

    // ----------------------------------------------------------------------------------------------------------------

    return $installManifest;
  }

  /********************************************************************************************************************
  * Parses manifest array into update.rdf
  ********************************************************************************************************************/
  public function createUpdateManifest($aManifest, $aDirectOutput = null) {
    // XXXTobin: This is for testing only
    if (!array_key_exists('updateLink', $aManifest)) {
      $aManifest['updateLink'] = 'about:blank?arg1=cabbage&arg2=celery';
      $aManifest['updateHash'] = 'sha256:none';
    }

    $aManifest['type'] = AUS_XPI_TYPES[$aManifest['type']] ?? 'item';

    // ----------------------------------------------------------------------------------------------------------------

    // Construct the Update Manifest
    $updateManifest = array(
      '@element' => 'RDF:RDF',
      '@attributes' => array(
        'xmlns:RDF' => self::RDF_NS,
        'xmlns:em' => self::EM_NS,
      ),
      array(
        '@element' => 'em:updates',
        array(
          '@element' => 'Description',
          '@attributes' => array(
            'about' => 'urn:mozilla:' . $aManifest['type'] . COLON . $aManifest['id']
          ),
          array(
            '@element' => 'RDF:Seq',
            array(
              '@element' => 'RDF:li',
              array(
                '@element' => 'Description',
                '@attributes' => array(
                  'em:version' => $aManifest['version']
                ),
              )
            )
          )
        )
      )
    );

    // ----------------------------------------------------------------------------------------------------------------

    // Add targetApplications as elements with attrs of the targetApplication description
    foreach ($aManifest['targetApplication'] as $_key => $_value) {
       // RDF:RDF -> em:updates -> Description -> RDF:Seq -> RDF:li -> Description
       $updateManifest[0][0][0][0][0][] = array(
        '@element' => 'em:targetApplication',
        array(
          '@element' => 'Description',
          '@attributes' => array(
            'em:id' => $_key,
            'em:minVersion' => $_value['minVersion'],
            'em:maxVersion' => $_value['maxVersion'],
          ),
          array(
            '@element' => 'em:updateLink',
            '@content' => $aManifest['updateLink'],
          ),
          array(
            '@element' => 'em:updateHash',
            '@content' => $aManifest['updateHash'],
          )
        )
      );
    }    

    // ----------------------------------------------------------------------------------------------------------------

    return gfCreateXML($updateManifest, $aDirectOutput);
  }

  /********************************************************************************************************************
  * Parses manifest array into update.rdf
  ********************************************************************************************************************/
  public function createSearchResults($aManifests, $aDirectOutput = null) {
    global $gaRuntime;
    $count = 0;
    $warnings = EMPTY_ARRAY;

    // Create the root searchresults element
    $searchResults = ['@element' => 'searchresults', '@attributes' => EMPTY_ARRAY];

    // Make sure aManifests is actually and array and an indexed list of manifests
    if (!is_array($aManifests) || !array_is_list($aManifests)) {
      // Log a warning if it is not
      $warnings[] = 'Not a list of search results.';

      // Make aManifests an empty array so that the subsequent foreach won't bitch.
      $aManifests = EMPTY_ARRAY;
    }

    // Loop through manifests to create the structure for a search result add-on
    // If it is null then assume empty array and pass through
    foreach ($aManifests as $_key => $_value) {
      $_addon = ['@element' => 'addon'];

      $_addon[] = ['@element' => 'beer', '@content' => $_value['beer']]; 

      $searchResults[] = $_addon;
    }

    // If the count has not be increased then there are no results so log a warning.
    if ($count == 0) {
      $warnings[] = 'No results.';
    }

    // Attach the total number of results to the searchresults element
    $searchResults['@attributes']['total_results'] = $count;

    // If we are in debug mode and we have warnings then create a phobos element
    // and emit the warnings as warning elements.
    // We do this so that this method has a safe failure that won't piss off either the
    // xml parser or the code that consumes the search results in the Add-ons Manager code.
    if ($gaRuntime['debugMode'] && $warnings != EMPTY_ARRAY) {
      // Create phobos element
      $warningResults = ['@element' => 'phobos'];

      // Loop through warnings and create warning elements and attach them to the phobos element.
      foreach ($warnings as $_value) {
        $warningResults[] = ['@element' => 'warning', '@content' => $_value];
      }

      // Attach the phobos element with the warnings to the search results element.
      $searchResults[] = $warningResults;
    }

    // Create the XML and return if not direct output.
    return gfCreateXML($searchResults, $aDirectOutput);
  }
}
?>
