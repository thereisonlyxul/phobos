<?php
// == | Setup | =======================================================================================================

// Enable Error Reporting
error_reporting(E_ALL);
ini_set("display_errors", "on");

// This is the absolute webroot path
// It does NOT have a trailing slash
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);

// Debug flag
define('DEBUG_MODE', $_GET['debug'] ?? null);

// Define basic constants for the software
const SOFTWARE_NAME       = 'Phobos';
const SOFTWARE_VERSION    = '0.9.0';
const BASE_RELPATH        = '/base/';
const SKIN_RELPATH        = '/skin/';

// Include fundamental constants and global functions
require_once(ROOT_PATH . BASE_RELPATH . 'binocUtils.php');

// Include application-specific global constants and functions
require_once(ROOT_PATH . BASE_RELPATH . 'appUtils.php');

// ====================================================================================================================

// == | Main | ========================================================================================================

// Define an array that will hold the current application state
$gaRuntime = array(
  'currentPath'         => null,
  'currentDomain'       => null,
  'currentSubDomain'    => null,
  'currentScheme'       => gfSuperVar('server', 'SCHEME') ?? (gfSuperVar('server', 'HTTPS') ? 'https' : 'http'),
  'currentSkin'         => 'default',
  'debugMode'           => DEBUG_MODE,
  'offlineMode'         => file_exists(ROOT_PATH . '/.offline') && !gfSuperVar('get', 'overrideOffline'),
  'remoteAddr'          => gfSuperVar('server', 'HTTP_X_FORWARDED_FOR') ?? gfSuperVar('server', 'REMOTE_ADDR'),
  'userAgent'           => gfSuperVar('server', 'HTTP_USER_AGENT'),
  'phpServerName'       => gfSuperVar('server', 'SERVER_NAME'),
  'phpRequestURI'       => gfSuperVar('server', 'REQUEST_URI'),
  'qComponent'          => gfSuperVar('get', 'component'),
  'qPath'               => gfSuperVar('get', 'path'),
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

// If we have a path we want to explode it into an array and count it
if ($gaRuntime['qPath']) {
  // Explode the path if it exists
  $gaRuntime['currentPath'] = gfExplodePath($gaRuntime['qPath']);

  // Get a count of the exploded path
  $gaRuntime['pathCount'] = count($gaRuntime['currentPath']);
}

// --------------------------------------------------------------------------------------------------------------------

// This is application-specific code that needs to run before a component is loaded but AFTER $gaRuntime
if (defined("APP_UTILS")) {
  // Merge our application-specific runtime state with the generic 
  $gaRuntime = array_merge($gaRuntime, array(
    'qApplication'        => gfSuperVar('get', 'appOverride'),
    'currentApplication'  => null,
    'orginalApplication'  => null,
    'unifiedMode'         => null,
    'unifiedApps'         => null,
    'validClient'         => null,
    'validVersion'        => null,
    'debugMode'           => (gfSuperVar('server', 'SERVER_NAME') == DEVELOPER_DOMAIN ?
                              !DEBUG_MODE : !gfSuperVar('get', 'debugOverride')),
  ));

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

  // ------------------------------------------------------------------------------------------------------------------

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
        gfOutput(XML_TAG . RDF_AUS_BLANK, 'xml');
        break;
      case 'integration':
        $gaRuntime['qAPIScope'] = gfSuperVar('get', 'type');
        $gaRuntime['qAPIFunction'] = gfSuperVar('get', 'request');
        if ($gaRuntime['qAPIScope'] != 'internal') {
          gfHeader(404);
        }
        switch ($gaRuntime['qAPIFunction']) {
          case 'search':
            gfOutput(XML_TAG . XML_API_SEARCH_BLANK, 'xml');
            break;      
          case 'get':
          case 'recommended':
            gfOutput(XML_TAG . XML_API_LIST_BLANK, 'xml');
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

  // ------------------------------------------------------------------------------------------------------------------

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

  // ------------------------------------------------------------------------------------------------------------------

  // We need nsIVersionComparator from this point on
  gfImportModules('static:vc');

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
      case 'panel':
        gfRedirect('https://addons-legacy.palemoon.org' . $gaRuntime['qPath']);
        break;
      default:
        gfErrorOr404('Invalid legacy request.');
    }
  }

  // ------------------------------------------------------------------------------------------------------------------

  // If we have a path then explode it and check for component pretty-paths
  if ($gaRuntime['currentPath']) {
    // These paths override the site component
    switch ($gaRuntime['currentPath'][0]) {
      case 'special':
      case 'panel':
        $gaRuntime['qComponent'] = $gaRuntime['currentPath'][0];
        break;
    }
  }
}

// --------------------------------------------------------------------------------------------------------------------

if (!defined("COMPONENTS")) {
  define("COMPONENTS", ['site' => ROOT_PATH . BASE_RELPATH . 'site.php',
                        'special' => ROOT_PATH . BASE_RELPATH . 'special.php']);
  if (($gaRuntime['currentPath'][0] ?? null) == 'special') {
    $gaRuntime['qComponent'] = 'special';
  }
}

// Load component based on qComponent
if ($gaRuntime['qComponent'] && array_key_exists($gaRuntime['qComponent'], COMPONENTS)) {
  $gvComponentFile = COMPONENTS[$gaRuntime['qComponent']];

  if (!file_exists($gvComponentFile)) {
    if ($gaRuntime['qComponent'] == 'site') {
      gfError('Could not load site component.');
    }
    else {
      gfErrorOr404('Cannot load the' . SPACE . $gaRuntime['qComponent'] . SPACE . 'component.');
    }
  }

  require_once($gvComponentFile);
}
else {
  gfErrorOr404('Invalid component.');
}


// ====================================================================================================================

?>