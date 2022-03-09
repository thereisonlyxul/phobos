<?php

// == | classGenerateContent | ========================================================================================

class classContent { 
  private $libSmarty;

  /********************************************************************************************************************
  * Class constructor that sets initial state of things
  ********************************************************************************************************************/
  function __construct() {
    global $gaRuntime;

    if ($gaRuntime['useSmarty'] ?? null) {
      $this->initSmarty();
    }
  }

  /********************************************************************************************************************
  * Registers various smarty things for use in templates
  ********************************************************************************************************************/
  public function registerSmarty($aType, $aSmartyRegister) {
    $ePrefix = __CLASS__ . '::' . __FUNCTION__ . DASH_SEPARATOR;

    if (!$this->libSmarty) {
      gfError($ePrefix . 'Could not register' . ucFirst($aType) . '()' . SPACE . 'because smarty was not initialized');
    }

    switch ($aType) {
      case 'plugin':
        $rv = $this->libSmarty->registerPlugin(...$aSmartyRegister);
        break;
      case 'filter':
        $rv = $this->libSmarty->registerFilter(...$aSmartyRegister);
        break;
      case 'class':
      case 'object':
      case 'resource':
      case 'cacheResource':
      case 'defaultPluginHandler':
        gfError($ePrefix . $aType . SPACE . 'is not currently supported.';
      default:
        gfError($ePrefix . 'Unknown smarty register type.';
    }

    return $rv;
  }

  /********************************************************************************************************************
  * Initialize smarty
  ********************************************************************************************************************/
  private function initSmarty() {
    global $gaRuntime;

    // Include Smarty
    require_once(LIBRARIES['smarty']);

    $gaRuntime['qSmartyDebug'] = gfSuperVar('get', 'smartyDebug');
    $objdir = gfBuildPath(ROOT_PATH, OBJECT_RELPATH, 'smarty', $gaRuntime['currentSkin'], $gaRuntime['qComponent']);

    $this->libSmarty = new Smarty();
    $this->libSmarty->caching = 0;
    $this->libSmarty->debugging = $gaRuntime['qSmartyDebug'] ? $gaRuntime['debugMode'] : false;
    $this->libSmarty->setConfigDir($objdir . 'config')
                    ->setCacheDir($objdir . 'cache')
                    ->setCompileDir($objdir . 'compile');
  }
}

// ====================================================================================================================

?>