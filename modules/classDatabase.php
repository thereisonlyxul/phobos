<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

class classDatabase {
  public $connection;
  private $sql;
  
  /********************************************************************************************************************
  * Class constructor that sets initial state of things
  ********************************************************************************************************************/
  function __construct() {
    $ePrefix = __CLASS__ . '::' . __FUNCTION__ . DASH_SEPARATOR;
    global $gaRuntime;

    $creds = gfReadFile(gfBuildPath(ROOT_PATH, DATASTORE_RELPATH, '.config', 'sql' . JSON_EXTENSION));

    if (!$creds) {
      gfError($ePrefix . 'Could not read sql configuration');
    }

    $gaRuntime['currentDatabase'] = $creds['liveDB'];

    if ($gaRuntime['phpServerName'] == DEVELOPER_DOMAIN) {
      $gaRuntime['currentDatabase'] = $creds['devDB'];
    }

    $this->connection = mysqli_connect('localhost', $creds['username'], $creds['password'], $gaRuntime['currentDatabase']);

    if (mysqli_connect_errno()) {
      gfError($ePrefix . 'SQL Connection Error: ' . mysqli_connect_errno($this->connection));
    }

    mysqli_set_charset($this->connection, 'utf8');

    require_once(LIBRARIES['safeMySQL']);

    $this->sql = new SafeMysql(['mysqli' => $this->connection]);
  }

  /********************************************************************************************************************
  * Class deconstructor that cleans up items
  ********************************************************************************************************************/
  function __destruct() {
    global $gaRuntime;

    if ($this->connection) {
      $this->sql = null;
      mysqli_close($this->connection);
      $gaRuntime['currentDatabase'] = false;
    }
  }

  /********************************************************************************************************************
  * Force a specific database
  ********************************************************************************************************************/
  public function changeDB($aDatabase) {
    $ePrefix = __CLASS__ . '::' . __FUNCTION__ . DASH_SEPARATOR;
    global $gaRuntime;

    $dbChange = mysqli_select_db($this->connection, $aDatabase);

    if ($dbChange) {
      $gaRuntime['currentDatabase'] = $this->sql->getCol("SELECT DATABASE()")[0];
      return $dbChange;
    }

    gfError($ePrefix . ': failed to change database to ' . $aDatabase);
  }

  /********************************************************************************************************************
  * Raw mysqli query
  ********************************************************************************************************************/
  public function raw($aQuery) {
    return gfSuperVar('var', mysqli_query($this->connection, $aQuery));
  }


  /********************************************************************************************************************
  * Raw mysqli multi-query
  ********************************************************************************************************************/
  public function multiRaw($aQuery) {
    return gfSuperVar('var', mysqli_multi_query($this->connection, $aQuery));
  }

  /********************************************************************************************************************
  * Normal query using SafeMySQL
  *
  * @param    ...$aArgs     Expanded list of arguments
  * @return   array with result or null
  ********************************************************************************************************************/
  public function query(...$aArgs) {
    return gfSuperVar('var', $this->sql->query(...$aArgs));
  }

  /********************************************************************************************************************
  * Get queries using SafeMySQL
  *
  * @param    string        col|row|all
  * @param    ...$aArgs     Expanded list of arguments
  * @return   array with result or null
  ********************************************************************************************************************/
  public function get($aQueryType, ...$aArgs) {
    $ePrefix = __CLASS__ . '::' . __FUNCTION__ . DASH_SEPARATOR;

    switch ($aQueryType) {
      case 'col':
        $result = $this->sql->getCol(...$aArgs);
        break;
      case 'row':
        $result = $this->sql->getRow(...$aArgs);
        break;
      case 'all':
        $result = $this->sql->getAll(...$aArgs);
        break;
      default:
        gfError($ePrefix . ' - Unknown get type');
    }

    return gfSuperVar('var', $result);
  }

  /********************************************************************************************************************
  * Parses query using SafeMySQL
  *
  * @param    ...$aArgs     Expanded list of arguments
  * @return   parsed query string or null
  ********************************************************************************************************************/
  public function parse(...$aArgs) {
    return gfSuperVar('var', $this->sql->parse(...$aArgs));
  }
}

?>
