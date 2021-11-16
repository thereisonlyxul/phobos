<?php

// == | Functions | ===================================================================================================

function gfCheckPathCount($aExpectedCount) {
  global $gvPathCount;
  if ($gvPathCount > $aExpectedCount) {
    gfErrorOr404('Expected count was' . SPACE . $aExpectedCount . SPACE .
                 'but was' . SPACE . $gvPathCount);
  }
}

// --------------------------------------------------------------------------------------------------------------------

function gfCheckFeature($aFeature, $aReturn = null) {
  global $gaRuntime;

  if (is_bool($gaRuntime['currentApplication'])) {
    gfError(__FUNCTION__ . ': Unable to determine the application features.');
  }
  
  if (!in_array($aFeature, TARGET_APPLICATION[$gaRuntime['currentApplication']]['features'])) {
    if (!$aReturn) {
      gfErrorOr404('Feature' . SPACE . $aFeature . SPACE .
                   'is not enabled for' . SPACE . $gaRuntime['currentApplication']);
    }
    return false;
  }

  return true;
}

// --------------------------------------------------------------------------------------------------------------------

function gfLegacyRedirect($aSlug, $aSubPage = null) { 
  global $gaRuntime;

  if (is_bool($gaRuntime['currentApplication'])) {
    gfError(__FUNCTION__ . ': Unable to determine the application.');
  }

  $uri = SLASH . 'addon' . SLASH . $aSlug . SLASH;

  if ($aSubPage) {
    $uri .= $aSubPage . SLASH;
  }
 
  if ($gaRuntime['unifiedMode']) {
    $uri = SLASH . $gaRuntime['currentApplication'] . $uri;
  }
  
  gfRedirect($uri);
}

// ====================================================================================================================

// == | Main | ========================================================================================================

// Get a count of the exploded path
$gvPathCount = count($gaRuntime['currentPath']);

// Set the skin to the current application
$gaRuntime['currentSkin'] = $gaRuntime['currentApplication'];

// Check if we are operating in Unified Mode
if ($gaRuntime['unifiedMode']) {
  // Get a possible unified application from index zero of the exploded path
  // and remove it from the exploded path array
  $gvMaybeApplication = array_shift($gaRuntime['currentPath']);

  // Check if the possible unified application is valid
  if (in_array($gvMaybeApplication, $gaRuntime['unifiedApps'])) {
    // Set current application to the valid unified application
    $gaRuntime['currentApplication'] = $gvMaybeApplication;

    // Re-count the exploded path array because we removed the application from it
    $gvPathCount = count($gaRuntime['currentPath']);
  }

  // Set the skin to match the unified domain
    $gaRuntime['currentSkin'] = preg_replace('/(\.com|\.net|\.org)/i',
                                             EMPTY_STRING,
                                             array_search($gaRuntime['unifiedApps'], APPLICATION_DOMAINS));

  // We need to provide a page for the application root
  if ($gaRuntime['qPath'] != SLASH &&
      $gaRuntime['currentApplication'] === $gvMaybeApplication && $gvPathCount == 0) {
    gfGenContent($gaRuntime['currentApplication'] . SPACE . 'Add-ons Application Root', $gaRuntime); 
  }

  // Check to see if we have a valid unified application if not then use the current skin
  if (is_bool($gaRuntime['currentApplication'])) {
    $gaRuntime['currentApplication'] = $gaRuntime['currentSkin'];
  }
}

// In unified mode the exploded path array may be empty so it will fall through to the default case
// where it will either be the Add-ons Site Root or 404
$gvSection = $gaRuntime['currentPath'][0] ?? null;

switch ($gvSection) {
  case 'addon':
    // Longer than three uri parts is not sane
    gfCheckPathCount(3);

    // Get the Add-on Slug
    $gvAddonSlug = $gaRuntime['currentPath'][1] ?? null;

    // Get the Add-on Sub-Page
    $gvAddonSubPage = $gaRuntime['currentPath'][2] ?? null;

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
    $gvLegacySlug = $gaRuntime['currentPath'][2] ?? null;
    if ($gvLegacySlug) {
      gfLegacyRedirect($gvLegacySlug);
    }

    gfCheckFeature($gvSection);

    $gaRuntime['qAllExtensions'] = gfSuperVar('get', 'all');
    $gvCategory = $gaRuntime['currentPath'][1] ?? null;

    if (gfCheckFeature('extensions-cat', true) && !$gaRuntime['qAllExtensions']) {
      gfCheckPathCount(2);

      $gvCategories = array_filter(CATEGORIES,
                                   function($aCat) {
                                     return $aCat['type'] == XPINSTALL_TYPES['extension'];
                                   });

      if ($gvCategory) {
        if (!array_key_exists($gvCategory, $gvCategories)) {
          gfHeader(404);
        }

        gfGenContent($gvCategories[$gvCategory]['name'] . SPACE . 'Category', $gaRuntime);
      }

      gfGenContent('Extensions', $gvCategories);
    }
    else {
      gfCheckPathCount(1);
      gfGenContent('All Extensions', $gaRuntime);
    }

    gfHeader(404);
    break;
  case 'themes':
    // Send Phoebus <= 1.0 links to the correct place
    $gvLegacySlug = $gaRuntime['currentPath'][1] ?? null;
    if ($gvLegacySlug) {
      gfLegacyRedirect($gvLegacySlug);
    }
  case 'language-packs':
  case 'dictionaries':
  case 'personas':
  case 'search-plugins':
  case 'user-scripts':
  case 'user-styles':
    gfCheckFeature($gvSection);
    gfCheckPathCount(1);

    $gvCategoryName = CATEGORIES[$gvSection]['name'];
    $gvAddonType = CATEGORIES[$gvSection]['type'];

    gfGenContent(ucfirst($gaRuntime['currentApplication']) . SPACE . $gvCategoryName . SPACE . $gvAddonType, $gaRuntime);
    break;
  case 'license':
  case 'releases':
    // Send Phoebus 2.0 links to the correct place
    $gvLegacySlug = $gaRuntime['currentPath'][1] ?? null;
    if ($gvLegacySlug) {
      gfLegacyRedirect($gvLegacySlug, $gvSection);
    }
    gfHeader(404);
    break;
  case 'search':
    gfCheckPathCount(1);
    gfHeader(501);
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
