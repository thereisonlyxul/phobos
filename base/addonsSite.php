<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Functions | ===================================================================================================

function gfIsFeature($aFeature) {
  global $gaRuntime;

  if (is_bool($gaRuntime['currentApplication'])) {
    gfError(__FUNCTION__ . ': Unable to determine the application features.');
  }
  
  if (in_array($aFeature, TARGET_APPLICATION[$gaRuntime['currentApplication']]['features'])) {
    return true;
  }

  return false;
}

// --------------------------------------------------------------------------------------------------------------------

function gfLegacyAddonRedirect($aSlug, $aSubPage = null) { 
  global $gaRuntime;

  if (is_bool($gaRuntime['currentApplication'])) {
    gfError(__FUNCTION__ . ': Unable to determine the application.');
  }

  $uri = SLASH . 'addon' . SLASH . $aSlug . SLASH;

  if ($aSubPage) {
    $uri .= $aSubPage . SLASH;
  }
 
  if ($gaRuntime['unified']) {
    $uri = SLASH . $gaRuntime['currentApplication'] . $uri;
  }
  
  gfRedirect($uri);
}

// ====================================================================================================================

// == | Main | ========================================================================================================

// Get a count of the exploded path
$gaRuntime['explodedCount'] = count($gaRuntime['explodedPath']);

// Set the skin to the current application
$gaRuntime['currentSkin'] = $gaRuntime['currentApplication'];

// Check if we are operating in Unified Mode
if ($gaRuntime['unified']) {
  // Get a possible unified application from index zero of the exploded path
  // and remove it from the exploded path array
  $gvMaybeApplication = array_shift($gaRuntime['explodedPath']);

  // Check if the possible unified application is valid
  if (in_array($gvMaybeApplication, $gaRuntime['unifiedApps'])) {
    // Set current application to the valid unified application
    $gaRuntime['currentApplication'] = $gvMaybeApplication;

    // Re-count the exploded path array because we removed the application from it
    $gaRuntime['explodedCount'] = count($gaRuntime['explodedPath']);
  }

  // Set the skin to match the unified domain
    $gaRuntime['currentSkin'] = preg_replace('/(addons\.|addons-dev\.|\.com|\.net|\.org)/i',
                                             EMPTY_STRING,
                                             array_search($gaRuntime['unifiedApps'], APPLICATION_DOMAINS));

  // We need to provide a page for the application root
  if ($gaRuntime['qPath'] != SLASH &&
      $gaRuntime['currentApplication'] === $gvMaybeApplication &&
      $gaRuntime['explodedCount'] == 0) {
    gfGenContent($gaRuntime['currentApplication'] . SPACE . 'Add-ons Application Root', $gaRuntime); 
  }

  // Check to see if we have a valid unified application if not then use the current skin
  if (is_bool($gaRuntime['currentApplication'])) {
    $gaRuntime['currentApplication'] = $gaRuntime['currentSkin'];
  }
}

// In unified mode the exploded path array may be empty so it will fall through to the default case
// where it will either be the Add-ons Site Root or 404
$gvSection = $gaRuntime['explodedPath'][0] ?? null;

switch ($gvSection) {
  case 'addon':
    // Longer than three uri parts is not sane
    if ($gaRuntime['explodedCount'] > 3) {
      gfHeader(404);
    }

    // Get the Add-on Slug
    $gvAddonSlug = $gaRuntime['explodedPath'][1] ?? null;

    // Get the Add-on Sub-Page
    $gvAddonSubPage = $gaRuntime['explodedPath'][2] ?? null;

    if ($gvAddonSlug) {
      if ($gvAddonSubPage) {
        if (!in_array($gvAddonSubPage, ['releases', 'license'])) {
          gfHeader(404);
        }

        gfGenContent($gvAddonSlug . SPACE . ucfirst($gvAddonSubPage), $gaRuntime);
      }

      gfGenContent($gvAddonSlug . SPACE . 'Add-on Page', $gaRuntime);
    }

    gfHeader(404);
    break;
  case 'extensions':
    // Send Phoebus <= 1.0 links to the correct place
    $gvLegacySlug = $gaRuntime['explodedPath'][2] ?? null;
    if ($gvLegacySlug) {
      gfLegacyAddonRedirect($gvLegacySlug);
    }

    if (!gfIsFeature($gvSection)) {
      gfHeader(404);
    }

    $gaRuntime['qAllExtensions'] = gfSuperVar('get', 'all');
    $gvCategory = $gaRuntime['explodedPath'][1] ?? null;

    if (gfIsFeature('extensions-cat') && !$gaRuntime['qAllExtensions']) {
      // Longer than three uri parts is not sane
      if ($gaRuntime['explodedCount'] > 3) {
        gfHeader(404);
      }

      $gvCategories = array_filter(CATEGORIES,
                                   function($aCat) { return $aCat['type'] == XPINSTALL_TYPES['extension']; });

      if ($gvCategory) {
        if (!array_key_exists($gvCategory, $gvCategories)) {
          gfHeader(404);
        }

        gfGenContent($gvCategories[$gvCategory]['name'] . SPACE . 'Category', $gaRuntime);
      }

      gfGenContent('Extensions', $gvCategories);
    }
    else {
      // Longer than one uri part is not sane
      if ($gaRuntime['explodedCount'] > 1) {
        gfHeader(404);
      }

      gfGenContent('All Extensions', $gaRuntime);
    }

    gfHeader(404);
    break;
  case 'themes':
    // Send Phoebus <= 1.0 links to the correct place
    $gvLegacySlug = $gaRuntime['explodedPath'][1] ?? null;
    if ($gvLegacySlug) {
      gfLegacyAddonRedirect($gvLegacySlug);
    }
  case 'personas':
  case 'search-plugins':
  case 'language-packs':
  case 'dictionaries':
    // Not an enabled feature or longer than one uri part is not sane
    // Themes legacy urls are handled above but fall through to here
    if (!gfIsFeature($gvSection) || $gaRuntime['explodedCount'] > 1) {
      gfHeader(404);
    }

    gfGenContent(ucfirst($gaRuntime['currentApplication']) . SPACE . DASH . SPACE . ucfirst($gaRuntime['explodedPath'][0]), $gaRuntime);
    break;
  case 'search':
    gfHeader(501);
    break;
  case 'license':
  case 'releases':
    // Send Phoebus 2.0 links to the correct place
    $gvLegacySlug = $gaRuntime['explodedPath'][1] ?? null;
    if ($gvLegacySlug) {
      gfLegacyAddonRedirect($gvLegacySlug, $gvSection);
    }
    gfHeader(404);
    break;
  default:
    if ($gaRuntime['qPath'] == SLASH) {
      gfGenContent('Add-ons Site Root', $gaRuntime);
    }
    else {
      gfHeader(404);
    }
}

// ====================================================================================================================

?>
