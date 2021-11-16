<?php
// == | Setup | =======================================================================================================

// Enable Error Reporting
error_reporting(E_ALL);
ini_set("display_errors", "on");
ini_set('html_errors', false);

// This has to be defined using the function at runtime because it is based
// on a variable. However, constants defined with the language construct
// can use this constant by some strange voodoo. Keep an eye on this.
// NOTE: DOCUMENT_ROOT does NOT have a trailing slash.
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);

// Debug flag
define('DEBUG_MODE', $_GET['debug'] ?? null);

// Define basic constants for the software
const SOFTWARE_NAME       = 'Phobos';
const SOFTWARE_VERSION    = '3.0.0a1';
const SOFTWARE_REPO       = 'about:blank';
const DATASTORE_RELPATH   = '/datastore/';
const OBJ_RELPATH         = '/.obj/';
const BASE_RELPATH        = '/base/';
const SKIN_RELPATH        = '/skin/';
const COMPONENTS_RELPATH  = '/components/';
const MODULES_RELPATH     = '/modules/';
const LIB_RELPATH         = '/libs/';

// Define components
const COMPONENTS = array(
  'site'            => ROOT_PATH . BASE_RELPATH . 'site.php',
  'special'         => ROOT_PATH . BASE_RELPATH . 'special.php',
);

// Define modules
const MODULES = array(
  'generateContent' => ROOT_PATH . MODULES_RELPATH . 'classGenerateContent.php'
);

// Define JS Modules
const JSMODULES = null;

// Define libraries
const LIBRARIES = null;

// Load fundamental constants and global functions
require_once('./fundamentals.php');

// --------------------------------------------------------------------------------------------------------------------

const XML_API_SEARCH_BLANK  = '<searchresults total_results="0" />';
const XML_API_LIST_BLANK    = '<addons />';
const XML_API_ADDON_ERROR   = '<error>Add-on not found!</error>';
const RDF_AUS_BLANK         = '<RDF:RDF xmlns:RDF="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:em="http://www.mozilla.org/2004/em-rdf#" />';

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
const OLD_PM_ID     = '{8de7fcbb-c55c-4fbe-bfc5-fc555c87dbc4}';

// --------------------------------------------------------------------------------------------------------------------

const DEVELOPER_DOMAIN = 'addons-dev.palemoon.org';

// Define Domains for Applications
const APPLICATION_DOMAINS = array(
  'palemoon.org'           => 'palemoon',
  'binaryoutcast.com'      => ['borealis', 'interlink'],
);

// --------------------------------------------------------------------------------------------------------------------

// Define application metadata
const TARGET_APPLICATION = array(
  'palemoon' => array(
    'id'            => '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}',
    'bit'           => 2,
    'name'          => 'Pale Moon',
    'shortName'     => null,
    'commonType'    => 'browser',
    'org'           => 'Moonchild Productions',
    'siteTitle'     => 'Pale Moon - Add-ons',
    'features'      => ['extensions', 'extensions-cat', 'themes', 'personas', 'language-packs',
                        'search-plugins', 'user-scripts', 'user-styles']
  ),
  'borealis' => array(
    'id'            => '{a3210b97-8e8a-4737-9aa0-aa0e607640b9}',
    'bit'           => 4,
    'name'          => 'Borealis Navigator',
    'shortName'     => 'Borealis',
    'commonType'    => 'navigator',
    'org'           => 'Binary Outcast',
    'siteTitle'     => 'Add-ons - Binary Outcast',
    'features'      => ['unified', 'extensions', 'themes', 'search-plugins', 'user-scripts', 'user-styles']
  ),
  'interlink' => array(
    'id'            => '{3550f703-e582-4d05-9a08-453d09bdfdc6}',
    'bit'           => 8,
    'name'          => 'Interlink Mail &amp; News',
    'shortName'     => 'Interlink',
    'commonType'    => 'client',
    'org'           => 'Binary Outcast',
    'siteTitle'     => 'Add-ons - Binary Outcast',
    'features'      => ['unified', 'extensions', 'themes', 'search-plugins', 'disable-xpinstall']
  ),
);

// --------------------------------------------------------------------------------------------------------------------

const MANIFEST_FILES = array(
  'xpinstall'         => 'install.js',
  'installRDF'        => 'install.rdf',
  'installJSON'       => 'install.json',
  'chrome'            => 'chrome.manifest',
  'bootstrap'         => 'bootstrap.js',
  'npmJetpack'        => 'package.json',
  'cfxJetpack'        => 'harness-options.json',
  'webex'             => 'manifest.json',
);

const XPINSTALL_TYPES = array(
  'app'               => 1,     // No longer applicable
  'extension'         => 2,
  'theme'             => 4,
  'locale'            => 8,
  'plugin'            => 16,    // No longer applicable
  'multipackage'      => 32,    // Forbidden on Phobos
  'dictionary'        => 64,
  'experiment'        => 128,   // No longer applicable
  'apiextension'      => 256,   // No longer applicable
  'external'          => 512,   // Phobos only
  'persona'           => 1024,  // Phobos only
  'search-plugin'     => 2048,  // Phobos only
  'user-script'       => 4096,  // Phobos only
  'user-style'        => 8192,  // Phobos only
);

// These are the supported "real" XPInstall types
const VALID_XPI_TYPES       = 2 | 4 | 8 | 64;

// These are types that only have a meaning in Phobos (save External (512))
const PHOBOS_XPI_TYPES     = 1024 | 2048 | 4096 | 8192;

// These are deprecated or unsupported "real" XPInstall types
// NOTE: External (512) is a completely virtual Phobos type so never allow it in an install manifest
const INVALID_XPI_TYPES     = 1 | 16 | 32 | 128 | 256 | 512;

// For some reason, when Mozilla killed the full XPInstall system and replaced Smart Update with the Add-ons Update Checker
// they used "item" for locales and dictionaries as the type in update.rdf
const AUS_XPI_TYPES         = [2 => 'extension', 4 => 'theme', 8 => 'item', 64 => 'item'];

// Add-ons Manager Search completely ignored the established bitwise types so we need to have a way to remap them to what
// the Add-ons Manager search results xml expects
const SEARCH_XPI_TYPES      = [2 => 1 /* extension */, 4 => 2 /* theme */,  8 => 6 /* locale */, 64 => 3 /* dictionary */];

// --------------------------------------------------------------------------------------------------------------------

// Define the specific technology that Extensions can have
const EXTENSION_TECHNOLOGY = array(
  'overlay'           => 1,
  'xpcom'             => 2,
  'bootstrap'         => 4,
  'jetpack'           => 8,
);

// These ID fragments are NOT allowed anywhere in an Add-on ID unless you are a member of the Add-ons Team or higher
const RESTRICTED_IDS  = array(
  'bfc5-fc555c87dbc4',  // Moonchild Productions
  '9376-3763d1ad1978',  // Pseudo-Static
  'b98e-98e62085837f',  // Ryan
  '9aa0-aa0e607640b9',  // Binary Outcast
  'moonchild',          // Moonchild Productions
  'palemoon',           // Moonchild Productions
  'basilisk',           // Moonchild Productions
  'binaryoutcast',      // Binary Outcast
  'mattatobin',         // Binary Outcast
  'thereisonlyxul',
  'mozilla.org',
  'lootyhoof',          // Ryan
  'srazzano'            // BANNED FOR LIFE
);

// --------------------------------------------------------------------------------------------------------------------

// These category defines allow mapping the slugs with normal text names as well as identifying the type
// Non-extension categories and the root extension category have an individual bit of 1 because they are
// interpreted as Add-ons Site sections rather than categories and largely programmatically assigned
const UNLISTED_CATEGORY        = ['name' => 'Unlisted',                     'type' => 0,    'bit' => 0];
const EXTENSION_CATEGORY       = ['name' => 'Extensions',                   'type' => 2,    'bit' => 1];
const CATEGORIES = array(
  'alerts-and-updates'        => ['name' => 'Alerts &amp; Updates',         'type' => 2,    'bit' => 2],
  'appearance'                => ['name' => 'Appearance',                   'type' => 2,    'bit' => 4],
  'bookmarks-and-tabs'        => ['name' => 'Bookmarks &amp; Tabs',         'type' => 2,    'bit' => 8],
  'download-management'       => ['name' => 'Download Management',          'type' => 2,    'bit' => 16],
  'feeds-news-and-blogging'   => ['name' => 'Feeds, News, &amp; Blogging',  'type' => 2,    'bit' => 32],
  'privacy-and-security'      => ['name' => 'Privacy &amp; Security',       'type' => 2,    'bit' => 64],
  'search-tools'              => ['name' => 'Search Tools',                 'type' => 2,    'bit' => 128],
  'social-and-communication'  => ['name' => 'Social &amp; Communication',   'type' => 2,    'bit' => 256],
  'tools-and-utilities'       => ['name' => 'Tools &amp; Utilities',        'type' => 2,    'bit' => 512],
  'web-development'           => ['name' => 'Web Development',              'type' => 2,    'bit' => 1024],
  'other'                     => ['name' => 'Other',                        'type' => 2,    'bit' => 2048],
  'themes'                    => ['name' => 'Themes',                       'type' => 4,    'bit' => 1],
  'language-packs'            => ['name' => 'Language Packs',               'type' => 8,    'bit' => 1],
  'dictionaries'              => ['name' => 'Dictionaries',                 'type' => 64,   'bit' => 1],
  'personas'                  => ['name' => 'Personas',                     'type' => 1024, 'bit' => 1],
  'search-plugins'            => ['name' => 'Search Plugins',               'type' => 2048, 'bit' => 1],
  'user-scripts'              => ['name' => 'User Scripts',                 'type' => 4096, 'bit' => 1],
  'user-styles'               => ['name' => 'User Styles',                  'type' => 8192, 'bit' => 1],
);

// --------------------------------------------------------------------------------------------------------------------

// Open Source Licenses users can set for their Add-ons
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

// == | Global Functions | ============================================================================================

/**********************************************************************************************************************
* Basic Content Generation using the Special Component's Template
*
* @dep SOFTWARE_NAME
* @dep SOFTWARE_VERSION
* @dep gfError()
* @param $aTtitle     Title of the page
* @param $aContent    Content of the page
* @param $aTextBox    Use textbox for content
* @param $aList       Use list for content
* @param $aError      Is an Error Page
***********************************************************************************************************************/
function gfGenContent($aMetadata, $aLegacyContent = null, $aTextBox = null, $aList = null, $aError = null) {
  $ePrefix = __FUNCTION__ . DASH_SEPARATOR;
  $skinPath = '/skin/default';

  // Anonymous functions
  $contentIsStringish = function($aContent) {
    return (!is_string($aContent) && !is_int($aContent)); 
  };

  $textboxContent = function($aContent) {
    return '<textarea class="special-textbox aligncenter" name="content" rows="36" readonly>' .
           $aContent . '</textarea>';
  };

  $template = gfReadFile(DOT . $skinPath . SLASH . 'template.xhtml');

  if (!$template) {
    gfError($ePrefix . 'Special Template is busted...', null, true);
  }

  $pageSubsts = array(
    '{$SKIN_PATH}'        => $skinPath,
    '{$SITE_NAME}'        => defined('SITE_NAME') ? SITE_NAME : SOFTWARE_NAME . SPACE . SOFTWARE_VERSION,
    '{$SITE_MENU}'        => EMPTY_STRING,
    '{$PAGE_TITLE}'       => null,
    '{$PAGE_CONTENT}'     => null,
    '{$SOFTWARE_NAME}'    => SOFTWARE_NAME,
    '{$SOFTWARE_VERSION}' => SOFTWARE_VERSION,
  );

  if ($aLegacyContent) {
    if (is_array($aMetadata)) {
      gfError($ePrefix . 'aMetadata may not be an array in legacy mode.');
    }

    if ($aTextBox && $aList) {
      gfError($ePrefix . 'You cannot use both textbox and list');
    }

    if ($contentIsStringish($aLegacyContent)) {
      $aLegacyContent = var_export($aLegacyContent, true);
      $aTextBox = true;
      $aList = false;
    }

    if ($aTextBox) {
      $aLegacyContent = $textboxContent($aLegacyContent);
    }
    elseif ($aList) {
      // We are using an unordered list so put aLegacyContent in there
      $aLegacyContent = '<ul><li>' . $aLegacyContent . '</li><ul>';
    }

    if (!$aError && ($GLOBALS['gaRuntime']['qTestCase'] ?? null)) {
      $pageSubsts['{$PAGE_TITLE}'] = 'Test Case' . DASH_SEPARATOR . $GLOBALS['gaRuntime']['qTestCase'];

      foreach ($GLOBALS['gaRuntime']['siteMenu'] ?? EMPTY_ARRAY as $_key => $_value) {
        $pageSubsts['{$SITE_MENU}'] .= '<li><a href="' . $_key . '">' . $_value . '</a></li>';
      }
    }
    else {
      $pageSubsts['{$PAGE_TITLE}'] = $aMetadata;
    }

    $pageSubsts['{$PAGE_CONTENT}'] = $aLegacyContent;
  }
  else {
    if ($aTextBox || $aList) {
      gfError($ePrefix . 'Mode attributes are deprecated.');
    }

    if (!array_key_exists('title', $aMetadata) && !array_key_exists('content', $aMetadata)) {
      gfError($ePrefix . 'You must specify a title and content');
    }

    $pageSubsts['{$PAGE_TITLE}'] = $aMetadata['title'];
    $pageSubsts['{$PAGE_CONTENT}'] = $contentIsStringish($aMetadata['content']) ?
                                     $textboxContent(var_export($aMetadata['content'], true)) :
                                     $aMetadata['content'];

    foreach ($aMetadata['menu'] ?? EMPTY_ARRAY as $_key => $_value) {
      $pageSubsts['{$SITE_MENU}'] .= '<li><a href="' . $_key . '">' . $_value . '</a></li>';
    }
  }

  if ($pageSubsts['{$SITE_MENU}'] == EMPTY_STRING) {
    $pageSubsts['{$SITE_MENU}'] = '<li><a href="/">Root</a></li>';
  }

  if (!str_starts_with($pageSubsts['{$PAGE_CONTENT}'], '<p') &&
      !str_starts_with($pageSubsts['{$PAGE_CONTENT}'], '<ul') &&
      !str_starts_with($pageSubsts['{$PAGE_CONTENT}'], '<h1') &&
      !str_starts_with($pageSubsts['{$PAGE_CONTENT}'], '<h2') &&
      !str_starts_with($pageSubsts['{$PAGE_CONTENT}'], '<table')) {
    $pageSubsts['{$PAGE_CONTENT}'] = '<p>' . $pageSubsts['{$PAGE_CONTENT}'] . '</p>';
  }

  $template = gfSubst('string', $pageSubsts, $template);

  // If we are generating an error from gfError we want to clean the output buffer
  if ($aError) {
    ob_get_clean();
  }

  // Send an html header
  header('Content-Type: text/html', false);

  // write out the everything
  print($template);

  // We're done here
  exit();
}

/**********************************************************************************************************************
* Get the bitwise value of valid applications from a list of application ids
*
* @param $aTargetApplications   list of targetApplication ids
* @param $isAssoc               set false to use a list if ids
* @returns                      bitwise int value representing applications
***********************************************************************************************************************/
function gfApplicationBits($aTargetApplications, $isAssoc = true) {
  if (!is_array($aTargetApplications)) {
    gfError(__FUNCTION__ . ': You must supply an array of ids');
  }

  if ($isAssoc) {
    $aTargetApplications = array_keys($aTargetApplications);
  }

  $applications = array_combine(array_column(TARGET_APPLICATION, 'id'), array_column(TARGET_APPLICATION, 'bit'));
  $applications = array_merge([TOOLKIT_ID => TOOLKIT_BIT, TOOLKIT_ALTID => TOOLKIT_BIT], $applications);

  $applicationBits = 0;

  foreach ($applications as $_key => $_value) {
    if (in_array($_key, $aTargetApplications)) {
      $applicationBits |= $_value;
    }
  }

  return $applicationBits;
}

/**********************************************************************************************************************
* 404 or Error
*
* @param $aErrorMessage   Error message if debug
***********************************************************************************************************************/
function gfErrorOr404($aErrorMessage) {
  global $gaRuntime;

  if ($gaRuntime['debugMode'] ?? null) {
    gfError($aErrorMessage);
  }

  gfHeader(404);
}

// ====================================================================================================================

// == | Main | ========================================================================================================

// Define an array that will hold the current application state
$gaRuntime = array(
  'currentApplication'  => null,
  'orginalApplication'  => null,
  'unifiedMode'         => null,
  'unifiedApps'         => null,
  'currentPath'         => null,
  'currentDomain'       => null,
  'currentSubDomain'    => null,
  'currentScheme'       => gfSuperVar('server', 'SCHEME') ?? (gfSuperVar('server', 'HTTPS') ? 'https' : 'http'),
  'debugMode'           => gfSuperVar('server', 'SERVER_NAME') == DEVELOPER_DOMAIN && !gfSuperVar('get', 'debugOverride'),
  'offlineMode'         => file_exists(ROOT_PATH . '/.offline') && !gfSuperVar('get', 'overrideOffline'),
  'phpServerName'       => gfSuperVar('server', 'SERVER_NAME'),
  'phpRequestURI'       => gfSuperVar('server', 'REQUEST_URI'),
  'remoteAddr'          => gfSuperVar('server', 'HTTP_X_FORWARDED_FOR') ?? gfSuperVar('server', 'REMOTE_ADDR'),
  'qComponent'          => gfSuperVar('get', 'component'),
  'qPath'               => gfSuperVar('get', 'path'),
  'qApplication'        => gfSuperVar('get', 'appOverride'),
);

// --------------------------------------------------------------------------------------------------------------------

// Root (/) won't set a component or path
if (!$gaRuntime['qComponent'] && !$gaRuntime['qPath']) {
  $gaRuntime['qComponent'] = 'site';
  $gaRuntime['qPath'] = SLASH;
}

// --------------------------------------------------------------------------------------------------------------------

// Set the current domain and subdomain
$gaRuntime['currentDomain'] = gfSuperVar('check', gfGetDomain($gaRuntime['phpServerName']));
$gaRuntime['currentSubDomain'] = gfSuperVar('check', gfGetDomain($gaRuntime['phpServerName'], true));

// --------------------------------------------------------------------------------------------------------------------

// Decide which application by domain that the software will be serving
$gaRuntime['currentApplication'] = APPLICATION_DOMAINS[$gaRuntime['currentDomain']] ?? null;

if (!$gaRuntime['currentApplication']) {
  if ($gaRuntime['debugMode']) {
    gfError('Invalid domain/application');
  }

  // We want to be able to give blank responses to any invalid domain/application
  // when not in debug mode
  $gaRuntime['offlineMode'] = true;
}

// See if this is a unified add-ons site
if (is_array($gaRuntime['currentApplication'])) {
  $gaRuntime['unifiedMode'] = true;
  $gaRuntime['unifiedApps'] = $gaRuntime['currentApplication'];
  $gaRuntime['currentApplication'] = true;
}

// --------------------------------------------------------------------------------------------------------------------

// Site Offline
if ($gaRuntime['offlineMode']) {
  $gvOfflineMessage = 'This site is currently unavailable. Please try again later.';

  // Development offline message
  if (str_contains(SOFTWARE_VERSION, 'a') || str_contains(SOFTWARE_VERSION, 'b') ||
      str_contains(SOFTWARE_VERSION, 'pre') || $gaRuntime['debugMode']) {
    $gvOfflineMessage = 'This in-development version of'. SPACE . SOFTWARE_NAME . SPACE . 'is not for public consumption.';
  }

  switch ($gaRuntime['qComponent']) {
    case 'aus':
      gfHeader('xml');
      print('<?xml version="1.0" encoding="UTF-8"?><RDF:RDF xmlns:RDF="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:em="http://www.mozilla.org/2004/em-rdf#" />');
      exit();
      break;
    case 'integration':
      $gaRuntime['requestAPIScope'] = gfSuperVar('get', 'type');
      $gaRuntime['requestAPIFunction'] = gfSuperVar('get', 'request');
      if ($gaRuntime['requestAPIScope'] != 'internal') {
        gfHeader(404);
      }
      switch ($gaRuntime['requestAPIFunction']) {
        case 'search':
          gfHeader('xml');
          print('<?xml version="1.0" encoding="utf-8" ?><searchresults total_results="0" />');
          exit();
          break;      
        case 'get':
        case 'recommended':
          gfHeader('xml');
          print('<?xml version="1.0" encoding="utf-8" ?><addons />');
          exit();
          break;
        default:
          gfHeader(404);
      }
      break;
    case 'discover':
      gfHeader(404);
    default:
      gfError($gvOfflineMessage);
  }
}

// --------------------------------------------------------------------------------------------------------------------

// Items that get changed depending on debug mode
if ($gaRuntime['debugMode']) {
  // In debug mode we need to test other applications
  if ($gaRuntime['qApplication']) {
    // We can't test an application that doesn't exist
    if (!array_key_exists($gaRuntime['qApplication'], TARGET_APPLICATION)) {
      gfError('Invalid override application');
    }

    // Stupidity check
    if ($gaRuntime['qApplication'] == $gaRuntime['currentApplication']) {
      gfError('It makes no sense to override to the same application');
    }

    // Set the application
    $gaRuntime['orginalApplication'] = $gaRuntime['currentApplication'];
    $gaRuntime['currentApplication'] = $gaRuntime['qApplication'];

    // If this is a unified add-ons site then we need to try and figure out the domain
    if (in_array('unified', TARGET_APPLICATION[$gaRuntime['currentApplication']]['features'])) {
      // Switch unified mode on
      $gaRuntime['unifiedMode'] = true;

      // Loop through the domains
      foreach (APPLICATION_DOMAINS as $_key => $_value) {
        // Skip any value that isn't an array
        if (!is_array($_value)) {
          continue;
        }

        // If we hit a domain with the requested application then set unifiedApps
        if (in_array($gaRuntime['currentApplication'], $_value)) {
          $gaRuntime['unifiedApps'] = $_value;
          $gaRuntime['currentApplication'] = true;
          break;
        }
      }

      // Final check to make sure we have a unified domain figured out
      if (!$gaRuntime['unifiedApps']) {
        gfError('Unable to switch to unified mode');
      }
    }
  }
}

// --------------------------------------------------------------------------------------------------------------------

// If we have a path then explode it and check for component pretty-paths
if ($gaRuntime['qPath']) {
  // Explode the path if it exists
  $gaRuntime['currentPath'] = gfExplodePath($gaRuntime['qPath']);

  // These paths override the site component
  switch ($gaRuntime['currentPath'][0]) {
    case 'special':
    case 'panel':
      $gaRuntime['qComponent'] = $gaRuntime['currentPath'][0];
      break;
  }
}

// --------------------------------------------------------------------------------------------------------------------

// Load component based on qComponent
if ($gaRuntime['qComponent'] && array_key_exists($gaRuntime['qComponent'], COMPONENTS)) {
  $gvComponentFile = COMPONENTS[$gaRuntime['qComponent']];

  if (!file_exists($gvComponentFile)) {
    gfErrorOr404('Cannot load component.');
  }

  require_once($gvComponentFile);
}
else {
  gfErrorOr404('Invalid component.');
}

// ====================================================================================================================

?>