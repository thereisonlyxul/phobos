<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Setup | =======================================================================================================

error_reporting(E_ALL);
ini_set("display_errors", "on");
ini_set('html_errors', false);

// This has to be defined using the function at runtime because it is based
// on a variable. However, constants defined with the language construct
// can use this constant by some strange voodoo. Keep an eye on this.
// NOTE: DOCUMENT_ROOT does NOT have a trailing slash.
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);

require_once('./globalConstants.php');
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
  'qComponent'          => gfSuperVar('get', 'component'),
  'qPath'               => gfSuperVar('get', 'path'),
  'qApplication'        => gfSuperVar('get', 'appOverride'),
  'qDebugOff'           => gfSuperVar('get', 'debugOff'),
  'qSearchTerms'        => gfSuperVar('get', 'terms'),
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
  switch ($gaRuntime['qComponent']) {
    case 'aus':
      gfOutputXML(XML_TAG . RDF_AUS_BLANK);
      break;
    case 'integration':
      $gaRuntime['qAPIScope'] = gfSuperVar('get', 'type');
      $gaRuntime['qAPIFunction'] = gfSuperVar('get', 'quest');
      if ($gaRuntime['qAPIScope'] == 'internal') {
        switch ($gaRuntime['qAPIFunction']) {
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
  if ($gaRuntime['qDebugOff']) {
    $gaRuntime['debugMode'] = false;
  }

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
if (!$gaRuntime['qComponent'] && !$gaRuntime['qPath']) {
  $gaRuntime['qComponent'] = 'site';
  $gaRuntime['qPath'] = SLASH;
}
// The PANEL component overrides the SITE component
elseif (str_starts_with($gaRuntime['phpRequestURI'], gfBuildPath('panel'))) {
  $gaRuntime['qComponent'] = 'panel';
}
// The SPECIAL component overrides the SITE component
elseif (str_starts_with($gaRuntime['phpRequestURI'], gfBuildPath('special'))) {
  $gaRuntime['qComponent'] = 'special';
}

// --------------------------------------------------------------------------------------------------------------------

// Load component based on requestComponent
if ($gaRuntime['qComponent'] && array_key_exists($gaRuntime['qComponent'], COMPONENTS)) {
  // Explode the path
  $gaRuntime['explodedPath'] = gfExplodePath($gaRuntime['qPath']);

  // Include the component
  require_once(COMPONENTS[$gaRuntime['qComponent']]);
}
else {
  if (!$gaRuntime['debugMode']) {
    gfHeader(404);
  }
  gfError('Invalid component');
}

// ====================================================================================================================

?>