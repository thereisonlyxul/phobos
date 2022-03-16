<?php

// == | classGenerateContent | ========================================================================================

class classContent { 
  private $libSmarty;
  

  /********************************************************************************************************************
  * Class constructor that sets initial state of things
  ********************************************************************************************************************/
  function __construct($useSmarty = null) {
    global $gaRuntime;

    $gaRuntime['smartyContent'] = false;

    if ($useSmarty) {
      $this->initSmarty();
      $gaRuntime['smartyContent'] = true;
    }
  }

  /********************************************************************************************************************
  * Initialize smarty
  ********************************************************************************************************************/
  private function initSmarty() {
    global $gaRuntime;

    // Include Smarty
    require_once(LIBRARIES['smarty']);

    $gaRuntime['qSmartyDebug'] = gfSuperVar('get', 'smartyDebug');
    $objdir = gfBuildPath(ROOT_PATH, OBJECT_RELPATH, 'smarty', $gaRuntime['qComponent'], $gaRuntime['currentSkin']);

    $this->libSmarty = new Smarty();
    $this->libSmarty->caching = 0;
    $this->libSmarty->debugging = $gaRuntime['qSmartyDebug'] ? $gaRuntime['debugMode'] : false;
    $this->libSmarty->setConfigDir($objdir . 'config')
                    ->setCacheDir($objdir . 'cache')
                    ->setCompileDir($objdir . 'compile');
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
      case 'plugin': $rv = $this->libSmarty->registerPlugin(...$aSmartyRegister); break;
      case 'filter': $rv = $this->libSmarty->registerFilter(...$aSmartyRegister); break;
      case 'class':
      case 'object':
      case 'resource':
      case 'cacheResource':
      case 'defaultPluginHandler':
        gfError($ePrefix . $aType . SPACE . 'is not currently supported.');
      default:
        gfError($ePrefix . 'Unknown smarty register type.');
    }

    return $rv;
  }

  /********************************************************************************************************************
  * Get yaml header as an array and strip the content of the yaml header
  ********************************************************************************************************************/
  public function parseYaml($aContent) {
    return ['data'    => gfSuperVar('check', @yaml_parse($aContent)),
            'content' => preg_replace(REGEX_YAML_FILTER, EMPTY_STRING, $aContent)];
  }

  /********************************************************************************************************************
  * Translates content with bbCode-like tags to HTML or returns already used HTML depending on type
  ********************************************************************************************************************/
  public function parseCodeTags($aType, $aContent) {
    $ePrefix = __CLASS__ . '::' . __FUNCTION__ . DASH_SEPARATOR;

    if (!gfSuperVar('check', $aContent)) {
      return null;
    }

    switch ($aType) {
      case 'phoebus':
        // This is the phoebusCode mangling that needs to die in a fire
        $aContent = htmlentities($aContent, ENT_XHTML);

        // Replace new lines with <br />
        $aContent = nl2br($aContent, true);

        $simpleTags = array(
          '[b]'         => '<strong>',
          '[/b]'        => '</strong>',
          '[i]'         => '<em>',
          '[/i]'        => '</em>',
          '[u]'         => '<u>',
          '[/u]'        => '</u>',
          '[ul]'        => '</p><ul><fixme />',
          '[/ul]'       => '</ul><p><fixme />',
          '[ol]'        => '</p><ol><fixme />',
          '[/ol]'       => '</ol><p><fixme />',
          '[li]'        => '<li>',
          '[/li]'       => '</li>',
          '[section]'   => '</p><h3>',
          '[/section]'  => '</h3><p><fixme />'
        );

        $regexTags = array(
          '\<(ul|\/ul|li|\/li|p|\/p)\><br \/>'  => '<$1>',
          '\[url=(.*)\](.*)\[\/url\]'           => '<a href="$1" target="_blank">$2</a>',
          '\[url\](.*)\[\/url\]'                => '<a href="$1" target="_blank">$1</a>',
          '\[img(.*)\](.*)\[\/img\]'            => EMPTY_STRING
        );

        // Process the substs
        $aContent = gfSubst('string', $simpleTags, $aContent);
        $aContent = gfSubst('regex', $regexTags, $aContent);

        // Less hacky than what is in funcReadManifest
        // Remove linebreak special cases
        $aContent = str_replace('<fixme /><br />', EMPTY_STRING, $aContent);
        break;
      case 'phobos':
      case 'selene':
        // This is a slight subset of the seleneCode used on DPMO
        $aContent = htmlentities($aContent, ENT_XHTML);

        $htmlTags       = implode(PIPE, ['p', 'span', 'small', 'br', 'hr', 'ul', 'ol', 'li', 'table', 'th', 'tr', 'td',
                                         'caption', 'col', 'colgroup', 'thead', 'tbody', 'tfoot']);

        $regexTags      = array(
          "\[\/(" . $htmlTags . ")\]"         => '</$1>',
          "\[(" . $htmlTags . ")\]"           => '<$1>',
          "\[break\]"                         => '<br />',
          "\[dblbreak\]"                      => '<br /><br/>',
          "\[separator\]"                     => '<hr style="display: block; width: 66%; margin: 2em auto;" />',
          "\[section=\"(.*)\"\]"              => '<h3>$1</h3>',
          "\[section](.*)\[\/section\]"       => '<h3>$1</h3>',
          "\[b](.*)\[\/b\]"                   => '<strong>$1</strong>',
          "\[i](.*)\[\/i\]"                   => '<em>$1</em>',
          "\[u](.*)\[\/u\]"                   => '<u>$1</u>',
          "\[link=(.*)\](.*)\[\/link\]"       => '<a href="$1">$2</a>',
          "\[url=(.*)\](.*)\[\/url\]"         => '<a href="$1" target="_blank">$2</a>',
          "\[url\](.*)\[\/url\]"              => '<a href="$1" target="_blank">$1</a>',
          '\[img(.*)\](.*)\[\/img\]'          => EMPTY_STRING
        );

        // Finally process the regex substs
        $aContent = gfSubst('regex', $regexTags, $aContent);
        break;
      case 'html':
      default:
        gfError($ePrefix . 'Unknown type.');
    }

    $javascript = ['src=', 'onload=', 'onunload=', 'onclick=', 'ondblclick=', 'onkeypress=', 'onkeyup=', 'onkeydown=',
                   'onmousedown=', 'onmouseenter=', 'onmouseleave', 'onmousemove=', 'onmouseover=', 'onmouseout=',
                   'onmouseup=', 'onblur='];

    foreach ($javascript as $_value) {
      if (str_contains($aContent, $_value)) {
        return null;
      }
    }

    // And return
    return $aContent;
  }
}

// ====================================================================================================================

?>