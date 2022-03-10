<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Setup | =======================================================================================================

// Check if the basic defines have been defined in the including script
foreach (['ROOT_PATH', 'DEBUG_MODE',  'SOFTWARE_NAME', 'SOFTWARE_VERSION'] as $_value) {
  if (!defined($_value)) {
    die('Binary Outcast Utilities: ' . $_value . ' must be defined before including this script.');
  }
}

// Do not allow this to be included more than once...
if (defined('BINOC_UTILS')) {
  die('Binary Outcast Utilities: You may not include this more than once.');
}

// Define that this is a thing.
define('BINOC_UTILS', 1);

// ====================================================================================================================

// == | Global Constants | ============================================================================================

const PHP_ERROR_CODES       = array(
  E_ERROR                   => 'Fatal Error',
  E_WARNING                 => 'Warning',
  E_PARSE                   => 'Parse',
  E_NOTICE                  => 'Notice',
  E_CORE_ERROR              => 'Fatal Error (Core)',
  E_CORE_WARNING            => 'Warning (Core)',
  E_COMPILE_ERROR           => 'Fatal Error (Compile)',
  E_COMPILE_WARNING         => 'Warning (Compile)',
  E_USER_ERROR              => 'Fatal Error (User Generated)',
  E_USER_WARNING            => 'Warning (User Generated)',
  E_USER_NOTICE             => 'Notice (User Generated)',
  E_STRICT                  => 'Strict',
  E_RECOVERABLE_ERROR       => 'Fatal Error (Recoverable)',
  E_DEPRECATED              => 'Deprecated',
  E_USER_DEPRECATED         => 'Deprecated (User Generated)',
  E_ALL                     => 'All'
);

const HTTP_HEADERS          = array(
  404                       => 'HTTP/1.1 404 Not Found',
  501                       => 'HTTP/1.1 501 Not Implemented',
  'text'                    => 'Content-Type: text/plain',
  'html'                    => 'Content-Type: text/html',
  'css'                     => 'Content-Type: text/css',
  'xml'                     => 'Content-Type: text/xml',
  'json'                    => 'Content-Type: application/json',
  'bin'                     => 'Content-Type: application/octet-stream',
  'xpi'                     => 'Content-Type: application/x-xpinstall',
  '7z'                      => 'Content-Type: application/x-7z-compressed',
  'xz'                      => 'Content-Type: application/x-xz',
);

// --------------------------------------------------------------------------------------------------------------------

const NEW_LINE              = "\n";
const EMPTY_STRING          = "";
const EMPTY_ARRAY           = [];
const SPACE                 = " ";
const WILDCARD              = "*";
const SLASH                 = "/";
const DOT                   = ".";
const DASH                  = "-";
const UNDERSCORE            = "_";
const PIPE                  = "|";
const DOLLAR                = "\$";
const COLON                 = ":";
const DBLCOLON              = COLON . COLON;
const DOTDOT                = DOT . DOT;

// --------------------------------------------------------------------------------------------------------------------

const DASH_SEPARATOR        = SPACE . DASH . SPACE;
const SCHEME_SUFFIX         = "://";

const PHP_EXTENSION         = DOT . 'php';
const INI_EXTENSION         = DOT . 'ini';
const HTML_EXTENSION        = DOT . 'html';
const XML_EXTENSION         = DOT . 'xml';
const RDF_EXTENSION         = DOT . 'rdf';
const JSON_EXTENSION        = DOT . 'json';
const CONTENT_EXTENSION     = DOT . 'content';
const XPINSTALL_EXTENSION   = DOT . 'xpi';
const WINSTALLER_EXTENSION  = DOT . 'installer' . DOT .'exe';
const WINPORTABLE_EXTENSION = DOT . 'portable' . DOT .'exe';
const SEVENZIP_EXTENSION    = DOT . '7z';
const TARGZ_EXTENSION       = DOT . 'tar' . DOT . 'gz';
const TGZ_EXTENSION         = DOT . 'tgz';
const TARBZ2_EXTENSION      = DOT . 'tar' . DOT . 'bz2';
const TBZ_EXTENSION         = DOT . 'tbz';
const TARXZ_EXTENSION       = DOT . 'tar' . DOT . 'xz';
const TXZ_EXTENSION         = DOT . 'txz';
const MAR_EXTENSION         = DOT . 'mar';
const TEMP_EXTENSION        = DOT . 'temp';

// --------------------------------------------------------------------------------------------------------------------

const XML_TAG               = '<?xml version="1.0" encoding="utf-8" ?>';

// --------------------------------------------------------------------------------------------------------------------

const RDF_INSTALL_MANIFEST  = 'install' . RDF_EXTENSION;
const JSON_INSTALL_MANIFEST = 'install' . JSON_EXTENSION;

// --------------------------------------------------------------------------------------------------------------------

const JSON_ENCODE_FLAGS     = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
const FILE_WRITE_FLAGS      = "w+";

// --------------------------------------------------------------------------------------------------------------------

const REGEX_GET_FILTER      = "/[^-a-zA-Z0-9_\-\/\{\}\@\.\%\s\,]/";
const REGEX_YAML_FILTER     = "/\A---(.|\n)*?---/";
const REGEX_GUID            = "/^\{[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\}$/i";
const REGEX_HOST            = "/[a-z0-9-\._]+\@[a-z0-9-\._]+/i";

// --------------------------------------------------------------------------------------------------------------------

const PASSWORD_CLEARTEXT    = "clrtxt";
const PASSWORD_HTACCESS     = "apr1";

const BASE64_ALPHABET       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
const APRMD5_ALPHABET       = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

// ====================================================================================================================

// == | Global Functions | ============================================================================================

/**********************************************************************************************************************
* Polyfills for str_starts_with, str_ends_with, str_contains
*
* @param $haystack  string
* @param $needle    substring
* @returns          true if substring exists in string else false
**********************************************************************************************************************/
if (!function_exists('str_starts_with')) {
  function str_starts_with($haystack, $needle) {
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
  }

  // Compatibility with previously used polyfill function name
  function startsWith(...$aArgs) { return str_starts_with(...$aArgs); }
}

// --------------------------------------------------------------------------------------------------------------------

if (!function_exists('str_ends_with')) {
  function str_ends_with($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
      return true;
    }

    return (substr($haystack, -$length) === $needle);
  }

  // Compatibility with previously used polyfill function name
  function endsWith(...$aArgs) { return str_ends_with(...$aArgs); }
}

// --------------------------------------------------------------------------------------------------------------------

if (!function_exists('str_contains')) {
  function str_contains($haystack, $needle) {
    if (strpos($haystack, $needle) > -1) {
      return true;
    }
    else {
      return false;
    }
  }

  // Compatibility with previously used polyfill function name
  function contains(...$aArgs) { return str_contains(...$aArgs); }
}

/**********************************************************************************************************************
* Error function that will display data (Error Message)
*
* This version of the function can emit the error as xml or text depending on the environment.
* It also can use gfContent() if defined and has the same signature.
* It also has its legacy ability for generic output if the error message is not a string as formatted json
* regardless of the environment.
*
* @dep gfContent() - conditional
* @dep NEW_LINE
* @dep XML_TAG
* @dep JSON_ENCODE_FLAGS
**********************************************************************************************************************/
function gfError($aValue, $aPHPError = false, $aExternalOutput = null) { 
  $pageHeader = ['default' => 'Unable to Comply', 'php' => 'PHP Error', 'output' => 'Output'];

  $externalOutput = $aExternalOutput ?? function_exists('gfContent');
  $isCLI = (php_sapi_name() == "cli");
  $isOutput = false;

  if (is_string($aValue) || is_int($aValue)) {
    $eContentType = 'text/xml';
    $ePrefix = $aPHPError ? $pageHeader['php'] : $pageHeader['default'];

    if ($externalOutput || $isCLI) {
      $eMessage = $aValue;
    }
    else {
      $eMessage = XML_TAG . NEW_LINE . '<error title="' . $ePrefix . '">' . $aValue . '</error>';
    }
  }
  else {
    $isOutput = true;
    $eContentType = 'application/json';
    $ePrefix = $pageHeader['output'];
    $eMessage = json_encode($aValue, JSON_ENCODE_FLAGS);
  }

  if ($externalOutput) {
    if ($aPHPError) {
      gfContent($ePrefix, $eMessage, null, true, true);
    }

    if ($isOutput) {
      gfContent($ePrefix, $eMessage, true, false, true);
    }
    
    gfContent($ePrefix, $eMessage, null, null, true);
  }
  elseif ($isCLI) {
    print('========================================' . NEW_LINE .
          $ePrefix . NEW_LINE .
          '========================================' . NEW_LINE .
          $eMessage . NEW_LINE);
  }
  else {
    header('Content-Type: ' . $eContentType, false);
    print($eMessage);
  }

  // We're done here.
  exit();
}

/**********************************************************************************************************************
* PHP Error Handler
*
* @dep SPACE
* @dep PHP_ERROR_CODES
* @dep gfError()
**********************************************************************************************************************/
function gfErrorHandler($eCode, $eString, $eFile, $eLine) {
  $eType = PHP_ERROR_CODES[$eCode] ?? $eCode;
  $eMessage = $eType . ': ' . $eString . SPACE . 'in' . SPACE .
                  str_replace(ROOT_PATH, '', $eFile) . SPACE . 'on line' . SPACE . $eLine;

  if (!(error_reporting() & $eCode)) {
    // Don't do jack shit because the developers of PHP think users shouldn't be trusted.
    return;
  }

  gfError($eMessage, true);
}

// Set error handler fairly early...
set_error_handler("gfErrorHandler");

/**********************************************************************************************************************
* Unified Var Checking
*
* @dep DASH_SEPARATOR
* @dep UNDERSCORE
* @dep EMPTY_STRING
* @dep REGEX_GET_FILTER
* @dep gfError()
* @param $aVarType        Type of var to check
* @param $aVarValue       GET/SERVER/EXISTING Normal Var
* @param $aFalsy          Optional - Allow falsey returns on var/direct
* @returns                Value or null
**********************************************************************************************************************/
function gfSuperVar($aVarType, $aVarValue) {
  // Set up the Error Message Prefix
  $ePrefix = __FUNCTION__ . DASH_SEPARATOR;
  $rv = null;

  // Turn the variable type into all caps prefixed with an underscore
  $varType = UNDERSCORE . strtoupper($aVarType);

  // General variable absolute null check unless falsy is allowed
  if ($varType == "_CHECK" || $varType == "_VAR"){
    $rv = $aVarValue;

    if (empty($rv) || $rv === 'none' || $rv === 0) {
      return null;
    }

    return $rv;
  }

  // This handles the superglobals
  switch($varType) {
    case '_SERVER':
    case '_GET':
    case '_FILES':
    case '_POST':
    case '_COOKIE':
    case '_SESSION':
      $rv = $GLOBALS[$varType][$aVarValue] ?? null;
      break;
    default:
      // We don't know WHAT was requested but it is obviously wrong...
      gfError($ePrefix . 'Incorrect Var Check');
  }
  
  // We always pass $_GET values through a general regular expression
  // This allows only a-z A-Z 0-9 - / { } @ % whitespace and ,
  if ($rv && $varType == "_GET") {
    $rv = preg_replace(REGEX_GET_FILTER, EMPTY_STRING, $rv);
  }

  // Files need special handling.. In principle we hard fail if it is anything other than
  // OK or NO FILE
  if ($rv && $varType == "_FILES") {
    if (!in_array($rv['error'], [UPLOAD_ERR_OK, UPLOAD_ERR_NO_FILE])) {
      gfError($ePrefix . 'Upload of ' . $aVarValue . ' failed with error code: ' . $rv['error']);
    }

    // No file is handled as merely being null
    if ($rv['error'] == UPLOAD_ERR_NO_FILE) {
      return null;
    }

    // Cursory check the actual mime-type and replace whatever the web client sent
    $rv['type'] = mime_content_type($rv['tmp_name']);
  }
  
  return $rv;
}

/**********************************************************************************************************************
* Sends HTTP Headers to client using a short name
*
* @dep HTTP_HEADERS
* @dep DEBUG_MODE
* @dep gfError()
* @param $aHeader    Short name of header
**********************************************************************************************************************/
function gfHeader($aHeader, $aReplace = true) { 
  $ePrefix = __FUNCTION__ . DASH_SEPARATOR;
  $debugMode = DEBUG_MODE;
  $isErrorPage = in_array($aHeader, [404, 501]);

  global $gaRuntime;

  if ($gaRuntime['debugMode'] ?? null || DEBUG_MODE) {
    $debugMode = $gaRuntime['debugMode'];
  }

  if (!array_key_exists($aHeader, HTTP_HEADERS)) {
    gfError($ePrefix . 'Unknown' . SPACE . $aHeader . SPACE . 'header');
  }

  if ($debugMode && $isErrorPage) {
    gfError($ePrefix . HTTP_HEADERS[$aHeader]);
  }

  if (!headers_sent()) { 
    header(HTTP_HEADERS[$aHeader], $aReplace);

    if ($isErrorPage) {
      exit();
    }
  }
}

/**********************************************************************************************************************
* 404 or Error
*
* @param $aErrorMessage   Error message if debug
***********************************************************************************************************************/
function gfErrorOr404($aErrorMessage) {
  global $gaRuntime;

  if ($gaRuntime['debugMode'] ?? null || DEBUG_MODE) {
    gfError($aErrorMessage);
  }

  gfHeader(404);
}

/**********************************************************************************************************************
* Sends HTTP Header to redirect the client to another URL
*
* @param $aURL   URL to redirect to
**********************************************************************************************************************/
// This function sends a redirect header
function gfRedirect($aURL) {
  header('Location: ' . $aURL, true, 302);
  
  // We are done here
  exit();
}

/**********************************************************************************************************************
* Simply prints output and sends header if not cli and exits
**********************************************************************************************************************/
function gfOutput($aContent, $aHeader = 'text') {
  if (php_sapi_name() != "cli") {
    gfHeader($aHeader);
  }

  print($aContent);
  exit();
}

/**********************************************************************************************************************
* Explodes a string to an array without empty elements if it starts or ends with the separator
*
* @dep DASH_SEPARATOR
* @dep gfError()
* @param $aSeparator   Separator used to split the string
* @param $aString      String to be exploded
* @returns             Array of string parts
***********************************************************************************************************************/
function gfExplodeString($aSeparator, $aString) {
  $ePrefix = __FUNCTION__ . DASH_SEPARATOR;

  if (!is_string($aString)) {
    gfError($ePrefix . 'Specified string is not a string type');
  }

  if (!str_contains($aString, $aSeparator)) {
    gfError($ePrefix . 'String does not contain the separator');
  }

  $explodedString = array_values(array_filter(explode($aSeparator, $aString), 'strlen'));

  return $explodedString;
}

/**********************************************************************************************************************
* Splits a path into an indexed array of parts
*
* @dep SLASH
* @dep gfExplodeString()
* @param $aPath   URI Path
* @returns        array of uri parts in order
***********************************************************************************************************************/
function gfExplodePath($aPath) {
  if ($aPath == SLASH) { return ['root']; }
  return gfExplodeString(SLASH, $aPath);
}

/**********************************************************************************************************************
* Builds a path from a list of arguments
*
* @dep ROOT_PATH
* @dep SLASH
* @dep DOT
* @param        ...$aPathParts  Path Parts
* @returns                      Path string
***********************************************************************************************************************/
function gfBuildPath(...$aPathParts) {
  $path = implode(SLASH, $aPathParts);
  $filesystem = str_starts_with($path, ROOT_PATH);
  
  // Add a pre-pending slash if this is not a file system path
  if (!$filesystem) {
    $path = SLASH . $path;
  }

  // Add a trailing slash if the last part does not contain a dot
  // If it is a file system path then we will also add a trailing slash if the last part starts with a dot
  if (!str_contains(basename($path), DOT) || ($filesystem && str_starts_with(basename($path), DOT))) {
    $path .= SLASH;
  }

  return $path;
}

/**********************************************************************************************************************
* Strips the constant ROOT_PATH from a string
*
* @dep ROOT_PATH
* @dep EMPTY_STRING
* @param $aPath   Path to be stripped
* @returns        Stripped path
***********************************************************************************************************************/
function gfStripRootPath($aPath) {
  return str_replace(ROOT_PATH, EMPTY_STRING, $aPath);
}

/**********************************************************************************************************************
* Get a subdomain or base domain from a host
*
* @dep DOT
* @dep gfExplodeString()
* @param $aHost       Hostname
* @param $aReturnSub  Should return subdmain
* @returns            domain or subdomain
***********************************************************************************************************************/
function gfGetDomain($aHost, $aReturnSub = null) {
  $host = gfExplodeString(DOT, $aHost);
  $domainSlice = $aReturnSub ? array_slice($host, 0, -2) : array_slice($host, -2, 2);
  $rv = implode(DOT, $domainSlice);
  return $rv;
}

/**********************************************************************************************************************
* Includes a module
*
* @dep MODULES - Phoebus-Style Array Constant
* @dep gfError()
* @param $aModules    List of modules
**********************************************************************************************************************/
function gfImportModules(...$aModules) {
  if (!defined('MODULES')) {
    gfError('MODULES is not defined');
  }

  foreach ($aModules as $_value) {
    if (is_array($_value)) {
      if (str_starts_with($_value[0], 'static:')) {
        gfError('Cannot initialize a static class with arguments.');
      }

      $include = str_replace('static:' , EMPTY_STRING, $_value[0]);
      $className = 'class' . ucfirst($_value[0]);
      $moduleName = 'gm' . ucfirst($_value[0]);
      unset($_value[0]);
      $args = array_values($_value);
    }
    else {
      $include = str_replace('static:' , EMPTY_STRING, $_value);
      $className = str_starts_with($_value, 'static:') ? false : 'class' . ucfirst($include);
      $moduleName = 'gm' . ucfirst($include);
      $args = null;
    }
   
    if (array_key_exists($moduleName, $GLOBALS)) {
      gfError('Module ' . $include . ' has already been imported.');
    }

    if (!array_key_exists($include, MODULES)) {
      gfError('Unable to import unknown module ' . $include . DOT);
    }

    require(MODULES[$include]);

    if ($args) {
      $GLOBALS[$moduleName] = new $className(...$args);
    }
    else {
      $GLOBALS[$moduleName] = ($className === false) ? true : new $className();
    }
  }
}

/**********************************************************************************************************************
* Check if a module has been included
*
* @dep EMPTY_ARRAY
* @dep gfError()
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
* @dep JSON_EXTENSION
* @dep gfError()
* @dep gfSuperVar()
* @dep $gmAviary - Conditional
* @param $aFile     File to read
* @returns          file contents or array if json
                    null if error, empty string, or empty array
**********************************************************************************************************************/
function gfReadFile($aFile) {
  $file = @file_get_contents($aFile);

  // Automagically decode json
  if (str_ends_with($aFile, JSON_EXTENSION)) {
    $file = json_decode($file, true);
  }

  // If it is a mozilla install manifest and the module has been included then parse it
  if (str_ends_with($aFile, RDF_INSTALL_MANIFEST) && array_key_exists('gmAviary', $GLOBALS)) {
    global $gmAviary;
    $file = $gmAviary->parseInstallManifest($file);

    if (is_string($file)) {
      gfError('RDF Parsing Error: ' . $file);
    }
  }

  return gfSuperVar('var', $file);
}

/**********************************************************************************************************************
* Read file from zip-type archive
*
* @dep gfReadFile()
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
* @dep JSON_EXTENSION
* @dep JSON_ENCODE_FLAGS
* @dep FILE_WRITE_FLAGS
* @dep gfSuperVar()
* @param $aData     Data to be written
* @param $aFile     File to write
* @returns          true else return error string
**********************************************************************************************************************/
function gfWriteFile($aData, $aFile, $aRenameFile = null, $aOverwrite = null) {
  if (!gfSuperVar('var', $aData)) {
    return 'No useful data to write';
  }

  if ($aOverwrite && $file_exists($aFile)) {
    return 'File already exists';
  }

  if (str_ends_with($aFile, JSON_EXTENSION)) {
    $aData = json_encode($aData, JSON_ENCODE_FLAGS);
  }

  $file = @file_put_contents($aFile, $aData);

  if ($file === false) {
    return 'Could not write file';
  }

  if ($aRenameFile) {
    rename($aFile, $aRenameFile);
  }

  return true;
}

/**********************************************************************************************************************
* Generate a random hexadecimal string
*
* @param $aLength   Desired number of final chars
* @returns          Random hexadecimal string of desired length
**********************************************************************************************************************/
function gfHexString($aLength = 40) {
  return bin2hex(random_bytes(($aLength <= 1) ? 1 : (int)($aLength / 2)));
}

/**********************************************************************************************************************
* Basic Filter Substitution of a string
*
* @dep EMPTY_STRING
* @dep SLASH
* @dep SPACE
* @dep gfError()
* @param $aSubsts               multi-dimensional array of keys and values to be replaced
* @param $aString               string to operate on
* @param $aRegEx                set to true if pcre
* @returns                      bitwise int value representing applications
***********************************************************************************************************************/
function gfSubst($aMode, $aSubsts, $aString) {
  $ePrefix = __FUNCTION__ . DASH_SEPARATOR;
  if (!is_array($aSubsts)) {
    gfError($ePrefix . '$aSubsts must be an array');
  }

  if (!is_string($aString)) {
    gfError($ePrefix . '$aString must be a string');
  }

  $rv = $aString;

  switch ($aMode) {
    case 'simple':
    case 'string':
      foreach ($aSubsts as $_key => $_value) { $rv = str_replace($_key, $_value, $rv); }
      break;
    case 'regex':
      foreach ($aSubsts as $_key => $_value) { $rv = preg_replace(SLASH . $_key . SLASH . 'iU', $_value, $rv); }
      break;
    default:
      gfError($ePrefix . 'Unknown mode');
  }

  if (!$rv) {
    gfError($ePrefix . 'Something has gone wrong...');
  }

  return $rv;
}

/**********************************************************************************************************************
* Request HTTP Basic Authentication
*
* @dep SOFTWARE_NAME
* @dep gfError()
***********************************************************************************************************************/
function gfBasicAuthPrompt() {
  header('WWW-Authenticate: Basic realm="' . SOFTWARE_NAME . '"');
  header('HTTP/1.0 401 Unauthorized');   
  gfError('You need to enter a valid username and password.');
}

/**********************************************************************************************************************
* Hash a password
***********************************************************************************************************************/
function gfPasswordHash($aPassword, $aCrypt = PASSWORD_BCRYPT, $aSalt = null) {
  $ePrefix = __FUNCTION__ . DASH_SEPARATOR;

  // We can "hash" a cleartext password by prefixing it with the fake algo prefix $clear$
  if ($aCrypt == PASSWORD_CLEARTEXT) {
    if (str_contains($aPassword, DOLLAR)) {
      // Since the dollar sign is used as an identifier and/or separator for hashes we can't use passwords
      // that contain said dollar sign.
      gfError($ePrefix . 'Cannot "hash" this Clear Text password because it contains a dollar sign.');
    }

    return DOLLAR . PASSWORD_CLEARTEXT . DOLLAR . time() . DOLLAR . $aPassword;
  }

  // We want to be able to generate Apache APR1-MD5 hashes for use in .htpasswd situations.
  if ($aCrypt == PASSWORD_HTACCESS) {
    $salt = $aSalt;

    if (!$salt) {
      $salt = EMPTY_STRING;

      for ($i=0; $i<8; $i++) {
        $offset = hexdec(bin2hex(openssl_random_pseudo_bytes(1))) % 64;
        $salt .= APRMD5_ALPHABET[$offset];
      }
    }

    $salt = substr($salt, 0, 8);
    $max = strlen($aPassword);
    $context = $aPassword . DOLLAR . PASSWORD_HTACCESS . DOLLAR .$salt;
    $binary = pack('H32', md5($aPassword . $salt . $aPassword));

    for ($i=$max; $i>0; $i-=16) {
      $context .= substr($binary, 0, min(16, $i));
    }

    for ($i=$max; $i>0; $i>>=1) {
      $context .= ($i & 1) ? chr(0) : $aPassword[0];
    }

    $binary = pack('H32', md5($context));

    for ($i=0; $i<1000; $i++) {
      $new = ($i & 1) ? $aPassword : $binary;

      if ($i % 3) {
        $new .= $salt;
      }
      if ($i % 7) {
        $new .= $aPassword;
      }

      $new .= ($i & 1) ? $binary : $aPassword;
      $binary = pack('H32', md5($new));
    }

    $hash = EMPTY_STRING;

    for ($i = 0; $i < 5; $i++) {
      $k = $i+6;
      $j = $i+12;
      if($j == 16) $j = 5;
      $hash = $binary[$i] . $binary[$k] . $binary[$j] . $hash;
    }

    $hash = chr(0) . chr(0) . $binary[11] . $hash;
    $hash = strtr(strrev(substr(base64_encode($hash), 2)), BASE64_ALPHABET, APRMD5_ALPHABET);

    return DOLLAR . PASSWORD_HTACCESS . DOLLAR . $salt . DOLLAR . $hash;
  }

  // Else, our standard (and secure) default is PASSWORD_BCRYPT hashing.
  // We do not allow custom salts for anything using password_hash as PHP generates secure salts.
  // PHP Generated passwords are also self-verifiable via password_verify.
  return password_hash($aPassword, $aCrypt);
}

/**********************************************************************************************************************
* Check a password
***********************************************************************************************************************/
function gfPasswordVerify($aPassword, $aHash) {
  $ePrefix = __FUNCTION__ . DASH_SEPARATOR;

  // We can accept a pseudo-hash for clear text passwords in the format of $clrtxt$unix-epoch$clear-text-password
  if (str_starts_with($aHash, DOLLAR . PASSWORD_CLEARTEXT)) {
    $password = gfExplodeString(DOLLAR, $aHash) ?? null;

    if ($password == null || count($password) > 3) {
      gfError($ePrefix . 'Unable to "verify" this Clear Text "hashed" password.');
    }

    return $aPassword === $password[2];
  }

  // We can also accept an Apache APR1-MD5 password that is commonly used in .htpasswd
  if (str_starts_with($aHash, DOLLAR . PASSWORD_HTACCESS)) {
    $salt = gfExplodeString(DOLLAR, $aHash)[1] ?? null;

    if(!$salt) {
      gfError($ePrefix . 'Unable to verify this Apache APR1-MD5 hashed password.');
    }

    return gfPasswordHash($aPassword, PASSWORD_HTACCESS, $salt) === $aHash;
  }

  // For everything else send to the native password_verify function.
  // It is almost certain to be a BCRYPT2 hash but hashed passwords generated BY PHP are self-verifiable.
  return password_verify($aPassword, $aHash);
}

/**********************************************************************************************************************
* Create DOM Elements from a properly constructed array
***********************************************************************************************************************/
function gfArrayToDOM($aDom, $aData) {
  if (!($aDom instanceof DOMDocument)) {
    gfError('$aDom should be an instance of DOMDocument');
  }

  if (!($aData['@element'] ?? null)) {
    return false;
  }

  // Create the element
  $element = $aDom->createElement($aData['@element']);

  // Properly handle content using XML and not try and be lazy.. It almost never works!
  if (array_key_exists('@content', $aData) && is_string($aData['@content'])) {
    if (str_contains($aData['@content'], "<") || str_contains($aData['@content'], ">") || str_contains($aData['@content'], "?") ||
        str_contains($aData['@content'], "&") || str_contains($aData['@content'], "'") || str_contains($aData['@content'], '"')) {
      $content = $aDom->createCDATASection($aData['@content'] ?? EMPTY_STRING);
    }
    else {
      $content = $aDom->createTextNode($aData['@content'] ?? EMPTY_STRING);
    }

    $element->appendChild($content);
  }
 
  // Add any attributes
  if (!empty($aData['@attributes']) && is_array($aData['@attributes'])) {
    foreach ($aData['@attributes'] as $_key => $_value) {
      $element->setAttribute($_key, $_value);
    }
  }
 
  // Any other items in the data array should be child elements
  foreach ($aData as $_key => $_value) {
    if (!is_numeric($_key)) {
      continue;
    }
 
    $child = gfArrayToDOM($aDom, $_value);
    if ($child) {
      $element->appendChild($child);
    }
  }
 
  return $element;
}

/**********************************************************************************************************************
* Create an XML Document 
***********************************************************************************************************************/
function gfGenerateXML($aData, $aDirectOutput = null) {
  $doc = new DOMDocument('1.0');
  $doc->encoding = "UTF-8";
  $doc->formatOutput = true;

  $child = gfArrayToDOM($doc, $aData);
  $xml = null;

  if ($child) {
    $doc->appendChild($child);
  }

  $xml = $doc->saveXML();

  if (!$xml) {
    gfError('Could not generate xml/rdf.');
  }

  // We want to always use UTF8 encoding and NOT have that shit translated into entities because raisins 
  $xml = html_entity_decode($xml, ENT_QUOTES | ENT_SUBSTITUTE | ENT_XML1, "UTF-8");

  if ($aDirectOutput) {
    gfOutput($xml, 'xml');
  }

  return $xml;
}

/**********************************************************************************************************************
* Show dates in different forms from epoch (or any value that date() accepts.. 
***********************************************************************************************************************/
function gfDate($aTypeOrFormat, $aDateStamp, $aReturnTime = null) {
  if ($aTypeOrFormat == 'buildNumber') {
    return date_diff(date_create(date('Y-m-D', $aDateStamp)), date_create('2000-01-01'))->format('%a');
  }

  $format = EMPTY_STRING;

  switch ($aTypeOrFormat) {
    case 'standard':
      $format = $aReturnTime ? 'Y-m-D, H:i' : 'Y-m-D';
      break;
    case 'longUS':
      $format = $aReturnTime ? 'F j, Y, H:i' : 'F j, Y';
      break;
    case 'shortUS':
      $format = $aReturnTime ? 'm-d-Y, H:i' : 'm-d-Y';
      break;
    case 'eDate':
      $format = 'Ymd.Hi';
      break;
    case 'buildDate':
      $format = 'YmdHi';
      break;
    default:
      $format = $aTypeOrFormat;
  }

  $rv = date($format, $aDateStamp);

  if (!$rv) {
    return $aDateStamp;
  }

  return $rv;
}

/**********************************************************************************************************************
* Simple and almost certainly painless hack to convert an object into an array
***********************************************************************************************************************/
function gfObjectToArray($aObject) {
  return json_decode(json_encode($aObject), true);
}

// ====================================================================================================================

?>