<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Setup | =======================================================================================================

error_reporting(E_ALL);
ini_set("display_errors", "on");

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