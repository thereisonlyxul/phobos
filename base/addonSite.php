<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Functions | ===================================================================================================

function isFeature($aFeature, $aReturn = null) {
  global $gaRuntime;

  if (is_bool($gaRuntime['currentApplication'])) {
    gfError(__FUNCTION__ . ': Cannot complete function in unified add-ons site mode from here');
  }
  
  if (in_array($aFeature, TARGET_APPLICATION[$gaRuntime['currentApplication']]['features'])) {
    return true;
  }

  if ($aReturn) {
    return false;
  }
}

// --------------------------------------------------------------------------------------------------------------------

function adjustedIndex($aIndex) {
  global $count;
  global $adjustedCount;
  
  if ($count != $adjustedCount) {
    return $aIndex + ($count - $adjustedCount);
  }

  return $aIndex;
}

// ====================================================================================================================

// == | Main | ========================================================================================================

// We need a count for the exploded path
$count = count($gaRuntime['explodedPath']);

// We need an adjusted count in case we are in unified mode
$adjustedCount = $count;

// --------------------------------------------------------------------------------------------------------------------

// Handle unified add-ons site mode
if ($gaRuntime['unified']) {
  if (in_array($gaRuntime['explodedPath'][0], $gaRuntime['unifiedApps'])) {
    if ($count >= 1) {
      $gaRuntime['currentApplication'] = $gaRuntime['explodedPath'][0];
      $adjustedCount -= 1;
    }

    if ($count == 1 && $gaRuntime['explodedPath'][0] == $gaRuntime['currentApplication']) {
      gfGenContent(TARGET_APPLICATION[$gaRuntime['currentApplication']]['name'] . ' index', null);
    }
  }

  // Don't allow adjusted URIs when in unified mode from /
  $unifiedURIs = ['addon', 'extensions', 'themes', 'personas', 'search-plugins', 'language-packs', 'dictionaries'];
  if (in_array($gaRuntime['explodedPath'][0], $unifiedURIs)) {
    gfHeader(404);
  }
}

// --------------------------------------------------------------------------------------------------------------------

// This is dev shit -- remove
$appText = TARGET_APPLICATION[$gaRuntime['currentApplication']]['shortName'] ??
           TARGET_APPLICATION[$gaRuntime['currentApplication']]['name'] ??
           'Application Irreleavnt';

switch ($gaRuntime['explodedPath'][adjustedIndex(0)]) {
  case 'addon':
    $slug = $gaRuntime['explodedPath'][adjustedIndex(1)] ?? null;

    // Add-on Versions and License
    if ($adjustedCount == 3) {
      switch ($gaRuntime['explodedPath'][adjustedIndex(2)]) {
        case 'versions':
          gfGenContent($appText . ' Add-on: ' . $slug . ' - Versions', null);
          break;
        case 'license':
          gfGenContent($appText . ' Add-on: ' . $slug . ' - License', null);
          break;
        default:
          gfHeader(404);
      }
    }

    // Add-on Page
    if ($adjustedCount == 2) {
      gfGenContent($appText . ' Add-on: ' . $slug, null);
    }

    // There is no content for just /addon/ so redirect to root
    if ($adjustedCount == 1) {
      $url = $gaRuntime['unified'] ? '/' . $gaRuntime['currentApplication'] . '/' : '/';

      // This is dev shit -- remove
      gfError('This will redirect back to ' . $url);
    }
    break;
  case 'extensions':
    if ($adjustedCount == 2) {
      if (!isFeature('extensions-cat')) {
        gfHeader(404);
      }

      gfGenContent($appText . ' Extension Category: ' . $gaRuntime['explodedPath'][adjustedIndex(1)], null);
    }

    if ($adjustedCount == 1) {
      if (!isFeature('extensions')) {
        gfheader(404);
      }

      if (isFeature('extensions-cat') && !gfSuperVar('get', 'all')) {
        gfGenContent($appText . ' Extensions Categories', null);
      }

      gfGenContent($appText . ' Extensions List', null);
    }
    break;
  case 'themes':
  case 'language-packs':
  case 'dictionaries':
  case 'search-plugins':
  case 'personas':
    if ($adjustedCount > 1) {
      gfHeader(404);
    }

    if (!isFeature($gaRuntime['explodedPath'][adjustedIndex(0)])) {
      gfHeader(404);
    }

    gfGenContent($appText . ' ' . OTHER_CATEGORY[$gaRuntime['explodedPath'][adjustedIndex(0)]] . ' List', null);
    break;
  case 'root':
    if ($gaRuntime['requestPath'] == '/') {
      gfGenContent('Add-ons Site Root', $gaRuntime);
    }
  default:
    gfHeader(404);
}

// ====================================================================================================================

?>
