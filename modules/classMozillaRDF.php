<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

class classMozillaRDF {
  const RDF_NS      = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
  const EM_NS       = 'http://www.mozilla.org/2004/em-rdf#';
  const MF_RES      = 'urn:mozilla:install-manifest';
  const ANON_PREFIX = '#genid';
  const MULTI_PROPS = ['contributor', 'developer', 'translator', 'additionalLicenses',
                       'targetPlatform', 'targetApplication'];

  /********************************************************************************************************************
  * Parses install.rdf using Rdf_parser class
  *
  * @param string     $aManifestData
  * @return array     $data["manifest"]
  ********************************************************************************************************************/
  public function parseInstallManifest($aManifestData) {
    require_once(LIBRARIES['rdfParser']);
    $rdf = new Rdf_parser();

    $data = array();

    $rdf->rdf_parser_create(null);
    $rdf->rdf_set_user_data($data);
    $rdf->rdf_set_statement_handler(array('classMozillaRDF', 'mfStatementHandler'));
    $rdf->rdf_set_base(EMPTY_STRING);

    if (!$rdf->rdf_parse($aManifestData, strlen($aManifestData), true)) {
      return xml_error_string(xml_get_error_code($rdf->rdf_parser['xml_parser']));
    }

    // Set the targetApplication data
    $targetArray = array();
    if (!empty($data['manifest']['targetApplication']) && is_array($data['manifest']['targetApplication'])) {
      foreach ($data['manifest']['targetApplication'] as $targetApp) {
        if (str_starts_with($data[$targetApp][self::EM_NS . "id"], self::ANON_PREFIX) ||
            str_starts_with($data[$targetApp][self::EM_NS . 'minVersion'], self::ANON_PREFIX) ||
            str_starts_with($data[$targetApp][self::EM_NS . 'maxVersion'], self::ANON_PREFIX)) {
          gfError('em:targetApplication description tags/attributes em:id, em:minVersion, and em:maxVersion MUST have a value');
        }

        $id = $data[$targetApp][self::EM_NS . "id"];
        $targetArray[$id]['minVersion'] = $data[$targetApp][self::EM_NS . 'minVersion'];
        $targetArray[$id]['maxVersion'] = $data[$targetApp][self::EM_NS . 'maxVersion'];
      }
    }

    $data['manifest']['targetApplication'] = $targetArray;

    $rdf->rdf_parser_free();

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
    // Single properties
    $singleProps = ['id', 'type', 'version', 'creator', 'homepageURL', 'updateURL', 'updateKey', 'bootstrap',
                    'skinnable', 'strictCompatibility', 'iconURL', 'icon64URL', 'optionsURL', 'optionsType',
                    'aboutURL', 'iconURL', 'unpack'];

    // These props are pretty much invalid but it would be wise to parse them so we can check against them.
    $singleProps[] = 'multiprocessCompatible';
    $singleProps[] = 'hasEmbeddedWebExtension';

    // We support additional em:properties but the Add-ons Manager doesn't. Still, keep it separate from "real" props.
    $singleProps[] = 'slug';
    $singleProps[] = 'license';
    $singleProps[] = 'supportURL';
    $singleProps[] = 'supportEmail';
    $singleProps[] = 'repositoryURL';

    // Multiple properties (because this is shared with other methods we use a class constant)
    // According to documentation, em:file is supposed to be used as a fallback when no chrome.manifest exists.
    // It would then use em:file and old style contents.rdf to generate a chrome manifest but I cannot find
    // any existing code to facilitate this at our level. AND NO I am not gonna add it back despite pining for
    // true XPInstall.
    $multiProps = self::MULTI_PROPS;
    
    // localizable properties
    // The documentation states that creator, homepageURL, and additional multiprops
    // contributor, developer, and translator are localizable though this makes no god damned sense
    // and will be dropped once we are install.json.. So don't even honor it.
    // NAMES specifically should never be localized and credit should be due regardless of the fe language.
    $localeProps = ['name', 'description'];

    //Look for properties on the install manifest itself
    if ($aSubject == self::MF_RES) {
      //we're only really interested in EM properties
      $length = strlen(self::EM_NS);
      if (strncmp($aPredicate, self::EM_NS, $length) == 0) {
        $prop = substr($aPredicate, $length, strlen($aPredicate)-$length);

        if (in_array($prop, $singleProps) &&
            !str_starts_with($aObject, self::ANON_PREFIX) &&
            $aObject != 'false') {
          $aData['manifest'][$prop] = $aObject;
        }
        elseif (in_array($prop, $multiProps)) {
          $aData['manifest'][$prop][] = $aObject;
        }
        elseif (in_array($prop, $localeProps)) {
          $lang = ($aXmlLang) ? $aXmlLang : 'en-US';
          $aData['manifest'][$prop][$lang] = $aObject;
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

    // em:developer seems redundant with the preferred em:contributor prop
    if (array_key_exists('developer', $aManifest)) {
      if (array_key_exists('contributor', $aManifest)) {
        $aManifest['contributor'] = array_unique(array_merge($aManifest['contributor'],
                                                             $aManifest['developer']));
      }
      else {
        $aManifest['contributor'] = $aManifest['developer'];
      }

      unset($aManifest['developer']);
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
