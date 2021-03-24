<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Sanity | ======================================================================================================

if (!defined('SOFTWARE_NAME')) {
  die('You must include globalConstants');
}

// ====================================================================================================================

/**********************************************************************************************************************
* Sends HTTP Headers to client using a short name
*
* @param $_value    Short name of header
**********************************************************************************************************************/
function gfHeader($aHeader) {
  $headers = array(
    404             => 'HTTP/1.1 404 Not Found',
    501             => 'HTTP/1.1 501 Not Implemented',
    'html'          => 'Content-Type: text/html',
    'text'          => 'Content-Type: text/plain',
    'xml'           => 'Content-Type: text/xml',
    'json'          => 'Content-Type: application/json',
    'css'           => 'Content-Type: text/css',
  );
  
  if (!headers_sent() && array_key_exists($aHeader, $headers)) {
    if (in_array($aHeader, [404, 501])) {
      if ($GLOBALS['gaRuntime']['debugMode'] ?? null) {
        gfError($headers[$aHeader]);
      }
      else {
        header($headers[$aHeader]);
        exit();
      }
    }

    header($headers[$aHeader]);
  }
}

/**********************************************************************************************************************
* Sends HTTP Header to redirect the client to another URL
*
* @param $_strURL   URL to redirect to
**********************************************************************************************************************/
// This function sends a redirect header
function gfRedirect($aURL) {
	header('Location: ' . $aURL , true, 302);
  
  // We are done here
  exit();
}

/**********************************************************************************************************************
* Splits a path into an indexed array of parts
*
* @param $aPath   URI Path
* @returns        array of uri parts in order
***********************************************************************************************************************/
function gfExplodePath($aPath) {
  if ($aPath == SLASH) {
    return ['root'];
  }

  return array_values(array_filter(explode(SLASH, $aPath), 'strlen'));
}

/**********************************************************************************************************************
* Builds a path from a list of arguments
*
* @param        ...$aPathParts  Path Parts
* @returns                      Path string
***********************************************************************************************************************/
function gfBuildPath(...$aPathParts) {
  $path = implode(SLASH, $aPathParts);
  $filesystem = str_starts_with($path, ROOT_PATH);
  
  // Add a prepending slash if this is not a filesystem path
  if (!$filesystem) {
    $path = SLASH . $path;
  }

  // Add a trailing slash if the last part does not contain a dot
  // If it is a filesystem path then we will also add a trailing slash if the last part starts with a dot
  if (!str_contains(basename($path), DOT) || ($filesystem && str_starts_with(basename($path), DOT))) {
    $path .= SLASH;
  }

  return $path;
}

/**********************************************************************************************************************
* Basic XML Output
***********************************************************************************************************************/
function gfOutputXML($aContent) {
  // Send XML Header
  gfHeader('xml');

  // Write out the XML
  print($aContent);

  // We're done here
  exit();
}

/**********************************************************************************************************************
* Basic Content Generation using the Special Component's Template
***********************************************************************************************************************/
function gfGenContent($aTitle, $aContent, $aTextBox = null, $aList = null, $aError = null) {
  $templateHead = @file_get_contents('./base/skin/special/template-header.xhtml');
  $templateFooter = @file_get_contents('./base/skin/special/template-footer.xhtml');

  // Make sure the template isn't busted, if it is send a text only error as an array
  if (!$templateHead || !$templateFooter) {
    gfError([__FUNCTION__ . ': Special Template is busted...', $aTitle, $aContent], -1);
  }

  // Can't use both the textbox and list arguments
  if ($aTextBox && $aList) {
    gfError(__FUNCTION__ . ': You cannot use both textbox and list');
  }

  // Anonymous function to determin if aContent is a string-ish or not
  $notString = function() use ($aContent) {
    return (!is_string($aContent) && !is_int($aContent)); 
  };

  // If not a string var_export it and enable the textbox
  if ($notString()) {
    $aContent = json_encode($aContent, JSON_ENCODE_FLAGS);
    $aTextBox = true;
    $aList = false;
  }

  // Use either a textbox or an unordered list
  if ($aTextBox) {
    // We are using the textbox so put aContent in there
    $aContent = '<textarea style="width: 1195px; resize: none;" name="content" rows="36" readonly>' .
                $aContent .
                '</textarea>';
  }
  elseif ($aList) {
    // We are using an unordered list so put aContent in there
    $aContent = '<ul><li>' . $aContent . '</li><ul>';
  }

  // Set page title
  $templateHead = str_replace('<title></title>',
                  '<title>' . $aTitle . ' - ' . SOFTWARE_NAME . SPACE . SOFTWARE_VERSION . '</title>',
                  $templateHead);

  if (str_contains(SOFTWARE_VERSION, 'a') || str_contains(SOFTWARE_VERSION, 'b') || str_contains(SOFTWARE_VERSION, 'pre')) {
    $templateHead = str_replace('<!-- Special -->', '<li><a href="/special/">Special</a></li>', $templateHead);
  }

  // If we are generating an error from gfError we want to clean the output buffer
  if ($aError) {
    ob_get_clean();
  }

  // Send an html header
  gfHeader('html');

  // write out the everything
  print($templateHead . '<h2>' . $aTitle . '</h2>' . $aContent . $templateFooter);

  // We're done here
  exit();
}

/**********************************************************************************************************************
* Error function that will display data (Error Message)
**********************************************************************************************************************/
function gfError($aValue, $aMode = 0) {
  $varExport  = var_export($aValue, true);
  $jsonEncode = json_encode($aValue, JSON_ENCODE_FLAGS);
  
  $pageHeader = array(
    'default' => 'Unable to Comply',
    'fatal'   => 'Fatal Error',
    'php'     => 'PHP Error',
    'output'  => 'Output'
  );

  switch($aMode) {
    case -1:
      // Text only
      header('Content-Type: text/plain', false);
      if (is_string($aValue) || is_int($aValue)) {
        print($aValue);
      }
      else {
        print($varExport);
      }
      break;
    case 1: gfGenContent($pageHeader['php'], $aValue, null, true, true);
            break;
    default: gfGenContent($pageHeader['default'], $aValue, null, true, true);
  }

  exit();
}

/**********************************************************************************************************************
* PHP Error Handler
**********************************************************************************************************************/

function gfErrorHandler($errno, $errstr, $errfile, $errline) {
  $errorCodes = array(
    E_ERROR             => 'Fatal Error',
    E_WARNING           => 'Warning',
    E_PARSE             => 'Parse',
    E_NOTICE            => 'Notice',
    E_CORE_ERROR        => 'Fatal Error (Core)',
    E_CORE_WARNING      => 'Warning (Core)',
    E_COMPILE_ERROR     => 'Fatal Error (Compile)',
    E_COMPILE_WARNING   => 'Warning (Compile)',
    E_USER_ERROR        => 'Fatal Error (User Generated)',
    E_USER_WARNING      => 'Warning (User Generated)',
    E_USER_NOTICE       => 'Notice (User Generated)',
    E_STRICT            => 'Strict',
    E_RECOVERABLE_ERROR => 'Fatal Error (Recoverable)',
    E_DEPRECATED        => 'Depercated',
    E_USER_DEPRECATED   => 'Depercated (User Generated)',
    E_ALL               => 'All',
  );

  $errorType = $errorCodes[$errno] ?? $errno;
  $errorMessage = $errorType . ': ' . $errstr . ' in ' .
                  str_replace(ROOT_PATH, '', $errfile) . ' on line ' . $errline;

  if (!(error_reporting() & $errno)) {
    // Don't do jack shit because the developers of PHP think users shouldn't be trusted.
    return;
  }

  gfError($errorMessage, 1);
}

// Set error handler fairly early...
set_error_handler("gfErrorHandler");

/**********************************************************************************************************************
* Unified Var Checking
*
* @param $_type           Type of var to check
* @param $_value          GET/PUT/SERVER/FILES/EXISTING Normal Var
* @param $_allowFalsy     Optional - Allow falsey returns (really only works with case var)
* @returns                Value or null
**********************************************************************************************************************/
function gfSuperVar($_type, $_value, $_allowFalsy = null) {
  $finalValue = null;

  switch ($_type) {
    case 'get':
      $finalValue = $_GET[$_value] ?? null;

      if ($finalValue) {
        $finalValue = preg_replace('/[^-a-zA-Z0-9_\-\/\{\}\@\.\%\s\,]/', '', $_GET[$_value]);
      }

      break;
    case 'post':
      $finalValue = $_POST[$_value] ?? null;
      break;
    case 'server':
      $finalValue = $_SERVER[$_value] ?? null;
      break;
    case 'files':
      $finalValue = $_FILES[$_value] ?? null;
      if ($finalValue) {
        if (!in_array($finalValue['error'], [UPLOAD_ERR_OK, UPLOAD_ERR_NO_FILE])) {
          gfError('Upload of ' . $_value . ' failed with error code: ' . $finalValue['error']);
        }

        if ($finalValue['error'] == UPLOAD_ERR_NO_FILE) {
          $finalValue = null;
        }
        else {
          $finalValue['type'] = mime_content_type($finalValue['tmp_name']);
        }
      }
      break;
    case 'cookie':
      $finalValue = $_COOKIE[$_value] ?? null;
      break;
    case 'var':
      $finalValue = $_value ?? null;
      break;
    default:
      gfError('Incorrect var check');
  }

  if (!$_allowFalsy && (empty($finalValue) || $finalValue === 'none' || $finalValue === '')) {
    return null;
  }

  return $finalValue;
}

/**********************************************************************************************************************
* Includes a module
*
* @param $aModules    List of modules
**********************************************************************************************************************/
function gfImportModules(...$aModules) {
  foreach ($aModules as $_value) {
    if (!array_key_exists($_value, MODULES)) {
      gfError('Unable to import unknown module ' . $_value);
    }

    $className = 'class' . ucfirst($_value);
    $moduleName = 'gm' . ucfirst($_value);

    // Special case for nsIVersionComparator
    if ($_value == 'vc') {
      $className = 'ToolkitVersionComparator';
      $moduleName = 'gm' . strtoupper($_value);
    }
   
    if (array_key_exists($moduleName, $GLOBALS)) {
      gfError('Module ' . $_value . ' has already been imported');
    }

    require(MODULES[$_value]);
    $GLOBALS[$moduleName] = new $className();
  }
}

/**********************************************************************************************************************
* Check if a module is in $arrayIncludes
*
* @param $aClass      Class name
* @param $aIncludes   List of includes
**********************************************************************************************************************/
function gfEnsureModules($aClass, ...$aIncludes) { 
  if (!$aClass) {
    $aClass = "Global";
  }

  if (empty($aIncludes)) {
    gfError('You did not specify any modules');
  }
  
  $unloadedModules = EMPTY_ARRAY;
  $indicative = ' is ';
  foreach ($aIncludes as $_value) {
    $moduleName = 'gm' . ucfirst($_value);

    if ($_value == 'vc') {
      $moduleName = 'gm' . strtoupper($_value);
    }

    if (!array_key_exists($moduleName, $GLOBALS)) {
      $unloadedModules[] = $_value;
    }
  }

  if (count($unloadedModules) > 0) {
    if (count($unloadedModules) > 1) {
      $indicative = ' are ';
    }

    gfError(implode(', ', $unloadedModules) . $indicative . 'required for ' . $aClass);
  }
}

/**********************************************************************************************************************
* Read file (decode json if the file has that extension or parse install.rdf if that is the target file)
*
* @param $aFile     File to read
* @returns          file contents or array if json
                    null if error, empty string, or empty array
**********************************************************************************************************************/
function gfReadFile($aFile) {
  $file = @file_get_contents($aFile);

  if (str_ends_with($aFile, JSON_EXTENSION)) {
    $file = json_decode($file, true);
  }

  if (str_ends_with($aFile, MANIFEST_FILES['installRDF']) && array_key_exists('gmMozillaRDF', $GLOBALS)) {
    global $gmMozillaRDF;
    $file = $gmMozillaRDF->parseInstallManifest($file);

    if (is_string($file)) {
      gfError('RDF Parsing Error: ' . $file);
    }
  }

  return gfSuperVar('var', $file);
}

/**********************************************************************************************************************
* Read file from zip-type archive
*
* @param $aArchive  Archive to read
* @param $aFile     File in archive
* @returns          file contents or array if json
                    null if error, empty string, or empty array
**********************************************************************************************************************/
function gfReadFileFromArchive($aArchive, $aFile) {
  return gfReadFile('zip://' . $aArchive . "#" . $aFile);
}

/**********************************************************************************************************************
* Write file (encodes json if the file has that extension)
*
* @param $aData     Data to be written
* @param $aFile     File to write
* @returns          true else return error string
**********************************************************************************************************************/
function gfWriteFile($aData, $aFile, $aRenameFile = null) {
  if (!gfSuperVar('var', $aData)) {
    return 'No useful data to write';
  }

  if (file_exists($aFile)) {
    return 'File already exists';
  }

  if (str_ends_with($aFile, JSON_EXTENSION)) {
    $aData = json_encode($aData, JSON_ENCODE_FLAGS);
  }

  $file = fopen($aFile, FILE_WRITE_FLAGS);
  fwrite($file, $aData);
  fclose($file);

  if ($aRenameFile) {
    rename($aFile, $aRenameFile);
  }

  return true;
}

/**********************************************************************************************************************
* Generate a random hexadecimal string
*
* @param $aLength   Desired number of final chars
* @returns          Random hexadecimal string of desired lenth
**********************************************************************************************************************/
function gfHexString($aLength = 40) {
  if ($aLength <= 1) {
    $length = 1;
  }
  else {
    $length = (int)($aLength / 2);
  }

  return bin2hex(random_bytes($length));
}

/**********************************************************************************************************************
* Basic Filter Substitution of a string
*
* @param $aSubsts               multi-dimensional array of keys and values to be replaced
* @param $aString               string to operate on
* @param $aRegEx                set to true if pcre
* @returns                      bitwise int value representing applications
***********************************************************************************************************************/
function gfSubst($aSubsts, $aString, $aRegEx = null) {
  if (!is_array($aSubsts)) {
    gfError('$aSubsts must be an array');
  }

  if (!is_string($aString)) {
    gfError('$aString must be a string');
  }

  $string = $aString;

  if ($aRegEx) {
    foreach ($aSubsts as $_key => $_value) {
      $string = preg_replace('/' . $_key . '/iU', $_value, $string);
    }
  }
  else {
    foreach ($aSubsts as $_key => $_value) {
      $string = str_replace('{%' . $_key . '}', $_value, $string);
    }
  }

  if (!$string) {
    gfError('Something has gone wrong with' . SPACE . __FUNCTION__);
  }

  return $string;
}

/**********************************************************************************************************************
* Get the bitwise value of valid applications from a list of application ids
*
* @param $aTargetApplications   list of targetApplication ids
* @param $isAssoc               set false to use a list if ids
* @returns                      bitwise int value representing applications
***********************************************************************************************************************/
function gfApplicationBits($aTargetApplications, $isAssoc = true) {
  if (!is_array($aTargetApplications)) {
    gfError(__FUNCTION__ . ': You must supply an array of ids');
  }

  if ($isAssoc) {
    $aTargetApplications = array_keys($aTargetApplications);
  }

  $applications = array_combine(array_column(TARGET_APPLICATION, 'id'), array_column(TARGET_APPLICATION, 'bit'));
  $applications = array_merge([TOOLKIT_ID => TOOLKIT_BIT, TOOLKIT_ALTID => TOOLKIT_BIT], $applications);

  $applicationBits = 0;

  foreach ($applications as $_key => $_value) {
    if (in_array($_key, $aTargetApplications)) {
      $applicationBits |= $_value;
    }
  }

  return $applicationBits;
}

?>