<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Setup | =======================================================================================================

error_reporting(E_ALL);
ini_set("display_errors", "on");

// This has to be defined using the function at runtime because it is based
// on a variable. However, constants defined with the language construct
// can use this constant by some strange voodoo. Keep an eye on this.
// NOTE: DOCUMENT_ROOT does NOT have a trailing slash.
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);

// --------------------------------------------------------------------------------------------------------------------

// Define some primitives
const NEW_LINE              = "\n";
const EMPTY_STRING          = "";
const EMPTY_ARRAY           = [];
const SPACE                 = " ";
const DOT                   = ".";
const SLASH                 = "/";
const DASH                  = "-";
const WILDCARD              = "*";

const PHP_EXTENSION         = DOT . 'php';
const JSON_EXTENSION        = DOT . 'json';
const TEMP_EXTENSION        = DOT . 'temp';
const XPINSTALL_EXTENSION   = DOT . 'xpi';

const JSON_ENCODE_FLAGS     = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
const FILE_WRITE_FLAGS      = "w+";

// --------------------------------------------------------------------------------------------------------------------

// Define the software
const SOFTWARE_VENDOR       = 'Binary Outcast';
const SOFTWARE_NAME         = 'Phoebus Ascendant';
const SOFTWARE_VERSION      = '3.0.0a1';
const SOFTWARE_REPO         = 'https://repo.palemoon.org/binaryoutcast/phoebus-ascendant.git';

// --------------------------------------------------------------------------------------------------------------------

// Define paths
const DATASTORE_RELPATH     = '/datastore/';
const OBJ_RELPATH           = '/.obj/';
const BASE_RELPATH          = '/base/';
const COMPONENTS_RELPATH    = '/components/';
const DATABASES_RELPATH     = '/databases/';
const MODULES_RELPATH       = '/modules/';
const LIB_RELPATH           = '/libraries/';

// --------------------------------------------------------------------------------------------------------------------

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
  'addon'           => ROOT_PATH . MODULES_RELPATH . 'classAddon.php',
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

// --------------------------------------------------------------------------------------------------------------------

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

const XML_TAG               = '<?xml version="1.0" encoding="utf-8" ?>';
const XML_API_SEARCH_BLANK  = '<searchresults total_results="0" />';
const XML_API_LIST_BLANK    = '<addons />';
const XML_API_ADDON_ERROR   = '<error>Add-on not found!</error>';
const RDF_AUS_BLANK         = '<RDF:RDF xmlns:RDF="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:em="http://www.mozilla.org/2004/em-rdf#" />';

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
  'app'               => 1,    // No longer applicable
  'extension'         => 2,
  'theme'             => 4,
  'locale'            => 8,
  'plugin'            => 16,   // No longer applicable
  'multipackage'      => 32,   // Forbidden on Phoebus
  'dictionary'        => 64,
  'experiment'        => 128,  // Not used in UXP
  'apiextension'      => 256,  // Not used in UXP
  'external'          => 512,  // Phoebus only
  'persona'           => 1024, // Phoebus only
  'search-plugin'     => 2048, // Phoebus only
);

const INVALID_XPI_TYPES   = 1 | 16 | 32 | 128 | 256 | 512 | 1024 | 2048;
const AUS_XPI_TYPES       = [2 => 'extension', 4 => 'theme', 8 => 'item', 64 => 'item'];
const SEARCH_XPI_TYPES    = array(
  2                       => 1, // extension
  4                       => 2, // theme
  8                       => 6, // locale
  64                      => 3, // dictionary
);

const REGEX_GUID          = '/^\{[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\}$/i';
const REGEX_HOST          = '/[a-z0-9-\._]+\@[a-z0-9-\._]+/i';

// --------------------------------------------------------------------------------------------------------------------

const EXTENSION_TECHNOLOGY = array(
  'overlay'           => 1,
  'xpcom'             => 2,
  'bootstrap'         => 4,
  'jetpack'           => 8,
);

const RESTRICTED_IDS  = array(
  'bfc5-fc555c87dbc4',  // Moonchild Productions
  '9376-3763d1ad1978',  // Pseudo-Static
  'b98e-98e62085837f',  // Ryan
  '9aa0-aa0e607640b9',  // Binary Outcast
  'moonchild',          // Moonchild Productions
  'palemoon',           // Moonchild Productions
  'basilisk',           // Moonchild Productions
  'thereisonlyxul',
  'binaryoutcast',      // Binary Outcast
  'mattatobin',         // Binary Outcast
  'mozilla.org',
  'lootyhoof',          // Ryan
  'srazzano'            // BANNED FOR LIFE
);

// --------------------------------------------------------------------------------------------------------------------

const EXTENSION_CATEGORY       = ['name' => 'Extensions',                   'type' => 2, 'bit' => 0];

const CATEGORIES = array(
  'alerts-and-updates'        => ['name' => 'Alerts &amp; Updates',         'type' => 2,    'bit' => 1],
  'appearance'                => ['name' => 'Appearance',                   'type' => 2,    'bit' => 2],
  'bookmarks-and-tabs'        => ['name' => 'Bookmarks &amp; Tabs',         'type' => 2,    'bit' => 4],
  'download-management'       => ['name' => 'Download Management',          'type' => 2,    'bit' => 8],
  'feeds-news-and-blogging'   => ['name' => 'Feeds, News, &amp; Blogging',  'type' => 2,    'bit' => 16],
  'privacy-and-security'      => ['name' => 'Privacy &amp; Security',       'type' => 2,    'bit' => 32],
  'search-tools'              => ['name' => 'Search Tools',                 'type' => 2,    'bit' => 64],
  'social-and-communication'  => ['name' => 'Social &amp; Communication',   'type' => 2,    'bit' => 128],
  'tools-and-utilities'       => ['name' => 'Tools &amp; Utilities',        'type' => 2,    'bit' => 256],
  'web-development'           => ['name' => 'Web Development',              'type' => 2,    'bit' => 512],
  'other'                     => ['name' => 'Other',                        'type' => 2,    'bit' => 1024],
  'themes'                    => ['name' => 'Themes',                       'type' => 4,    'bit' => 0],
  'language-packs'            => ['name' => 'Language Packs',               'type' => 8,    'bit' => 0],
  'dictionaries'              => ['name' => 'Dictionaries',                 'type' => 64,   'bit' => 0],
  'personas'                  => ['name' => 'Personas',                     'type' => 1024, 'bit' => 0],
  'search-plugins'            => ['name' => 'Search Plugins',               'type' => 2048, 'bit' => 0],
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

// == | Global Functions | ============================================================================================

/**********************************************************************************************************************
* Polyfill for str_starts_with
*
* @param $haystack  string
* @param $needle    substring
* @returns          true if substring exists at the start of the string else false
**********************************************************************************************************************/

if (!function_exists('str_starts_with')) {
  function str_starts_with($haystack, $needle) {
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
  }
}

/**********************************************************************************************************************
* Polyfill for str_ends_with
*
* @param $haystack  string
* @param $needle    substring
* @returns          true if substring exists at the end of the string else false
**********************************************************************************************************************/
if (!function_exists('str_ends_with')) {
  function str_ends_with($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
      return true;
    }

    return (substr($haystack, -$length) === $needle);
  }
}

/**********************************************************************************************************************
* Polyfill for str_contains
*
* @param $haystack  string
* @param $needle    substring
* @returns          true if substring exists in string else false
**********************************************************************************************************************/
if (!function_exists('str_contains')) {
  function str_contains($haystack, $needle) {
    if (strpos($haystack, $needle) > -1) {
      return true;
    }
    else {
      return false;
    }
  }
}

/**********************************************************************************************************************
* Sends HTTP Headers to client using a short name
*
* @param $_value    Short name of header
**********************************************************************************************************************/
function gfHeader($aHeader) {
  $headers = array(
    404             => 'HTTP/1.1 404 Not Found',
    501             => 'HTTP/1.1 501 Not Implemented',
    'html'          => 'Content-Type: text/html',
    'text'          => 'Content-Type: text/plain',
    'xml'           => 'Content-Type: text/xml',
    'json'          => 'Content-Type: application/json',
    'css'           => 'Content-Type: text/css',
  );
  
  if (!headers_sent() && array_key_exists($aHeader, $headers)) {
    if (in_array($aHeader, [404, 501])) {
      if ($GLOBALS['gaRuntime']['debugMode'] ?? null) {
        gfError($headers[$aHeader]);
      }
      else {
        header($headers[$aHeader]);
        exit();
      }
    }

    header($headers[$aHeader]);
  }
}

/**********************************************************************************************************************
* Sends HTTP Header to redirect the client to another URL
*
* @param $_strURL   URL to redirect to
**********************************************************************************************************************/
// This function sends a redirect header
function gfRedirect($aURL) {
	header('Location: ' . $aURL , true, 302);
  
  // We are done here
  exit();
}

/**********************************************************************************************************************
* Basic XML Output
***********************************************************************************************************************/
function gfOutputXML($aContent) {
  // Send XML Header
  gfHeader('xml');

  // Write out the XML
  print($aContent);

  // We're done here
  exit();
}

/**********************************************************************************************************************
* Basic Content Generation using the Special Component's Template
***********************************************************************************************************************/
function gfGenContent($aTitle, $aContent, $aTextBox = null, $aList = null, $aError = null) {
  $templateHead = @file_get_contents('./base/skin/special/template-header.xhtml');
  $templateFooter = @file_get_contents('./base/skin/special/template-footer.xhtml');

  // Make sure the template isn't busted, if it is send a text only error as an array
  if (!$templateHead || !$templateFooter) {
    gfError([__FUNCTION__ . ': Special Template is busted...', $aTitle, $aContent], -1);
  }

  // Can't use both the textbox and list arguments
  if ($aTextBox && $aList) {
    gfError(__FUNCTION__ . ': You cannot use both textbox and list');
  }

  // Anonymous function to determin if aContent is a string-ish or not
  $notString = function() use ($aContent) {
    return (!is_string($aContent) && !is_int($aContent)); 
  };

  // If not a string var_export it and enable the textbox
  if ($notString()) {
    $aContent = json_encode($aContent, JSON_ENCODE_FLAGS);
    $aTextBox = true;
    $aList = false;
  }

  // Use either a textbox or an unordered list
  if ($aTextBox) {
    // We are using the textbox so put aContent in there
    $aContent = '<textarea style="width: 1195px; resize: none;" name="content" rows="36" readonly>' .
                $aContent .
                '</textarea>';
  }
  elseif ($aList) {
    // We are using an unordered list so put aContent in there
    $aContent = '<ul><li>' . $aContent . '</li><ul>';
  }

  // Set page title
  $templateHead = str_replace('<title></title>',
                  '<title>' . $aTitle . DASH . SOFTWARE_NAME . SPACE . SOFTWARE_VERSION . '</title>',
                  $templateHead);

  if (str_contains(SOFTWARE_VERSION, 'a') || str_contains(SOFTWARE_VERSION, 'b') || str_contains(SOFTWARE_VERSION, 'pre')) {
    $templateHead = str_replace('<!-- Special -->', '<li><a href="/special/">Special</a></li>', $templateHead);
  }

  // If we are generating an error from gfError we want to clean the output buffer
  if ($aError) {
    ob_get_clean();
  }

  // Send an html header
  gfHeader('html');

  // write out the everything
  print($templateHead . '<h2>' . $aTitle . '</h2>' . $aContent . $templateFooter);

  // We're done here
  exit();
}

/**********************************************************************************************************************
* Error function that will display data (Error Message)
**********************************************************************************************************************/
function gfError($aValue, $aMode = 0) {
  $varExport  = var_export($aValue, true);
  $jsonEncode = json_encode($aValue, JSON_ENCODE_FLAGS);
  
  $pageHeader = array(
    'default' => 'Unable to Comply',
    'fatal'   => 'Fatal Error',
    'php'     => 'PHP Error',
    'output'  => 'Output'
  );

  switch($aMode) {
    case -1:
      // Text only
      header('Content-Type: text/plain', false);
      if (is_string($aValue) || is_int($aValue)) {
        print($aValue);
      }
      else {
        print($varExport);
      }
      break;
    case 1: gfGenContent($pageHeader['php'], $aValue, null, true, true);
            break;
    default: gfGenContent($pageHeader['default'], $aValue, null, true, true);
  }

  exit();
}

/**********************************************************************************************************************
* PHP Error Handler
**********************************************************************************************************************/

function gfErrorHandler($errno, $errstr, $errfile, $errline) {
  $errorCodes = array(
    E_ERROR             => 'Fatal Error',
    E_WARNING           => 'Warning',
    E_PARSE             => 'Parse',
    E_NOTICE            => 'Notice',
    E_CORE_ERROR        => 'Fatal Error (Core)',
    E_CORE_WARNING      => 'Warning (Core)',
    E_COMPILE_ERROR     => 'Fatal Error (Compile)',
    E_COMPILE_WARNING   => 'Warning (Compile)',
    E_USER_ERROR        => 'Fatal Error (User Generated)',
    E_USER_WARNING      => 'Warning (User Generated)',
    E_USER_NOTICE       => 'Notice (User Generated)',
    E_STRICT            => 'Strict',
    E_RECOVERABLE_ERROR => 'Fatal Error (Recoverable)',
    E_DEPRECATED        => 'Depercated',
    E_USER_DEPRECATED   => 'Depercated (User Generated)',
    E_ALL               => 'All',
  );

  $errorType = $errorCodes[$errno] ?? $errno;
  $errorMessage = $errorType . ': ' . $errstr . ' in ' .
                  str_replace(ROOT_PATH, '', $errfile) . ' on line ' . $errline;

  if (!(error_reporting() & $errno)) {
    // Don't do jack shit because the developers of PHP think users shouldn't be trusted.
    return;
  }

  gfError($errorMessage, 1);
}

// Set error handler fairly early...
set_error_handler("gfErrorHandler");

/**********************************************************************************************************************
* Unified Var Checking
*
* @param $_type           Type of var to check
* @param $_value          GET/PUT/SERVER/FILES/EXISTING Normal Var
* @param $_allowFalsy     Optional - Allow falsey returns (really only works with case var)
* @returns                Value or null
**********************************************************************************************************************/
function gfSuperVar($_type, $_value, $_allowFalsy = null) {
  $finalValue = null;

  switch ($_type) {
    case 'get':
      $finalValue = $_GET[$_value] ?? null;

      if ($finalValue) {
        $finalValue = preg_replace('/[^-a-zA-Z0-9_\-\/\{\}\@\.\%\s\,]/', '', $_GET[$_value]);
      }

      break;
    case 'post':
      $finalValue = $_POST[$_value] ?? null;
      break;
    case 'server':
      $finalValue = $_SERVER[$_value] ?? null;
      break;
    case 'files':
      $finalValue = $_FILES[$_value] ?? null;
      if ($finalValue) {
        if (!in_array($finalValue['error'], [UPLOAD_ERR_OK, UPLOAD_ERR_NO_FILE])) {
          gfError('Upload of ' . $_value . ' failed with error code: ' . $finalValue['error']);
        }

        if ($finalValue['error'] == UPLOAD_ERR_NO_FILE) {
          $finalValue = null;
        }
        else {
          $finalValue['type'] = mime_content_type($finalValue['tmp_name']);
        }
      }
      break;
    case 'cookie':
      $finalValue = $_COOKIE[$_value] ?? null;
      break;
    case 'var':
      $finalValue = $_value ?? null;
      break;
    default:
      gfError('Incorrect var check');
  }

  if (!$_allowFalsy && (empty($finalValue) || $finalValue === 'none' || $finalValue === '')) {
    return null;
  }

  return $finalValue;
}

/**********************************************************************************************************************
* Includes a module
*
* @param $aModules    List of modules
**********************************************************************************************************************/
function gfImportModules(...$aModules) {
  global $gaRuntime;
  foreach ($aModules as $_value) {
    if (!array_key_exists($_value, MODULES)) {
      gfError('Unable to import unknown module ' . $_value);
    }

    $className = 'class' . ucfirst($_value);
    $moduleName = 'module' . ucfirst($_value);

    // Special case for nsIVersionComparator
    if ($_value == 'vc') {
      $className = 'ToolkitVersionComparator';
      $moduleName = 'module' . strtoupper($_value);
    }
   
    if (array_key_exists($moduleName, $GLOBALS)) {
      gfError('Module ' . $_value . ' has already been imported');
    }

    require(MODULES[$_value]);
    $GLOBALS[$moduleName] = new $className();
  }
}

/**********************************************************************************************************************
* Check if a module is in $arrayIncludes
*
* @param $aClass      Class name
* @param $aIncludes   List of includes
**********************************************************************************************************************/
function gfEnsureModules($aClass, ...$aIncludes) { 
  global $gaRuntime;
  if (empty($aIncludes)) {
    gfError('You did not specify any modules');
  }
  
  $unloadedModules = [];
  $indicative = ' is ';
  foreach ($aIncludes as $_value) {
    $moduleName = 'module' . ucfirst($_value);

    if ($_value == 'vc') {
      $moduleName = 'module' . strtoupper($_value);
    }

    if (!array_key_exists($moduleName, $GLOBALS)) {
      $unloadedModules[] = $_value;
    }
  }

  if (count($unloadedModules) > 0) {
    if (count($unloadedModules) > 1) {
      $indicative = ' are ';
    }

    gfError(implode(', ', $unloadedModules) . $indicative . 'required for ' . $aClass);
  }
}

/**********************************************************************************************************************
* Splits a path into an indexed array of parts
*
* @param $aPath   URI Path
* @returns        array of uri parts in order
***********************************************************************************************************************/
function gfSplitPath($aPath) {
  if ($aPath == SLASH) {
    return ['root'];
  }

  return array_values(array_filter(explode(SLASH, $aPath), 'strlen'));
}

/**********************************************************************************************************************
* Read file (decode json if the file has that extension or parse install.rdf if that is the target file)
*
* @param $aFile     File to read
* @returns          file contents or array if json
                    null if error, empty string, or empty array
**********************************************************************************************************************/
function gfReadFile($aFile) {
  $file = @file_get_contents($aFile);

  if (str_ends_with($aFile, JSON_EXTENSION)) {
    $file = json_decode($file, true);
  }

  if (str_ends_with($aFile, MANIFEST_FILES['installRDF'])) {
    gfImportModules('mozillaRDF');
    global $moduleMozillaRDF;
    $file = $moduleMozillaRDF->parseInstallManifest($file);

    if (is_string($file)) {
      gfError('RDF Parsing Error: ' . $file);
    }
  }

  return gfSuperVar('var', $file);
}

/**********************************************************************************************************************
* Read file from zip-type archive
*
* @param $aArchive  Archive to read
* @param $aFile     File in archive
* @returns          file contents or array if json
                    null if error, empty string, or empty array
**********************************************************************************************************************/
function gfReadFileFromArchive($aArchive, $aFile) {
  return gfReadFile('zip://' . $aArchive . "#" . $aFile);
}

/**********************************************************************************************************************
* Write file (encodes json if the file has that extension)
*
* @param $aData     Data to be written
* @param $aFile     File to write
* @returns          true else return error string
**********************************************************************************************************************/
function gfWriteFile($aData, $aFile, $aRenameFile = null) {
  if (!gfSuperVar('var', $aData)) {
    return 'No useful data to write';
  }

  if (file_exists($aFile)) {
    return 'File already exists';
  }

  if (str_ends_with($aFile, JSON_EXTENSION)) {
    $aData = json_encode($aData, JSON_ENCODE_FLAGS);
  }

  $file = fopen($aFile, FILE_WRITE_FLAGS);
  fwrite($file, $aData);
  fclose($file);

  if ($aRenameFile) {
    rename($aFile, $aRenameFile);
  }

  return true;
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
  'currentDatabase'     => null,
  'currentSiteTitle'    => null,
  'currentScheme'       => gfSuperVar('server', 'SCHEME'),
  'currentDomain'       => null,
  'currentSkin'         => null,
  'debugMode'           => null,
  'offlineMode'         => file_exists(ROOT_PATH . SLASH . '.offline'),
  'phpServerName'       => gfSuperVar('server', 'SERVER_NAME'),
  'phpRequestURI'       => gfSuperVar('server', 'REQUEST_URI'),
  'remoteAddr'          => gfSuperVar('server', 'HTTP_X_FORWARDED_FOR') ?? gfSuperVar('server', 'REMOTE_ADDR'),
  'requestComponent'    => gfSuperVar('get', 'component'),
  'requestPath'         => gfSuperVar('get', 'path'),
  'requestApplication'  => gfSuperVar('get', 'appOverride'),
  'requestDebugOff'     => gfSuperVar('get', 'debugOff'),
  'requestSearchTerms'  => gfSuperVar('get', 'terms'),
  'module'              => [],
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
  if ($gaRuntime['debugMode']) {
    gfError('Invalid domain/application');
  }

  // We want to be able to give blank responses to any invalid domain/application
  // when not in debug mode
  $gaRuntime['offlineMode'] = true;
}

// --------------------------------------------------------------------------------------------------------------------

// If the entire site is offline we want to serve proper but empty responses
if ($gaRuntime['offlineMode']) {
  // Offline message to display where content is normally expected
  $offlineMessage = 'This Add-ons Site is currently unavailable. Please try again later.';

  // Development offline message
  if (str_contains(SOFTWARE_VERSION, 'a') || str_contains(SOFTWARE_VERSION, 'b') ||
      str_contains(SOFTWARE_VERSION, 'pre') || $gaRuntime['debugMode']) {
    $offlineMessage = 'This in-development version of Phoebus is not for public consumption.';
  }

  // This switch will handle requests for components
  switch ($gaRuntime['requestComponent']) {
    case 'aus':
      gfOutputXML(XML_TAG . RDF_AUS_BLANK);
      break;
    case 'integration':
      $gaRuntime['requestAPIScope'] = gfSuperVar('get', 'type');
      $gaRuntime['requestAPIFunction'] = gfSuperVar('get', 'request');
      if ($gaRuntime['requestAPIScope'] == 'internal') {
        switch ($gaRuntime['requestAPIFunction']) {
          case 'search':
            gfOutputXML(XML_TAG . XML_API_SEARCH_BLANK);
            break;      
          case 'get':
          case 'recommended':
            gfOutputXML(XML_TAG . XML_API_LIST_BLANK);
            break;
          default:
            gfHeader(404);
        }
      }
      else {
        gfHeader(404);
      }
      break;
    case 'discover':
      gfHeader(404);
      break;
    default:
      gfError($offlineMessage);
  }
}

// --------------------------------------------------------------------------------------------------------------------

// Items that get changed depending on debug mode
if ($gaRuntime['debugMode']) {
  // We can disable debug mode when on the dev url otherwise if debug mode we want all errors
  // When important we can distingish between false and null
  if ($gaRuntime['requestDebugOff']) {
    $gaRuntime['debugMode'] = false;
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
  $gaRuntime['requestPath'] = SLASH;
}
// The PANEL component overrides the SITE component
elseif (str_starts_with($gaRuntime['phpRequestURI'], SLASH . 'panel' . SLASH)) {
  $gaRuntime['requestComponent'] = 'panel';
}
// The SPECIAL component overrides the SITE component
elseif (str_starts_with($gaRuntime['phpRequestURI'], SLASH . 'special' . SLASH)) {
  $gaRuntime['requestComponent'] = 'special';
}

// --------------------------------------------------------------------------------------------------------------------

// Load component based on requestComponent
if ($gaRuntime['requestComponent'] && array_key_exists($gaRuntime['requestComponent'], COMPONENTS)) {
  // Explode the path
  $gaRuntime['splitPath'] = gfSplitPath($gaRuntime['requestPath']);

  // Include the component
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