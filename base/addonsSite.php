<?php

// == | Setup | =======================================================================================================

// ====================================================================================================================

// == | Functions | ===================================================================================================

// ====================================================================================================================

// == | Main | ========================================================================================================

// Set the skin to the current application
$gaRuntime['currentSkin'] = $gaRuntime['currentApplication'];

// --------------------------------------------------------------------------------------------------------------------

// We use this to tell if a page in unified mode BUT without a known application
$gvUnifiedPrePage = false;

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
    $gaRuntime['pathCount'] = count($gaRuntime['currentPath']);

    // Re-run the client validity checks unless disable-xpinstall
    if (!gfCheckFeature('disable-xpinstall', true)) {
      $gaRuntime['validClient'] = gfValidClientVersion();
      $gaRuntime['validVersion'] = gfValidClientVersion(true);
    }
  }

  // Set the skin to match the unified domain
  $gaRuntime['currentSkin'] = preg_replace('/(\.com|\.net|\.org)/i',
                                           EMPTY_STRING,
                                           array_search($gaRuntime['unifiedApps'], APPLICATION_DOMAINS));

  if (is_bool($gaRuntime['currentApplication'])) {
    $gaRuntime['currentApplication'] = $gaRuntime['currentSkin'];
    $gvUnifiedPrePage = true;
  }
}

// --------------------------------------------------------------------------------------------------------------------

// Add enabled features to the site menu
$gaRuntime['siteMenu'] = [SLASH => 'Home'];
$gvMenuItems = array_keys(SECTIONS);

foreach ($gvMenuItems as $_value) {
  if ($gaRuntime['unifiedMode'] && $gvUnifiedPrePage) {
    break;
  }

  if (gfCheckFeature($_value, true)) {
    $_link = $gaRuntime['unifiedMode'] ?
             SLASH . $gaRuntime['currentApplication'] . SLASH . $_value . SLASH :
             SLASH . $_value . SLASH;
    $gaRuntime['siteMenu'][$_link] = SECTIONS[$_value]['name'];
  }
}

// --------------------------------------------------------------------------------------------------------------------

// In unified mode the exploded path array may be empty so it will fall through to the default case
// where it will either be the Add-ons Site Root, a Unified Application Root, or 404
$gvSection = $gaRuntime['currentPath'][0] ?? null;

switch ($gvSection) {
  case 'addon':
    // Get the Add-on Slug
    $gvAddonSlug = $gaRuntime['currentPath'][1] ?? null;

    // Get the Add-on Sub-Page
    $gvAddonSubPage = $gaRuntime['currentPath'][2] ?? null;

    // Has Add-on Slug
    if ($gvAddonSlug) {
      gfCheckPathCount(3);

      // Has Add-on Sub-Page
      if ($gvAddonSubPage) {
        if (!in_array($gvAddonSubPage, ['releases', 'license'])) {
          gfErrorOr404('Invalid Add-on Sub-page');
        }

        // Generate Add-on Sub-Page
        $gvPage = ['title' => $gvAddonSlug . SPACE . ucfirst($gvAddonSubPage),
                   'content' => $gaRuntime,
                   'menu' => $gaRuntime['siteMenu']];
        gfContent($gvPage);
      }

      // Generate Add-on Page
      $gvPage = ['title' => $gvAddonSlug . SPACE . 'Add-on Page',
                 'content' => $gaRuntime,
                 'menu' => $gaRuntime['siteMenu']];
      gfContent($gvPage);
    }

    gfRedirect(SLASH);
    break;
  case 'extensions':
    gfCheckFeature($gvSection);

    if (gfCheckFeature('e-cat', true) && !gfSuperVar('get', 'all')) {
      gfCheckPathCount(2);

      $gvCategory = $gaRuntime['currentPath'][1] ?? null;
      $gvCategories = gfGetCategoriesByType(XPINSTALL_TYPES['extension']);

      if ($gvCategory) {
        if (!array_key_exists($gvCategory, $gvCategories)) {
          gfErrorOr404('Invalid Extension Category');
        }

        // Generate Extension Category Page
        $gvPage = ['title' => $gvCategories[$gvCategory]['name'] . SPACE . 'Category',
                   'content' => $gaRuntime,
                   'menu' => $gaRuntime['siteMenu']];
        gfContent($gvPage);
      }

      // Generate Extension Page with List of Categories
      $gvPage = ['title' => 'Extensions',
                 'content' => $gvCategories,
                 'menu' => $gaRuntime['siteMenu']];
      gfContent($gvPage);
    }

    gfCheckPathCount(1);

    // Generate Extension Page with All Extensions
    $gvPage = ['title' => 'All Extensions',
               'content' => $gaRuntime,
               'menu' => $gaRuntime['siteMenu']];
    gfContent($gvPage);
    break;
  case 'themes':
  case 'personas':
  case 'language-packs':
  case 'dictionaries':
  case 'search-plugins':
  case 'user-scripts':
  case 'user-styles':
    gfCheckFeature($gvSection);
    gfCheckPathCount(1);

    $gvCategoryName = SECTIONS[$gvSection]['name'];
    $gvAddonType = SECTIONS[$gvSection]['type'];

    // Generate Section Page for Add-on Type
    $gvPage = ['title' => ucfirst($gaRuntime['currentApplication']) . SPACE . $gvCategoryName . SPACE . $gvAddonType,
               'content' => $gaRuntime,
               'menu' => $gaRuntime['siteMenu']];
    gfContent($gvPage);
    break;
  case 'search':
    gfCheckPathCount(1);
    gfHeader(501);
    break;
  default:
    // Deal with the Root Index and the Application Index
    if ($gaRuntime['qPath'] == SLASH) {
      if ($gaRuntime['unifiedMode']) {
        $gaRuntime['siteMenu'] = [SLASH => 'Root'];
        foreach ($gaRuntime['unifiedApps'] as $_value) {
          $gaRuntime['siteMenu'][SLASH . $_value . SLASH] = TARGET_APPLICATION[$_value]['name'];
        }
      }
      $gvPage = ['title' => 'Add-ons Site Root', 'content' => $gaRuntime, 'menu' => $gaRuntime['siteMenu']];
      gfContent($gvPage);
    }
    elseif ($gaRuntime['unifiedMode'] && $gaRuntime['pathCount'] == 0 &&
            in_array($gaRuntime['currentApplication'], $gaRuntime['unifiedApps']) ?? EMPTY_ARRAY) {
      $gvPage = ['title' => $gaRuntime['currentApplication'] . SPACE . 'Add-ons Application Root',
                 'content' => $gaRuntime,
                 'menu' => $gaRuntime['siteMenu']];
      gfContent($gvPage);
    }
    else {
      gfHeader(404);
    }
}

// ====================================================================================================================

?>
