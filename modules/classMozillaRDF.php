<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

class classMozillaRDF {
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
  const SINGLE_PROPS  = ['id', 'type', 'version', 'creator', 'homepageURL', 'updateURL', 'updateKey', 'bootstrap',
                         'skinnable', 'strictCompatibility', 'iconURL', 'icon64URL', 'optionsURL', 'optionsType',
                         'aboutURL', 'iconURL', 'unpack', 'multiprocessCompatible', 'hasEmbeddedWebExtension',
                         'slug', 'category', 'license', 'repositoryURL', 'supportURL', 'supportEmail'];

  // Multiple properties (because this is shared with other methods we use a class constant)
  // According to documentation, em:file is supposed to be used as a fallback when no chrome.manifest exists.
  // It would then use em:file and old style contents.rdf to generate a chrome manifest but I cannot find
  // any existing code to facilitate this at our level.
  // em:additionalLicenses is a Phobos-only multi-prop
  const MULTI_PROPS   = ['contributor', 'developer', 'translator', 'additionalLicenses',
                         'targetPlatform', 'localized', 'targetApplication'];

  /********************************************************************************************************************
  * Parses install.rdf using Rdf_parser class
  *
  * @param string     $aManifestData
  * @return array     $data["manifest"]
  ********************************************************************************************************************/
  public function parseInstallManifest($aManifestData, $aReturnAllData = null, $aMangleLocalized = null) {
    $ePrefix = __CLASS__ . DBLCOLON . __FUNCTION__ . DASH_SEPARATOR;
    $data = EMPTY_ARRAY;

    // ----------------------------------------------------------------------------------------------------------------

    require_once(LIBRARIES['rdfParser']);
    $rdf = new Rdf_parser();
    $rdf->rdf_parser_create(null);
    $rdf->rdf_set_user_data($data);
    $rdf->rdf_set_statement_handler(['classMozillaRDF', 'mfStatementHandler']);
    $rdf->rdf_set_base(EMPTY_STRING);

    if (!$rdf->rdf_parse($aManifestData, strlen($aManifestData), true)) {
      gfError('<strong>RDF Parsing Error' . COLON . '</strong>' . SPACE .
              xml_error_string(xml_get_error_code($rdf->rdf_parser['xml_parser'])));
    }

    // ----------------------------------------------------------------------------------------------------------------

    // Get em:name and em:description from em:localized
    if (array_key_exists('localized', $data['manifest']) &&
        is_array($data['manifest']['localized'])) {
      foreach ($data['manifest']['localized'] as $_value) {
        $_locale = $data[$_value][self::EM_NS . 'locale'];

        if (array_key_exists(self::EM_NS . 'name', $data[$_value])) {
          if ($data['manifest']['name']['en-US'] != $data[$_value][self::EM_NS . 'name']) {
            $data['manifest']['name'][$_locale] = $data[$_value][self::EM_NS . 'name'];
          }
        }

        if (array_key_exists(self::EM_NS . 'description', $data[$_value])) {
          if ($data['manifest']['description']['en-US'] != $data[$_value][self::EM_NS . 'description']) {
            $data['manifest']['description'][$_locale] = $data[$_value][self::EM_NS . 'description'];
          }
        }

        unset($data[$_value]);
      }

      unset($data['manifest']['localized']);
    }

    // XXXTobin: This is fuckin gross but it is a best effort hack to merge em:localized contributors,
    // developers, and translators to their top level values
    if ($aMangleLocalized) {
      $mangledManifest = gfSubSt('string', ['RDF:' => 'RDF_', 'em:' => 'em_'], $aManifestData);
      $mangledManifest = @simplexml_load_string($mangledManifest);
      $mangledManifest = gfObjectToArray($mangledManifest);
      $mangledManifest = $mangledManifest['Description']['em_localized'] ?? null;

      if (gfSuperVar('check', $mangledManifest)) {
        $mangledData = ['contributor' => EMPTY_ARRAY, 'developer' => EMPTY_ARRAY, 'translator' => EMPTY_ARRAY];
        foreach ($mangledManifest as $_value) {
          if (array_key_exists('em_contributor', $_value['Description'])) {
            if (is_array($_value['Description']['em_contributor'])) {
              $mangledData['contributor'] = array_merge($mangledData['contributor'],
                                                        $_value['Description']['em_contributor'] ?? EMPTY_ARRAY);
            }
            else {
              $mangledData['contributor'][] = $_value['Description']['em_contributor'];
            }
          }

          if (array_key_exists('em_developer', $_value['Description'])) {
            if (is_array($_value['Description']['em_developer'])) {
            $mangledData['developer']   = array_merge($mangledData['developer'],
                                                      $_value['Description']['em_developer'] ?? EMPTY_ARRAY);
            }
            else {
              $mangledData['developer'][] = $_value['Description']['em_developer'];
            }
          }

          if (array_key_exists('em_translator', $_value['Description'])) {
            if (is_array($_value['Description']['em_translator'])) {
            $mangledData['translator']  = array_merge($mangledData['translator'],
                                                      $_value['Description']['em_translator'] ?? EMPTY_ARRAY);
            }
            else {
              $mangledData['translator'][] = $_value['Description']['em_translator'];
            }
          }
        }

        foreach ($mangledData as $_key => $_value) {
          if ($_value != EMPTY_ARRAY) {

            if (array_key_exists($_key, $data['manifest'])) {
              $data['manifest'][$_key] = array_merge($data['manifest'][$_key], $_value);
            }
            else {
              $data['manifest'][$_key] = $_value;
            }

            unset($_value['@attributes']);
          }
        }

        foreach (array_keys($mangledData) as $_value) {
          if (array_key_exists($_value, $data['manifest'])) {
            $data['manifest'][$_value] = array_values(array_unique($data['manifest'][$_value]));
          }
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
    $targetApplication = EMPTY_ARRAY;
    if (array_key_exists('targetApplication', $data['manifest']) &&
        is_array($data['manifest']['targetApplication'])) {
      foreach ($data['manifest']['targetApplication'] as $_value) {
        $id = $data[$_value][self::EM_NS . "id"];
        $targetApplication[$id]['minVersion'] = $data[$_value][self::EM_NS . 'minVersion'];
        $targetApplication[$id]['maxVersion'] = $data[$_value][self::EM_NS . 'maxVersion'];
        unset($data[$_value]);
      }
    }

    unset($data['manifest']['targetApplication']);
    $data['manifest']['targetApplication'] = $targetApplication;

    // ----------------------------------------------------------------------------------------------------------------

    $rdf->rdf_parser_free();
    return $aReturnAllData ? $data : $data['manifest'];
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
      $length = strlen(self::EM_NS);
      if (strncmp($aPredicate, self::EM_NS, $length) == 0) {
        $prop = substr($aPredicate, $length, strlen($aPredicate)-$length);

        if (in_array($prop, self::SINGLE_PROPS)) {
          $aData['manifest'][$prop] = $aObject;
        }
        elseif (in_array($prop, self::MULTI_PROPS)) {
          $aData['manifest'][$prop][] = $aObject;
        }
        elseif (in_array($prop, ['name', 'description'])) {
          $aData['manifest'][$prop][($aXmlLang ? $aXmlLang  : 'en-US')] = $aObject;
        }
      }
    }
    else {
      // Save it anyway
      $aData[$aSubject][$aPredicate] = $aObject;
    }

    return $aData;
  }

  /********************************************************************************************************************
  * Parses manifest array into install.rdf
  ********************************************************************************************************************/
  public function createInstallManifest($aManifest, $aFormatAttrs = null, $aDirectOutput = null) {
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

    // Fix up em:name
    if (is_array($aManifest['name'])) {
      $aManifest['name'] = $aManifest['name']['en-US'];
    }

    // Fix up em:description
    if (is_array($aManifest['description'])) {
      $aManifest['description'] = $aManifest['description']['en-US'];
    }

    // aboutURL is the add-on's about box NOT website
    if (array_key_exists('aboutURL', $aManifest)) {
      if (!str_starts_with($aManifest['aboutURL'], 'chrome://')) {
        unset($aManifest['aboutURL']);
      }
    }

    // multiprocessCompatible means nothing to us
    if (array_key_exists('multiprocessCompatible', $aManifest)) {
      unset($aManifest['multiprocessCompatible']);
    }

    // We tend to mangle homepageURL to repositoryURL when it is a known forge
    // However, we should mangle back unless both are used.
    if (!array_key_exists('homepageURL', $aManifest)) {
      if (array_key_exists('repositoryURL', $aManifest)) {
        $aManifest['homepageURL'] = $aManifest['repositoryURL'];
        unset($aManifest['repositoryURL']);
      }
    }

    // Add single props as attributes to the main description
    foreach ($aManifest as $_key => $_value) {
      if (in_array($_key, self::MULTI_PROPS)) {
        continue;
      }

      $mainDescription['@attributes']['em:' . $_key] = $_value;
    }

    // Add multiprops as elements
    foreach (['em:contributor'        => $aManifest['contributor'] ?? null,
              'em:developer'          => $aManifest['developer'] ?? null,
              'em:translator'         => $aManifest['translator'] ?? null,
              'em:additionalLicenses' => $aManifest['additionalLicenses'] ?? null,
              'em:targetPlatform'     => $aManifest['targetPlatform'] ?? null]
             as $_key => $_value) {
      if (!$_value) {
        continue;
      }

      foreach ($_value as $_value2) {
        $mainDescription[] = ['@element' => $_key, '@content' => $_value2];
      }
    }

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

    // Attach the main description to the root element
    $installManifest[] = $mainDescription;

    // Generate XML (or RDF in this case)
    $installManifest = gfGenerateXML($installManifest);
    
    // This is a hack to format the attrs of the main description so they aren't just one long line
    if ($aFormatAttrs) {
      $substs = array(
        ' em:'                                              => NEW_LINE . '               em:',
        '<Description' . NEW_LINE . '               em:id'  => '<Description em:id',
        NEW_LINE . '               em:minVersion'           => SPACE . 'em:minVersion',
        NEW_LINE . '               em:maxVersion'           => SPACE . 'em:maxVersion'
      );

      $installManifest = gfSubst('string', $substs, $installManifest);
    }

    if ($aDirectOutput) {
      gfHeader('xml');
      print($installManifest);
      exit();
    }

    return $installManifest;
  }

  /********************************************************************************************************************
  * Parses manifest array into update.rdf
  ********************************************************************************************************************/
  public function createUpdateManifest($aManifest, $aDirectOutput = null) {
    // XXXTobin: This is for testing only
    if (!array_key_exists('updateLink', $aManifest)) {
      $aManifest['updateLink'] = 'about:blank';
      $aManifest['updateHash'] = 'null';
    }

    $aManifest['type'] = AUS_XPI_TYPES[$aManifest['type']] ?? 'item';

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
            'em:updateLink' => '<![CDATA[' . $aManifest['updateLink'] . ']]>',
            'em:updateHash' => 'sha256:' . $aManifest['updateHash'],
          )
        )
      );
    }    

    return gfGenerateXML($updateManifest, $aDirectOutput);
  }
}
?>
