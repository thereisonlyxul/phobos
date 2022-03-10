<?php
// == | Setup | =======================================================================================================

// Enable Error Reporting
error_reporting(E_ALL);
ini_set("display_errors", "on");
ini_set('html_errors', true);

// This is the absolute webroot path
// It does NOT have a trailing slash
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);

// Debug flag
define('DEBUG_MODE', $_GET['debug'] ?? null);

// Define basic constants for the software
const SOFTWARE_NAME       = 'Phobos';
const SOFTWARE_VERSION    = '3.0.0a1';
const BASE_RELPATH        = '/base/';
const SKIN_RELPATH        = '/skin/';
const COMPONENTS_RELPATH  = '/components/';
const MODULES_RELPATH     = '/modules/';
const DATABASES_RELPATH   = '/db/';
const LIB_RELPATH         = '/libs/';

// Define components
const COMPONENTS = array(
  'special'         => ROOT_PATH . BASE_RELPATH       . 'special.php',
  'site'            => ROOT_PATH . BASE_RELPATH       . 'addonsSite.php',
  'download'        => ROOT_PATH . BASE_RELPATH       . 'addonsDownload.php',
  'aus'             => ROOT_PATH . COMPONENTS_RELPATH . 'manager/amUpdate.php',
  'discover'        => ROOT_PATH . COMPONENTS_RELPATH . 'manager/amDiscoverPane.php',
  'integration'     => ROOT_PATH . COMPONENTS_RELPATH . 'manager/amIntegration.php',
  'panel'           => ROOT_PATH . COMPONENTS_RELPATH . 'panel/addonsPanel.php',
);

// Define modules
const MODULES = array(
  'vc'              => ROOT_PATH . MODULES_RELPATH . 'nsIVersionComparator.php',
  'aviary'          => ROOT_PATH . MODULES_RELPATH . 'classAviary.php',
  'database'        => ROOT_PATH . MODULES_RELPATH . 'classDatabase.php',
  'account'         => ROOT_PATH . MODULES_RELPATH . 'classAccount.php',
  'addon'           => ROOT_PATH . MODULES_RELPATH . 'classAddon.php',
  'content'         => ROOT_PATH . MODULES_RELPATH . 'classContent.php',

);

// Define databases
const DATABASES = array(
  'emailBlacklist'  => ROOT_PATH . DATABASES_RELPATH . 'emailBlacklist.php',
);

// Define libraries
const LIBRARIES = array(
  'rdfParser'       => ROOT_PATH . LIB_RELPATH . 'rdf_parser.php',
  'safeMySQL'       => ROOT_PATH . LIB_RELPATH . 'safemysql.class.php',
  'smarty'          => ROOT_PATH . LIB_RELPATH . 'smarty/Smarty.class.php',
);


// Include fundamental constants and global functions
require_once(ROOT_PATH . BASE_RELPATH . 'binocUtils.php');

// Include application-specific global constants and functions
require_once(ROOT_PATH . BASE_RELPATH . 'appUtils.php');

// ====================================================================================================================

// == | Main | ========================================================================================================

// Define an array that will hold the current application state
$gaRuntime = array(
  'currentApplication'  => null,
  'orginalApplication'  => null,
  'unifiedMode'         => null,
  'unifiedApps'         => null,
  'validClient'         => null,
  'validVersion'        => null,
  'currentPath'         => null,
  'currentDomain'       => null,
  'currentSubDomain'    => null,
  'currentScheme'       => gfSuperVar('server', 'SCHEME') ?? (gfSuperVar('server', 'HTTPS') ? 'https' : 'http'),
  'currentSkin'         => 'default',
  'debugMode'           => gfSuperVar('server', 'SERVER_NAME') == DEVELOPER_DOMAIN && !gfSuperVar('get', 'debugOverride'),
  'offlineMode'         => file_exists(ROOT_PATH . '/.offline') && !gfSuperVar('get', 'overrideOffline'),
  'phpServerName'       => gfSuperVar('server', 'SERVER_NAME'),
  'phpRequestURI'       => gfSuperVar('server', 'REQUEST_URI'),
  'remoteAddr'          => gfSuperVar('server', 'HTTP_X_FORWARDED_FOR') ?? gfSuperVar('server', 'REMOTE_ADDR'),
  'userAgent'           => gfSuperVar('server', 'HTTP_USER_AGENT'),
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
      print(XML_TAG . RDF_AUS_BLANK);
      exit();
      break;
    case 'integration':
      $gaRuntime['qAPIScope'] = gfSuperVar('get', 'type');
      $gaRuntime['qAPIFunction'] = gfSuperVar('get', 'request');
      if ($gaRuntime['qAPIScope'] != 'internal') {
        gfHeader(404);
      }
      switch ($gaRuntime['qAPIFunction']) {
        case 'search':
          gfHeader('xml');
          print(XML_TAG . XML_API_SEARCH_BLANK);
          exit();
          break;      
        case 'get':
        case 'recommended':
          gfHeader('xml');
          print(XML_TAG . XML_API_LIST_BLANK);
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

// We need nsIVersionComparator from this point on
gfImportModules('static:vc');
gfError($gmVc);

// Set valid client
$gaRuntime['validClient'] = gfValidClientVersion();
$gaRuntime['validVersion'] = gfValidClientVersion(true);

// Determine if we should redirect Pale Moon clients back to addons-legacy
$gaRuntime['phoebusRedirect'] = ($gaRuntime['currentApplication'] == 'palemoon' &&
                                 $gaRuntime['validClient'] && !$gaRuntime['validVersion']);

if ($gaRuntime['phoebusRedirect']) {
  switch ($gaRuntime['qComponent']) {
    case 'aus':
    case 'integration':
    case 'discover':
      gfRedirect('https://addons-legacy.palemoon.org/?' . gfSuperVar('server', 'QUERY_STRING'));
      break;
    case 'site':
  //case 'special':
    case 'panel':
      gfRedirect('https://addons-legacy.palemoon.org' . $gaRuntime['qPath']);
      break;
    default:
      gfErrorOr404('Invalid component.');
  }
}

// --------------------------------------------------------------------------------------------------------------------

// If we have a path then explode it and check for component pretty-paths
if ($gaRuntime['qPath']) {
  // Explode the path if it exists
  $gaRuntime['currentPath'] = gfExplodePath($gaRuntime['qPath']);

  // Get a count of the exploded path
  $gaRuntime['pathCount'] = count($gaRuntime['currentPath']);

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