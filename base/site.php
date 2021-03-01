<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Functions | ===================================================================================================

function gfIsFeature($aFeature) {
  global $gaRuntime;

  if (is_bool($gaRuntime['currentApplication'])) {
    gfError(__FUNCTION__ . ': Cannot complete function in unified add-ons site mode from here');
  }
  
  if (in_array($aFeature, TARGET_APPLICATION[$gaRuntime['currentApplication']]['features'])) {
    return true;
  }

  return false;
}

// --------------------------------------------------------------------------------------------------------------------

function gfAdjustedIndex($aIndex) {
  global $gvCount;
  global $gvAdjustedCount;
  
  if ($gvCount != $gvAdjustedCount) {
    return $aIndex + ($gvCount - $gvAdjustedCount);
  }

  return $aIndex;
}

// ====================================================================================================================

// == | Main | ========================================================================================================

// We need a count for the exploded path
$gvCount = count($gaRuntime['splitPath']);

// We need an adjusted count in case we are in unified mode
$gvAdjustedCount = $gvCount;

// Set the site skin
$gaRuntime['currentSkin'] = $gaRuntime['currentApplication'];

// --------------------------------------------------------------------------------------------------------------------

// Handle unified add-ons site mode
if ($gaRuntime['unified']) {
  // Determine the skin from the assigned domain name in unified mode
  $gaRuntime['currentSkin'] = preg_replace('/(addons\.|addons-dev\.|\.com|\.net|\.org)/i',
                                          '',
                                          array_search($gaRuntime['unifiedApps'], APPLICATION_DOMAINS));

  if (in_array($gaRuntime['splitPath'][0], $gaRuntime['unifiedApps'])) {
    if ($gvCount >= 1) {
      $gaRuntime['currentApplication'] = $gaRuntime['splitPath'][0];
      $gvAdjustedCount -= 1;
    }

    if ($gvCount == 1 && $gaRuntime['splitPath'][0] == $gaRuntime['currentApplication']) {
      gfGenContent(TARGET_APPLICATION[$gaRuntime['currentApplication']]['name'] . ' index', null);
    }
  }

  // Don't allow adjusted URIs when in unified mode from /
  $unifiedURIs = ['addon', 'extensions', 'themes', 'personas', 'search-plugins', 'language-packs', 'dictionaries'];
  if (in_array($gaRuntime['splitPath'][0], $unifiedURIs)) {
    gfHeader(404);
  }
}

// --------------------------------------------------------------------------------------------------------------------

// Make sure the skin path exists
if ((!$gaRuntime['currentSkin']) ||
    (!file_exists(dirname(COMPONENTS[$gaRuntime['qComponent']]) . '/skin/' . $gaRuntime['currentSkin']))) {
  gfError('Skin ' .
          (string)$gaRuntime['currentSkin'] .
          ' path does not exist for component ' .
          $gaRuntime['qComponent']);
}

// --------------------------------------------------------------------------------------------------------------------

switch ($gaRuntime['splitPath'][gfAdjustedIndex(0)]) {
  case 'addon':
    $slug = $gaRuntime['splitPath'][gfAdjustedIndex(1)] ?? null;

    // Add-on Versions and License
    if ($gvAdjustedCount == 3) {
      switch ($gaRuntime['splitPath'][gfAdjustedIndex(2)]) {
        case 'versions':
          gfGenContent('Add-on: ' . $slug . ' - Versions', null);
          break;
        case 'license':
          gfGenContent('Add-on: ' . $slug . ' - License', null);
          break;
        default:
          gfHeader(404);
      }
    }

    // Add-on Page
    if ($gvAdjustedCount == 2) {
      gfGenContent('Add-on: ' . $slug, null);
    }

    // There is no content for just /addon/ so redirect to root
    if ($gvAdjustedCount == 1) {
      $url = $gaRuntime['unified'] ? '/' . $gaRuntime['currentApplication'] . '/' : '/';
      gfRedirect($url);
    }
    break;
  case 'extensions':
    $categories = array_filter(CATEGORIES, function($aElement) { return $aElement['type'] == XPINSTALL_TYPES['extension']; });

    // Extension Sub-categories
    if ($gvAdjustedCount == 2) {
      $category = $gaRuntime['splitPath'][gfAdjustedIndex(1)];

      if (!gfIsFeature('extensions-cat')) {
        gfHeader(404);
      }

      if (!array_key_exists($category, $categories)) {
        gfHeader(404);
      }

      gfGenContent('Extension Category: ' . CATEGORIES[$category]['name'], null);
    }

    // Extension category
    if ($gvAdjustedCount == 1) {
      if (!gfIsFeature('extensions')) {
        gfheader(404);
      }

      if (gfIsFeature('extensions-cat') && !gfSuperVar('get', 'all')) {
        gfGenContent(EXTENSION_CATEGORY['name'] . ' Categories', $categories);
      }

      gfGenContent(EXTENSION_CATEGORY['name'] . ' List', null);
    }
    break;
  case 'themes':
  case 'language-packs':
  case 'dictionaries':
    $category = $gaRuntime['splitPath'][gfAdjustedIndex(0)];

    if ($gvAdjustedCount > 1 || !gfIsFeature($category)) {
      gfHeader(404);
    }

    gfGenContent(CATEGORIES[$category]['name'] . ' List', null);
    break;
  case 'search-plugins':
    if ($gvAdjustedCount > 1 || !gfIsFeature('search-plugins')) {
      gfHeader(404);
    }

    gfGenContent(CATEGORIES['search-plugins']['name'] . ' List', null);
  case 'personas':
    if ($gvAdjustedCount > 1 || !gfIsFeature('personas')) {
      gfHeader(404);
    }

    gfGenContent(CATEGORIES['personas']['name'] . ' List', null);
    break;
  case 'root':
    if ($gaRuntime['qPath'] == '/') {
      gfGenContent('Add-ons Site Root', $gaRuntime);
    }
  default:
    gfHeader(404);
}

// ====================================================================================================================

?>
