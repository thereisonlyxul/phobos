<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Setup | =======================================================================================================

// Include modules
// gfImportModules('database', 'addonManifest', 'content');

// Assign HTTP GET arguments to the software state
$gaRuntime['qAPIScope']       = gfSuperVar('get', 'type');
$gaRuntime['qAPIFunction']    = gfSuperVar('get', 'request');
$gaRuntime['qAPISearchQuery'] = gfSuperVar('get', 'q');
$gaRuntime['qAPISearchGUID']  = gfSuperVar('get', 'addonguid');
$gaRuntime['qAPIVersion']     = gfSuperVar('get', 'version');

// ====================================================================================================================

// == | Main | ========================================================================================================

// Sanity
if (!$gaRuntime['qAPIScope'] || !$gaRuntime['qAPIFunction']) {
  gfErrorOr404('Missing minimum arguments (type or request)');
}

// --------------------------------------------------------------------------------------------------------------------

if ($gaRuntime['qAPIScope'] == 'internal') {
  switch ($gaRuntime['qAPIFunction']) {
    case 'search':
      gfOutput(XML_TAG . XML_API_SEARCH_BLANK, 'xml');
      break;      
    case 'get':
    case 'recommended':
      gfOutput(XML_TAG . XML_API_LIST_BLANK, 'xml');
      break;
    default:
      gfErrorOr404('Unknown Internal Request');
  }
}
elseif ($gaRuntime['qAPIScope'] == 'external') {
  switch ($gaRuntime['qAPIFunction']) {
    case 'search':
      gfRedirect('/search/?terms=' . $gaRuntime['qAPISearchQuery']);
    case 'themes':
      gfRedirect('/themes/');
    case 'searchplugins':
      gfRedirect('/search-plugins/');
    case 'devtools':
      gfRedirect('/extensions/web-development/');
    case 'recommended':
    default:
      gfRedirect('/');
  }
}

// ====================================================================================================================

?>
