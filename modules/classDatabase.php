<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

class classDatabase {
  public $connection;
  private $sql;
  
  /********************************************************************************************************************
  * Class constructor that sets inital state of things
  ********************************************************************************************************************/
  function __construct() {
    global $gaRuntime;

    @require(ROOT_PATH . DATASTORE_RELPATH . '.phoebus/sql');

    if (!($arrayCreds ?? false)) {
      gfError(__CLASS__ . '::' . __FUNCTION__ . ' - Could not read aql file');
    }

    $arrayCreds['currentDB'] = $arrayCreds['liveDB'];

    if ($this->gaRuntime['debugMode']) {
      $arrayCreds['currentDB'] = $arrayCreds['devDB'];;
    }

    $this->connection = mysqli_connect('localhost', $arrayCreds['username'], $arrayCreds['password'], $arrayCreds['currentDB']);
    
    if (mysqli_connect_errno($this->connection)) {
      gfError('SQL Connection Error: ' . mysqli_connect_errno($this->connection));
    }
    
    mysqli_set_charset($this->connection, 'utf8');

    require_once(LIBRARIES['safeMySQL']);

    $this->sql = new SafeMysql(['mysqli' => $this->connection]);
  }

  /********************************************************************************************************************
  * Class deconstructor that cleans up items
  ********************************************************************************************************************/
  function __destruct() {
    if ($this->connection) {
      $this->sql = null;
      mysqli_close($this->connection);
    }
  }

  /********************************************************************************************************************
  * Raw mysqli query
  ********************************************************************************************************************/
  public function raw ($aQuery) {
    return gfSuperVar('var', mysqli_query($this->connection, $aQuery));
  }


  /********************************************************************************************************************
  * Raw mysqli multi-query
  ********************************************************************************************************************/
  public function multiRaw ($aQuery) {
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
  public function get($aQueryType, .. $aArgs) {
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
        gfError(__CLASS__ . '::' . __FUNCTION__ . ' - Unknown get type');
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
