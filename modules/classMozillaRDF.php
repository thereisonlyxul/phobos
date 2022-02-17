<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

class classMozillaRDF {
  const RDF_NS = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
  const EM_NS = 'http://www.mozilla.org/2004/em-rdf#';
  const MF_RES = 'urn:mozilla:install-manifest';
  const ANON_PREFIX = '#genid';
  const MULTI_PROPS = ['contributor', 'developer', 'translator', 'targetPlatform', 'targetApplication'];

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
    $rdf->rdf_set_statement_handler(array('classMozillaRDF', 'installManifestStatementHandler'));
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
  * @param string     $manifestData
  * @param array      &$data
  * @param string     $subjectType
  * @param string     $subject
  * @param string     $predicate
  * @param int        $ordinal
  * @param string     $objectType
  * @param string     $object
  * @param string     $xmlLang
  ********************************************************************************************************************/
  static function installManifestStatementHandler(&$data,
                                                  $subjectType,
                                                  $subject,
                                                  $predicate,
                                                  $ordinal,
                                                  $objectType,
                                                  $object,
                                                  $xmlLang) {
    // Single properties
    $singleProps = ['id', 'type', 'version', 'creator', 'homepageURL', 'updateURL', 'updateKey', 'bootstrap',
                    'skinnable', 'strictCompatibility', 'iconURL', 'icon64URL', 'optionsURL', 'optionsType',
                    'aboutURL', 'iconURL', 'unpack'];

    // These props are pretty much invalid but it would be wise to parse them so we can check against them.
    $singleProps[] = 'multiprocessCompatible';
    $singleProps[] = 'hasEmbeddedWebExtension';

    // We support an em:license but the Add-ons Manager doesn't. Still, keep it separate from "real" props.
    $singleProps[] = 'license';

    // Multiple properties
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
    if ($subject == self::MF_RES) {
      //we're only really interested in EM properties
      $length = strlen(self::EM_NS);
      if (strncmp($predicate, self::EM_NS, $length) == 0) {
        $prop = substr($predicate, $length, strlen($predicate)-$length);

        if (in_array($prop, $singleProps) &&
            !str_starts_with($object, self::ANON_PREFIX) &&
            $object != 'false') {
          $data['manifest'][$prop] = $object;
        }
        elseif (in_array($prop, $multiProps)) {
          $data['manifest'][$prop][] = $object;
        }
        elseif (in_array($prop, $localeProps)) {
          $lang = ($xmlLang) ? $xmlLang : 'en-US';
          $data['manifest'][$prop][$lang] = $object;
        }
      }
    }
    else {
      // Save it anyway
      $data[$subject][$predicate] = $object;
    }
    
    return $data;
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

    // Add single props as attributes to the main description
    foreach ($aManifest as $_key => $_value) {
      if (in_array($_key, ['contributor', 'developer', 'translator', 'targetPlatform', 'targetApplication'])) {
        continue;
      }

      $mainDescription['@attributes']['em:' . $_key] = $_value;
    }

    // em:developer seems redundant with the preferred em:contributor prop
    if (array_key_exists('developer', $aManifest)) {
      if (array_key_exists('contributor', $aManifest)) {
        $aManifest['contributor'] = array_unique(array_merge($aManifest['contributor'], $aManifest['developer']));
      }
      else {
        $aManifest['contributor'] = $aManifest['developer'];
      }

      unset($aManifest['developer']);
    }

    // Add multiprops as elements
    foreach (['em:contributor'    => $aManifest['contributor'] ?? null,
              'em:developer'      => $aManifest['developer'] ?? null,
              'em:translator'     => $aManifest['translator'] ?? null,
              'em:targetPlatform' => $aManifest['targetPlatform'] ?? null] as $_key => $_value) {
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
}

?>
