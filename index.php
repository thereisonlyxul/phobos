<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Setup | =======================================================================================================

error_reporting(E_ALL);
ini_set("display_errors", "on");

// --------------------------------------------------------------------------------------------------------------------

// This has to be defined using the function at runtime because it is based
// on a variable. However, constants defined with the language construct
// can use this constant by some strange voodoo. Keep an eye on this.
// NOTE: DOCUMENT_ROOT does NOT have a trailing slash.
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);

// Define basic constants for the software
const SOFTWARE_NAME       = 'Phoebus Ascendant';
const SOFTWARE_VERSION    = '3.0.0a1';
const DATASTORE_RELPATH   = '/datastore/';
const OBJ_RELPATH         = '/.obj/';
const BASE_RELPATH        = '/base/';
const COMPONENTS_RELPATH  = '/components/';
const DATABASES_RELPATH   = '/databases/';
const MODULES_RELPATH     = '/modules/';
const LIB_RELPATH         = '/libraries/';
const NEW_LINE            = "\n";

// Define components
const COMPONENTS = array(
  'site'            => ROOT_PATH . BASE_RELPATH . 'site.php',
  'special'         => ROOT_PATH . BASE_RELPATH . 'special.php',
  'aus'             => ROOT_PATH . COMPONENTS_RELPATH . 'aus/addonUpdateService.php',
  'discover'        => ROOT_PATH . COMPONENTS_RELPATH . 'api/discoverPane.php',
  'download'        => ROOT_PATH . COMPONENTS_RELPATH . 'download/addonDownload.php',
  'integration'     => ROOT_PATH . COMPONENTS_RELPATH . 'api/amIntegration.php',
  'panel'           => ROOT_PATH . COMPONENTS_RELPATH . 'panel/phoebusPanel.php',
);

// Define modules
const MODULES = array(
  'account'         => ROOT_PATH . MODULES_RELPATH . 'classAccount.php',
  'database'        => ROOT_PATH . MODULES_RELPATH . 'classDatabase.php',
  'generateContent' => ROOT_PATH . MODULES_RELPATH . 'classGenerateContent.php',
  'mozillaRDF'      => ROOT_PATH . MODULES_RELPATH . 'classMozillaRDF.php',
  'vc'              => ROOT_PATH . MODULES_RELPATH . 'nsIVersionComparator.php',
);

// Define databases
const DATABASES = array(
  'emailBlacklist'  => ROOT_PATH . DATABASES_RELPATH . 'emailBlacklist.php',
  'searchPlugins'   => ROOT_PATH . DATABASES_RELPATH . 'searchPlugins.php',
);

// Define libraries
const LIBRARIES = array(
  'smarty'          => ROOT_PATH . LIB_RELPATH . 'smarty/libs/Smarty.class.php',
  'safeMySQL'       => ROOT_PATH . LIB_RELPATH . 'safemysql/safemysql.class.php',
  'rdfParser'       => ROOT_PATH . LIB_RELPATH . 'librdf/rdf_parser.php',
);

const DEVELOPER_DOMAIN = 'addons-dev.palemoon.org';

// Define Domains for Applications
const APPLICATION_DOMAINS = array(
  'addons.palemoon.org'           => 'palemoon',
  'addons-dev.palemoon.org'       => 'palemoon',
  'addons.basilisk-browser.org'   => 'basilisk',
  'addons.binaryoutcast.com'      => ['borealis', 'interlink'],
);

// --------------------------------------------------------------------------------------------------------------------

/* Known Application IDs
 * Application IDs are normally in the form of a {GUID} or user@host ID.
 *
 * Firefox:          {ec8030f7-c20a-464f-9b0e-13a3a9e97384}
 * Thunderbird:      {3550f703-e582-4d05-9a08-453d09bdfdc6}
 * SeaMonkey:        {92650c4d-4b8e-4d2a-b7eb-24ecf4f6b63a}
 * Fennec (Android): {aa3c5121-dab2-40e2-81ca-7ea25febc110}
 * Fennec (XUL):     {a23983c0-fd0e-11dc-95ff-0800200c9a66}
 * Sunbird:          {718e30fb-e89b-41dd-9da7-e25a45638b28}
 * Instantbird:      {33cb9019-c295-46dd-be21-8c4936574bee}
 * Adblock Browser:  {55aba3ac-94d3-41a8-9e25-5c21fe874539} */

const TOOLKIT_ID    = 'toolkit@mozilla.org';
const TOOLKIT_ALTID = 'toolkit@palemoon.org';
const TOOLKIT_BIT   = 1;

// --------------------------------------------------------------------------------------------------------------------

// Define application metadata
const TARGET_APPLICATION = array(
  'palemoon' => array(
    'id'            => '{8de7fcbb-c55c-4fbe-bfc5-fc555c87dbc4}',
    'bit'           => 2,
    'name'          => 'Pale Moon',
    'shortName'     => null,
    'commonType'    => 'browser',
    'siteTitle'     => 'Pale Moon - Add-ons',
    'features'      => ['https', 'extensions', 'extensions-cat', 'themes',
                        'personas', 'language-packs', 'search-plugins']
  ),
  'basilisk' => array(
    'id'            => '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}',
    'bit'           => 4,
    'name'          => 'Basilisk',
    'shortName'     => null,
    'commonType'    => 'browser',
    'siteTitle'     => 'Basilisk: add-ons',
    'features'      => ['https', 'extensions', 'themes', 'personas', 'search-plugins']
  ),
  'borealis' => array(
    'id'            => '{a3210b97-8e8a-4737-9aa0-aa0e607640b9}',
    'bit'           => 8,
    'name'          => 'Borealis Navigator',
    'shortName'     => 'Borealis',
    'commonType'    => 'navigator',
    'siteTitle'     => 'Add-ons - Binary Outcast',
    'features'      => ['unified', 'extensions', 'search-plugins']
  ),
  'interlink' => array(
    'id'            => '{3550f703-e582-4d05-9a08-453d09bdfdc6}',
    'bit'           => 16,
    'name'          => 'Interlink Mail &amp; News',
    'shortName'     => 'Interlink',
    'commonType'    => 'client',
    'siteTitle'     => 'Add-ons - Binary Outcast',
    'features'      => ['unified', 'extensions', 'themes', 'search-plugins', 'disable-xpinstall']
  ),
  'ambassador' => array(
    'id'            => '{4523665a-317f-4a66-9376-3763d1ad1978}',
    'bit'           => 32,
    'name'          => 'Ambassador',
    'shortName'     => null,
    'commonType'    => 'client',
    'siteTitle'     => 'Add-ons - Ambassador',
    'features'      => ['extensions', 'themes', 'disable-xpinstall']
  ),
);

// --------------------------------------------------------------------------------------------------------------------

const ADDON_TYPES = array(
  'app'             => 1, // No longer applicable
  'extension'       => 2,
  'theme'           => 4,
  'locale'          => 8,
  'plugin'          => 16, // No longer applicable
  'multipackage'    => 32, // Forbidden on Phoebus
  'dictionary'      => 64,
  'experiment'      => 128, // Not used in UXP
  'apiextension'    => 256, // Not used in UXP
  'persona'         => 512, // Phoebus only
  'search-plugin'   => 1024, // Phoebus only
);

const BAD_XPI_TYPES = 1 | 16 | 32 | 128 | 256 | 512 | 1024;
const AUS_TYPES = [2 => 'extension', 4 => 'theme', 8 => 'item', 64 => 'item'];

// --------------------------------------------------------------------------------------------------------------------

const EXTENSION_CATEGORY = ['name' => 'Extensions', 'type' => 2];

const CATEGORIES = array(
  'alerts-and-updates'        => ['name' => 'Alerts &amp; Updates',
                                  'type' => 2],
  'appearance'                => ['name' => 'Appearance',
                                  'type' => 2],
  'bookmarks-and-tabs'        => ['name' => 'Bookmarks &amp; Tabs',
                                  'type' => 2],
  'download-management'       => ['name' => 'Download Management',
                                  'type' => 2],
  'feeds-news-and-blogging'   => ['name' => 'Feeds, News, &amp; Blogging',
                                  'type' => 2],
  'privacy-and-security'      => ['name' => 'Privacy &amp; Security',
                                  'type' => 2],
  'search-tools'              => ['name' => 'Search Tools',
                                  'type' => 2],
  'social-and-communication'  => ['name' => 'Social &amp; Communication',
                                  'type' => 2],
  'tools-and-utilities'       => ['name' => 'Tools &amp; Utilities',
                                  'type' => 2],
  'web-development'           => ['name' => 'Web Development',
                                  'type' => 2],
  'other'                     => ['name' => 'Other',
                                  'type' => 2],
  'themes'                    => ['name' => 'Themes',
                                  'type' => 4],
  'language-packs'            => ['name' => 'Language Packs',
                                  'type' => 8],
  'dictionaries'              => ['name' => 'Dictionaries',
                                  'type' => 64],
  'personas'                  => ['name' => 'Personas',
                                  'type' => 512],
  'search-plugins'            => ['name' => 'Search Plugins',
                                  'type' => 1024],
);

// --------------------------------------------------------------------------------------------------------------------

const LICENSES = array(
  'Apache-2.0'                => 'Apache License 2.0',
  'Apache-1.1'                => 'Apache License 1.1',
  'BSD-3-Clause'              => 'BSD 3-Clause',
  'BSD-2-Clause'              => 'BSD 2-Clause',
  'GPL-3.0'                   => 'GNU General Public License 3.0',
  'GPL-2.0'                   => 'GNU General Public License 2.0',
  'LGPL-3.0'                  => 'GNU Lesser General Public License 3.0',
  'LGPL-2.1'                  => 'GNU Lesser General Public License 2.1',
  'AGPL-3.0'                  => 'GNU Affero General Public License v3',
  'MIT'                       => 'MIT License',
  'MPL-2.0'                   => 'Mozilla Public License 2.0',
  'MPL-1.1'                   => 'Mozilla Public License 1.1',
  'Custom'                    => 'Custom License',
  'PD'                        => 'Public Domain',
  'COPYRIGHT'                 => ''
);

// ====================================================================================================================

require_once('./globalFunctions.php');

// ====================================================================================================================

// == | Main | ========================================================================================================

// Define an array that will hold the current application state
$gaRuntime = array(
  'authentication'      => null,
  'currentApplication'  => null,
  'orginalApplication'  => null,
  'unified'             => null,
  'unifiedApps'         => null,
  'unifiedDomain'       => null,
  'currentSiteTitle'    => null,
  'currentScheme'       => gfSuperVar('server', 'SCHEME'),
  'currentDomain'       => null,
  'debugMode'           => null,
  'phpServerName'       => gfSuperVar('server', 'SERVER_NAME'),
  'phpRequestURI'       => gfSuperVar('server', 'REQUEST_URI'),
  'remoteAddr'          => gfSuperVar('server', 'HTTP_X_FORWARDED_FOR') ?? gfSuperVar('server', 'REMOTE_ADDR'),
  'requestComponent'    => gfSuperVar('get', 'component'),
  'requestPath'         => gfSuperVar('get', 'path'),
  'requestApplication'  => gfSuperVar('get', 'appOverride'),
  'requestDebugOff'     => gfSuperVar('get', 'debugOff'),
  'requestSearchTerms'  => gfSuperVar('get', 'terms')
);

// --------------------------------------------------------------------------------------------------------------------

// Decide which application by domain that the software will be serving
if (array_key_exists($gaRuntime['phpServerName'], APPLICATION_DOMAINS)) {
  $gaRuntime['currentDomain'] = $gaRuntime['phpServerName'];
  $gaRuntime['currentApplication'] = APPLICATION_DOMAINS[$gaRuntime['currentDomain']];

  // See if this is a unified add-ons site
  if (is_array($gaRuntime['currentApplication'])) {
    $gaRuntime['unified'] = true;
    $gaRuntime['unifiedDomain'] = $gaRuntime['currentDomain'];
    $gaRuntime['unifiedApps'] = $gaRuntime['currentApplication'];
    $gaRuntime['currentApplication'] = true;
  }

  // If this is the developer domain then switch debug on
  if ($gaRuntime['currentDomain'] == DEVELOPER_DOMAIN) {
    $gaRuntime['debugMode'] = true;
  }
}
else {
  gfError('Invalid domain/application');
}

// --------------------------------------------------------------------------------------------------------------------

// Items that get changed depending on debug mode
if ($gaRuntime['debugMode']) {
  // We can disable debug mode when on the dev url otherwise if debug mode we want all errors
  if ($gaRuntime['requestDebugOff']) {
    $gaRuntime['debugMode'] = null;
  }

  // In debug mode we need to test other applications
  if ($gaRuntime['requestApplication']) {
    // We can't test an application that doesn't exist
    if (!array_key_exists($gaRuntime['requestApplication'], TARGET_APPLICATION)) {
      gfError('Invalid override application');
    }

    // Stupidity check
    if ($gaRuntime['requestApplication'] == $gaRuntime['currentApplication']) {
      gfError('It makes no sense to override to the same application');
    }

    // Set the application
    $gaRuntime['orginalApplication'] = $gaRuntime['currentApplication'];
    $gaRuntime['currentApplication'] = $gaRuntime['requestApplication'];

    // If this is a unified add-ons site then we need to try and figure out the domain
    if (in_array('unified', TARGET_APPLICATION[$gaRuntime['currentApplication']]['features'])) {
      // Switch unified mode on
      $gaRuntime['unified'] = true;

      // Loop through the domains
      foreach (APPLICATION_DOMAINS as $_key => $_value) {
        // Skip any value that isn't an array
        if (!is_array($_value)) {
          continue;
        }

        // If we hit a domain with the requested application in it we have found our domain
        if (in_array($gaRuntime['currentApplication'], $_value)) {
          $gaRuntime['unifiedDomain'] = $_key;
          $gaRuntime['unifiedApps'] = $_value;
          $gaRuntime['currentApplication'] = true;
          break;
        }
      }

      // Final check to make sure we have a unified domain figured out
      if (!$gaRuntime['unifiedDomain']) {
        gfError('Invalid unified domain');
      }
    }
  }
}

// --------------------------------------------------------------------------------------------------------------------

// If the entire site is offline but nothing above is busted.. We want to serve proper but empty responses
if (file_exists(ROOT_PATH . '/.offline')) {
  require_once(ROOT_PATH . '/mini.php');
}

// --------------------------------------------------------------------------------------------------------------------

// We cannot continue without a valid currentDomain
if (!$gaRuntime['currentDomain']) {
  gfError('Invalid domain');
}

// We cannot continue without a valid currentApplication or at least a true value in unified mode
if (!$gaRuntime['currentApplication']) {
  gfError('Invalid application');
}

// --------------------------------------------------------------------------------------------------------------------

// Root (/) won't set a component or path
if (!$gaRuntime['requestComponent'] && !$gaRuntime['requestPath']) {
  $gaRuntime['requestComponent'] = 'site';
  $gaRuntime['requestPath'] = '/';
}
// The PANEL component overrides the SITE component
elseif (str_starts_with($gaRuntime['phpRequestURI'], '/panel/')) {
  $gaRuntime['requestComponent'] = 'panel';
}
// The SPECIAL component overrides the SITE component
elseif (str_starts_with($gaRuntime['phpRequestURI'], '/special/')) {
  $gaRuntime['requestComponent'] = 'special';
}

// --------------------------------------------------------------------------------------------------------------------

// Load component based on requestComponent
if ($gaRuntime['requestComponent'] && array_key_exists($gaRuntime['requestComponent'], COMPONENTS)) {
  $gaRuntime['explodedPath'] = gfSplitPath($gaRuntime['requestPath']);
  require_once(COMPONENTS[$gaRuntime['requestComponent']]);
}
else {
  if (!$gaRuntime['debugMode']) {
    gfHeader(404);
  }
  gfError('Invalid component');
}

// ====================================================================================================================

?>