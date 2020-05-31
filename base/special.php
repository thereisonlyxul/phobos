<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Main | ========================================================================================================

$specialFunctions = array(
  'test' => 'Test Cases',
  'phpinfo' => 'PHP Info',
  'software-state' => 'Software State',
);

$function = $gaRuntime['splitPath'][1] ?? null;
$componentPath = dirname(COMPONENTS[$gaRuntime['requestComponent']]) . '/';

if (!$gaRuntime['debugMode']) {
  if ($specialFunction != 'phpinfo') {
    gfRedirect('/');
  }
}

// --------------------------------------------------------------------------------------------------------------------

if ($function && count($gaRuntime['splitPath']) == 2) {
  switch ($function) {
    case 'phpinfo':
      phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_ENVIRONMENT | INFO_VARIABLES);
      break;
    case 'software-state':
      gfGenContent($specialFunctions[$function], $gaRuntime);
      break;
    case 'restructure':
      gfHeader(404);
      break;
    case 'test':
      $gaRuntime['requestTestCase'] = gfSuperVar('get', 'case');
      $globTests = glob($componentPath . 'tests/*.php');
      $tests = [];

      foreach ($globTests as $_value) {
        $tests[] = str_replace('.php', '', str_replace($componentPath . 'tests/', '', $_value));
      }

      unset($globTests);

      if ($gaRuntime['requestTestCase']) {
        if (in_array($gaRuntime['requestTestCase'], $tests)) {
          require_once($componentPath . 'tests/' . $gaRuntime['requestTestCase'] . '.php');
        }
        else {
          gfError($gaRuntime['requestTestCase'] . ': Invalid Test Case');
        }
      }

      $output = '';

      foreach ($tests as $_value) {
        $output .= '<li><a href="/special/test/?case=' . $_value . '">' . $_value . '</a></li>';
      }

      $output = '<ul>' . $output . '</ul>';

      gfGenContent($specialFunctions[$function], $output);
      break;
    default:
      gfHeader(404);
  }
}
else {
  if ($gaRuntime['requestPath'] == '/special/') {
    $output = '';

    foreach ($specialFunctions as $_key => $_value) {
      $output .= '<li><a href="/special/' . $_key . '/">' . $_value . '</li>';
    }

    $output = '<ul>' . $output . '</ul>';

    gfGenContent('Special', $output);
  }
  else {
    gfHeader(404);
  }
}

exit();

// ====================================================================================================================

?>