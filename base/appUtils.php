<?php

// == | Setup | =======================================================================================================

/* Known Application IDs
 * Application IDs are normally in the form of a {GUID} or user@host ID.
 *
 * Mozilla Suite:             {86c18b42-e466-45a9-ae7a-9b95ba6f5640}
 * Firefox:                   {ec8030f7-c20a-464f-9b0e-13a3a9e97384}    (Also, Pale Moon 30+)
 * Thunderbird:               {3550f703-e582-4d05-9a08-453d09bdfdc6}    (Also, Interlink Mail & News)
 * SeaMonkey:                 {92650c4d-4b8e-4d2a-b7eb-24ecf4f6b63a}
 * Fennec (Android):          {aa3c5121-dab2-40e2-81ca-7ea25febc110}
 * Fennec (XUL):              {a23983c0-fd0e-11dc-95ff-0800200c9a66}
 * Sunbird:                   {718e30fb-e89b-41dd-9da7-e25a45638b28}
 * Instantbird:               {33cb9019-c295-46dd-be21-8c4936574bee}
 * Netscape Browser:          {3db10fab-e461-4c80-8b97-957ad5f8ea47}
 *
 * Nvu:                       {136c295a-4a5a-41cf-bf24-5cee526720d5}
 * Flock:                     {a463f10c-3994-11da-9945-000d60ca027b}
 * Kompozer:                  {20aa4150-b5f4-11de-8a39-0800200c9a66}
 * BlueGriffon:               bluegriffon@bluegriffon.com
 * Adblock Browser:           {55aba3ac-94d3-41a8-9e25-5c21fe874539}
 * Postbox:                   postbox@postbox-inc.com
 *
 * Pale Moon 25-29:           {8de7fcbb-c55c-4fbe-bfc5-fc555c87dbc4}
 * Borealis 0.9:              {a3210b97-8e8a-4737-9aa0-aa0e607640b9}
 * Ambassador (Standalone):   {4523665a-317f-4a66-9376-3763d1ad1978}    (Soft-abandoned, also, same as extension)
 * XUL Example:               example@uxp.app
 *
 * IceDove-UXP:               {3aa07e56-beb0-47a0-b0cb-c735edd25419}
 * IceApe-UXP:                {9184b6fe-4a5c-484d-8b4b-efbfccbfb514}
 */

/* ----------------------------------------------------------------------------------------------------------------- */

/* Olympia Add-on Types
 * ADDON_ANY        = 0
 * ADDON_EXTENSION  = 1
 * ADDON_THEME      = 2
 * ADDON_DICT       = 3
 * ADDON_SEARCH     = 4
 * ADDON_LPAPP      = 5   XXXTobin: This seems to be the PROPER locale type originally defined in XPInstall
 * ADDON_LPADDON    = 6   XXXTobin: What the hell is the difference between LPAPP and LPADDON?!
 * ADDON_PLUGIN     = 7
 * ADDON_API        = 8   XXXOlympia: not actually a type but used to identify extensions + themes
 *                        XXXTobin: Are these actual multipackage or on-the-fly multipackage via AMO Collections?
 * ADDON_PERSONA    = 9
 * ADDON_WEBAPP     = 11  XXXOlympia: Calling this ADDON_* is gross but we've gotta ship code.
 *                        XXXTobin: no1curr
 */

/* ----------------------------------------------------------------------------------------------------------------- */

/* Olympia Update Types
 * ADDON_EXTENSION  : 'extension',
 * ADDON_THEME      : 'theme',
 * ADDON_DICT       : 'extension',        XXXTobin: extensions.. Really?
 * ADDON_SEARCH     : 'search',           XXXTobin: We may never find out how this was intended to be handled.
 * ADDON_LPAPP      : 'item',
 * ADDON_LPADDON    : 'extension',        XXXTobin: See Olympia Add-on Types
 * ADDON_PERSONA    : 'background-theme', XXXTobin: Ditto re: search
 * ADDON_PLUGIN     : 'plugin',
 */

// --------------------------------------------------------------------------------------------------------------------

// Do not allow this to be included more than once...
if (defined('APP_UTILS')) {
  die('Application Specific Utilities: You may not include this more than once.');
}

// Define that this is a thing.
define('APP_UTILS', 1);

// --------------------------------------------------------------------------------------------------------------------

const SOFTWARE_REPO       = 'about:blank';
const DATASTORE_RELPATH   = '/datastore/';
const OBJ_RELPATH         = '/.obj/';
const COMPONENTS_RELPATH  = '/components/';
const MODULES_RELPATH     = '/modules/';
const DATABASES_RELPATH   = '/db/';
const LIB_RELPATH         = '/libs/';

// Define components
const COMPONENTS = array(
  'special'         => ROOT_PATH . BASE_RELPATH       . 'special.php',
  'site'            => ROOT_PATH . BASE_RELPATH       . 'addonsSite.php',
  'download'        => ROOT_PATH . BASE_RELPATH       . 'addonsDownload.php',
  'aus'             => ROOT_PATH . COMPONENTS_RELPATH . 'manager/amUpdate.php',
  'discover'        => ROOT_PATH . COMPONENTS_RELPATH . 'manager/amDiscoverPane.php',
  'integration'     => ROOT_PATH . COMPONENTS_RELPATH . 'manager/amIntegration.php',
  'panel'           => ROOT_PATH . COMPONENTS_RELPATH . 'panel/addonsPanel.php',
);

// Define modules
const MODULES = array(
  'account'         => ROOT_PATH . MODULES_RELPATH . 'classAccount.php',
  'aviary'          => ROOT_PATH . MODULES_RELPATH . 'classAviary.php',
  'database'        => ROOT_PATH . MODULES_RELPATH . 'classDatabase.php',
  'addonManifest'   => ROOT_PATH . MODULES_RELPATH . 'classAddonManifest.php',
  'content'         => ROOT_PATH . MODULES_RELPATH . 'classContent.php',
  'vc'              => ROOT_PATH . MODULES_RELPATH . 'nsIVersionComparator.php',
);

// Define databases
const DATABASES = array(
  'emailBlacklist'  => ROOT_PATH . DATABASES_RELPATH . 'emailBlacklist.php',
);

// Define libraries
const LIBRARIES = array(
  'rdfParser'       => ROOT_PATH . LIB_RELPATH . 'rdf_parser.php',
  'safeMySQL'       => ROOT_PATH . LIB_RELPATH . 'safemysql.class.php',
  'smarty'          => ROOT_PATH . LIB_RELPATH . 'smarty/Smarty.class.php',
);

// --------------------------------------------------------------------------------------------------------------------

const XML_API_SEARCH_BLANK  = '<searchresults total_results="0" />';
const XML_API_LIST_BLANK    = '<addons />';
const XML_API_ADDON_ERROR   = '<error>Add-on not found!</error>';
const RDF_AUS_BLANK         = '<RDF:RDF xmlns:RDF="http://www.w3.org/1999/02/22-rdf-syntax-ns#"' . SPACE .
                              'xmlns:em="http://www.mozilla.org/2004/em-rdf#" />';

// --------------------------------------------------------------------------------------------------------------------

// Define Domains for Applications
const APPLICATION_DOMAINS = ['palemoon.org' => 'palemoon', 'binaryoutcast.com' => ['borealis', 'interlink']];
const DEVELOPER_DOMAIN = 'addons-dev.palemoon.org';

// --------------------------------------------------------------------------------------------------------------------

// Define application metadata
/* Features are as follows:
 * 'e-cat', 't-cat', 'p-cat', 'unified', 'disable-xpinstall',
 * 'extensions', 'themes', 'language-packs', 'dictionaries',
 * 'search-plugins', 'personas', 'user-scripts', 'user-styles'
*/
const TARGET_APPLICATION = array(
  'toolkit' => array(
    'id'            => 'toolkit@mozilla.org',
    'bit'           => 1,
    'minVersion'    => '5.0.0a1',
    'maxVersion'    => '5.*',
    'maxOldVersion' => '4.*',
    'domain'        => 'addons.thereisonlyxul.org',
    'unified'       => false,
    'name'          => 'Goanna Runtime Environment',
    'shortName'     => 'GRE',
    'commonType'    => 'platform',
    'vendor'        => 'GRE Alliance',
    'siteTitle'     => EMPTY_STRING,
    'features'      => EMPTY_ARRAY
  ),
  'palemoon' => array(
    'id'            => '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}',
    'bit'           => 2,
    'minVersion'    => '30.0.0a1',
    'maxVersion'    => '30.*',
    'maxOldVersion' => '29.*',
    'domain'        => 'addons.palemoon.org',
    'unified'       => false,
    'name'          => 'Pale Moon',
    'shortName'     => 'Pale Moon',
    'commonType'    => 'browser',
    'vendor'        => 'Moonchild Productions',
    'siteTitle'     => 'Pale Moon - Add-ons',
    'features'      => ['extensions', 'themes', 'language-packs', 'dictionaries']
  ),
  'borealis' => array(
    'id'            => '{86c18b42-e466-4580-8b97-957ad5f8ea47}',
    'bit'           => 4,
    'minVersion'    => '8.5.7900a1',
    'maxVersion'    => '8.5.8400',
    'maxOldVersion' => '8.4.*',
    'domain'        => 'addons.binaryoutcast.com',
    'unified'       => true,
    'name'          => 'Borealis Navigator',
    'shortName'     => 'Borealis',
    'commonType'    => 'navigator',
    'vendor'        => 'Binary Outcast',
    'siteTitle'     => 'Add-ons - Binary Outcast',
    'features'      => ['extensions', 'themes', 'dictionaries', 'search-plugins']
  ),
  'interlink' => array(
    'id'            => '{3550f703-e582-4d05-9a08-453d09bdfdc6}',
    'bit'           => 8,
    'minVersion'    => '52.9.7900a1',
    'maxVersion'    => '52.9.8400',
    'maxOldVersion' => '52.9.7899', /* Basically irrelevant for non-web clients */
    'domain'        => 'addons.binaryoutcast.com',
    'unified'       => true,
    'name'          => 'Interlink Mail &amp; News',
    'shortName'     => 'Interlink',
    'commonType'    => 'client',
    'vendor'        => 'Binary Outcast',
    'siteTitle'     => 'Add-ons - Binary Outcast',
    'features'      => ['disable-xpinstall', 'extensions', 'themes', 'dictionaries', 'search-plugins']
  ),
);

const PALEMOON_GUID = '{8de7fcbb-c55c-4fbe-bfc5-fc555c87dbc4}';

// --------------------------------------------------------------------------------------------------------------------

// User Levels are not bit-wise so they correspond with the following indexed array order
const USER_LEVELS         = ['unregistered', 'banned', 'user', 'developer',
                             'moderator', 'administrator'];
const USER_LEVELS_DISPLAY = ['Unknown', 'Non-entity', 'Regular User', 'Add-on Developer',
                             'Add-ons Team', 'Phobos Overlord'];

// --------------------------------------------------------------------------------------------------------------------

const XPINSTALL_TYPES = array(
  'app'               => 1,     // No longer applicable
  'extension'         => 2,
  'theme'             => 4,
  'locale'            => 8,
  'plugin'            => 16,    // No longer applicable
  'multipackage'      => 32,    // Forbidden on Phobos
  'dictionary'        => 64,
  'experiment'        => 128,   // No longer applicable
  'apiextension'      => 256,   // No longer applicable
  'external'          => 512,   // Phobos only
  'persona'           => 1024,  // Phobos only
  'search-plugin'     => 2048,  // Phobos only
  'user-script'       => 4096,  // Phobos only
  'user-style'        => 8192,  // Phobos only
);

// These are the supported "real" XPInstall types
const VALID_XPI_TYPES   = XPINSTALL_TYPES['extension'] | XPINSTALL_TYPES['theme'] |
                          XPINSTALL_TYPES['locale'] | XPINSTALL_TYPES['dictionary'];

// These are add-on types only Phobos understands. They are NOT installable in the application directly
// We will treat them as any other xpi but deliver them to the client in different ways
const EXTRA_XPI_TYPES   = XPINSTALL_TYPES['persona'] | XPINSTALL_TYPES['search-plugin'] |
                          XPINSTALL_TYPES['user-script'] | XPINSTALL_TYPES['user-style'];

// These are unsupported "real" XPInstall types (plus external because it is completely virtual)
const INVALID_XPI_TYPES = XPINSTALL_TYPES['app'] | XPINSTALL_TYPES['plugin'] | XPINSTALL_TYPES['multipackage'] |
                          XPINSTALL_TYPES['experiment'] | XPINSTALL_TYPES['apiextension'] | XPINSTALL_TYPES['external'];

// Originally XPInstall only needed to a handful of types since it was killed much refactoring and Olympia reused
// older types. We are gonna match that for now even if they aren't actually implemented. 
const AUS_XPI_TYPES = array(
  XPINSTALL_TYPES['extension']      => 'extension',
  XPINSTALL_TYPES['theme']          => 'theme',
  XPINSTALL_TYPES['dictionary']     => 'extension',
  XPINSTALL_TYPES['search-plugin']  => 'search',
  XPINSTALL_TYPES['locale']         => 'item',
  XPINSTALL_TYPES['persona']        => 'background-theme',
);

// Add-ons Manager Search uses the Olympia types so map the XPInstall Types to Olympia which match the Add-ons Manager
const SEARCH_XPI_TYPES = array(
  XPINSTALL_TYPES['extension']      => 1,
  XPINSTALL_TYPES['theme']          => 2,
  XPINSTALL_TYPES['dictionary']     => 3,
  XPINSTALL_TYPES['search-plugin']  => 4,
  XPINSTALL_TYPES['locale']         => 5,
  XPINSTALL_TYPES['persona']        => 9,
);

// --------------------------------------------------------------------------------------------------------------------

const MANIFEST_FILES = array(
  'xpinstall'         => 'install.js',
  'rdfinstall'        => RDF_INSTALL_MANIFEST,
  'jsoninstall'       => JSON_INSTALL_MANIFEST,
  'chrome'            => 'chrome.manifest',
  'bootstrap'         => 'bootstrap.js',
  'cfxJetpack'        => 'harness-options.json',
  'npmJetpack'        => 'package.json',
  'webex'             => 'manifest.json',
);

// --------------------------------------------------------------------------------------------------------------------

// Define the specific technology that Add-ons can have
const ADDON_TECHNOLOGY = ['overlay' => 1, 'xpcom' => 2, 'bootstrap' => 4, 'jetpack' => 8];

// These ID fragments are NOT allowed anywhere in an Add-on ID unless you are a member of the Add-ons Team or higher
const RESTRICTED_IDS  = array(
  'bfc5-fc555c87dbc4',  // Moonchild Productions
  '9376-3763d1ad1978',  // Pseudo-Static
  '9aa0-aa0e607640b9',  // Binary Outcast
  'moonchild',          // Moonchild Productions
  'palemoon',           // Moonchild Productions
  'basilisk',           // Moonchild Productions
  'binaryoutcast',      // Binary Outcast
  'mattatobin',         // Binary Outcast
  'thereisonlyxul',
  'mozilla.org',
  'lootyhoof',          // Ryan
  'srazzano',           // BANNED FOR LIFE
  'justoff',            // BANNED FOR LIFE
);

// --------------------------------------------------------------------------------------------------------------------

const SECTIONS = array(
  'extensions'      => array('type'        => XPINSTALL_TYPES['extension'],
                             'name'        => 'Extensions',
                             'description' =>
                               'Extensions are small add-ons that add new functionality to {%APPLICATION_SHORTNAME},' . SPACE .
                               'from a simple toolbar button to a completely new feature.' . SPACE .
                               'They allow you to customize the {%APPLICATION_COMMONTYPE} to fit your own needs' . SPACE .
                               'and preferences, while keeping the core itself light and lean.'
                            ),
  'themes'          => array('type'        => XPINSTALL_TYPES['theme'],
                             'name'        => 'Themes',
                             'description' =>
                               'Themes allow you to change the look and feel of the user interface' . SPACE .
                               'and personalize it to your tastes.' . SPACE .
                               'A theme can simply change the colors of the UI or it can change every aspect of its appearance.'
                            ),
  'language-packs'  => array('type'        => XPINSTALL_TYPES['locale'],
                             'name'        => 'Language Packs',
                             'description' => 'These add-ons provide strings for the user interface in your local language.'
                            ),
  'dictionaries'    => array('type'        => XPINSTALL_TYPES['dictionary'],
                             'name'        => 'Dictionaries',
                             'description' =>
                               '{%APPLICATION_SHORTNAME} has spell checking features, with this type of add-on' . SPACE .
                               'you can add check the spelling in additional languages.'
                            ),
  'personas'        => array('type'        => XPINSTALL_TYPES['persona'],
                             'name'        => 'Personas',
                             'description' => 'Lightweight themes which allow you personalize {%APPLICATION_SHORTNAME} further.'
                            ),
  'search-plugins'  => array('type'        => XPINSTALL_TYPES['search-plugin'],
                             'name'        => 'Search Plugins',
                             'description' =>
                               'A search plugin provides the ability to access a search engine from a web browser,' . SPACE .
                               'without having to go to the engine\'s website first.<br />' .
                               'Technically, a search plugin is a small Extensible Markup Language file that tells' . SPACE .
                               'the browser what information to send to a search engine and how the results are to be retrieved. '
                            ),
  'user-scripts'    => ['type' => XPINSTALL_TYPES['user-script'], 'name' => 'User Scripts', 'description' => null],
  'user-styles'     => ['type' => XPINSTALL_TYPES['user-style'], 'name' => 'User Styles', 'description' => null],
);

// --------------------------------------------------------------------------------------------------------------------

const CATEGORIES = array(
  'unlisted'                  => ['bit' => 0,         'name' => 'Unlisted', 'type' => 0],
  'alerts-and-updates'        => ['bit' => 1,         'name' => 'Alerts &amp; Updates',
                                  'type' => XPINSTALL_TYPES['extension']],
  'appearance'                => ['bit' => 2,         'name' => 'Appearance',
                                  'type' => XPINSTALL_TYPES['extension']],
  'bookmarks-and-tabs'        => ['bit' => 4,         'name' => 'Bookmarks &amp; Tabs',
                                  'type' => XPINSTALL_TYPES['extension']],
  'download-management'       => ['bit' => 8,         'name' => 'Download Management',
                                  'type' => XPINSTALL_TYPES['extension'],],
  'feeds-news-and-blogging'   => ['bit' => 16,        'name' => 'Feeds, News, &amp; Blogging',
                                  'type' => XPINSTALL_TYPES['extension'],],
  'privacy-and-security'      => ['bit' => 32,        'name' => 'Privacy &amp; Security',
                                  'type' => XPINSTALL_TYPES['extension']],
  'search-tools'              => ['bit' => 64,        'name' => 'Search Tools',
                                  'type' => XPINSTALL_TYPES['extension']],
  'social-and-communication'  => ['bit' => 128,       'name' => 'Social &amp; Communication',
                                  'type' => XPINSTALL_TYPES['extension']],
  'tools-and-utilities'       => ['bit' => 256,       'name' => 'Tools &amp; Utilities',
                                  'type' => XPINSTALL_TYPES['extension']],
  'web-development'           => ['bit' => 512,       'name' => 'Web Development', 
                                  'type' => XPINSTALL_TYPES['extension']],
  'abstract'                  => ['bit' => 1024,      'name' => 'Abstract',
                                  'type' => XPINSTALL_TYPES['persona']],
  'brands'                    => ['bit' => 4096,      'name' => 'Brands',
                                  'type' => XPINSTALL_TYPES['persona']],
  'compact'                   => ['bit' => 8192,      'name' => 'Compact',
                                  'type' => XPINSTALL_TYPES['theme']],
  'dark'                      => ['bit' => 16384,     'name' => 'Dark',
                                  'type' => XPINSTALL_TYPES['theme'] | XPINSTALL_TYPES['persona']],
  'large'                     => ['bit' => 32768,     'name' => 'Large',
                                  'type' => XPINSTALL_TYPES['theme']],
  'modern'                    => ['bit' => 65536,     'name' => 'Modern',
                                  'type' => XPINSTALL_TYPES['theme']],
  'music'                     => ['bit' => 131072,    'name' => 'Music',
                                  'type' => XPINSTALL_TYPES['persona']],
  'nature'                    => ['bit' => 262144,    'name' => 'nature',
                                  'type' => XPINSTALL_TYPES['persona']],
  'other-web-clients'         => ['bit' => 524288,    'name' => 'Browsers, Explorers, &amp; Navigators',
                                  'type' => XPINSTALL_TYPES['theme']],
  'retro'                     => ['bit' => 1048576,   'name' => 'Retro &amp; Classic',
                                  'type' => XPINSTALL_TYPES['theme'] | XPINSTALL_TYPES['persona']],
  'os-integration'            => ['bit' => 2097152,   'name' => 'OS Integration',
                                  'type' => XPINSTALL_TYPES['theme']],
  'scenery'                   => ['bit' => 4194304,   'name' => 'Scenery',
                                  'type' => XPINSTALL_TYPES['persona']],
  'seasonal'                  => ['bit' => 8388608,   'name' => 'Seasonal',
                                  'type' => XPINSTALL_TYPES['persona']],
  'other'                     => ['bit' => 16777216,  'name' => 'Other',
                                  'type' => XPINSTALL_TYPES['extension'] | XPINSTALL_TYPES['theme'] | XPINSTALL_TYPES['persona']],
);

// --------------------------------------------------------------------------------------------------------------------

// Open Source Licenses users can set for their Add-ons
const LICENSES = array(
  'Apache-2.0'                => 'Apache License 2.0',
  'Apache-1.1'                => 'Apache License 1.1',
  'BSD-3-Clause'              => 'BSD 3-Clause',
  'BSD-2-Clause'              => 'BSD 2-Clause',
  'GPL-3.0'                   => 'GNU General Public License 3.0',
  'GPL-2.0'                   => 'GNU General Public License 2.0',
  'LGPL-3.0'                  => 'GNU Lesser General Public License 3.0',
  'LGPL-2.1'                  => 'GNU Lesser General Public License 2.1',
  'AGPL-3.0'                  => 'GNU Affero General Public License v3',
  'MIT'                       => 'MIT License',
  'MPL-2.0'                   => 'Mozilla Public License 2.0',
  'MPL-1.1'                   => 'Mozilla Public License 1.1',
  'PD'                        => 'Public Domain',
  'COPYRIGHT'                 => '&copy;',
  'Custom'                    => 'Custom License',
);

// ====================================================================================================================

// == | Global Functions | ============================================================================================

/**********************************************************************************************************************
* Basic Content Generation using the Special Component's Template
*
* @dep SOFTWARE_NAME
* @dep SOFTWARE_VERSION
* @dep gfError()
* @param $aTtitle     Title of the page
* @param $aContent    Content of the page
* @param $aTextBox    Use textbox for content
* @param $aList       Use list for content
* @param $aError      Is an Error Page
***********************************************************************************************************************/
function gfContent($aMetadata, $aLegacyContent = null, $aTextBox = null, $aList = null, $aError = null) {
  $ePrefix = __FUNCTION__ . DASH_SEPARATOR;
  $skinPath = '/skin/default';

  // Anonymous functions
  $contentIsStringish = function($aContent) {
    return (is_string($aContent) || is_int($aContent)); 
  };

  $textboxContent = function($aContent) {
    return '<textarea class="special-textbox aligncenter" name="content" rows="36" readonly>' .
           $aContent . '</textarea>';
  };

  $maybePTagContent = function($aContent) {
    if (!str_starts_with($aContent, '<p') && !str_starts_with($aContent, '<ul') &&
        !str_starts_with($aContent, '<h1') && !str_starts_with($aContent, '<h2') &&
        !str_starts_with($aContent, '<table')) {
      $aContent = '<p>' . $aContent . '</p>';
    }

    return $aContent;
  };

  $template = gfReadFile(DOT . $skinPath . SLASH . 'template.xhtml');

  if (!$template) {
    gfError($ePrefix . 'Special Template is busted...', null, true);
  }

  $pageSubsts = array(
    '{$SKIN_PATH}'        => $skinPath,
    '{$SITE_NAME}'        => defined('SITE_NAME') ? SITE_NAME : SOFTWARE_NAME . SPACE . SOFTWARE_VERSION,
    '{$SITE_MENU}'        => EMPTY_STRING,
    '{$PAGE_TITLE}'       => null,
    '{$PAGE_CONTENT}'     => null,
    '{$SOFTWARE_NAME}'    => SOFTWARE_NAME,
    '{$SOFTWARE_VERSION}' => SOFTWARE_VERSION,
  );

  if (is_string($aMetadata) || $aMetadata == null) {
    if (is_array($aMetadata)) {
      gfError($ePrefix . 'aMetadata may not be an array in legacy mode.');
    }

    if ($aTextBox && $aList) {
      gfError($ePrefix . 'You cannot use both textbox and list');
    }

    if (!$contentIsStringish($aLegacyContent) || in_array($aMetadata, ['jsonEncode', 'phpEncode'])) {
      if ($aMetadata == 'phpEncode') {
        $aLegacyContent = var_export($aLegacyContent, true);
      }
      else {
        $aLegacyContent = json_encode($aLegacyContent, JSON_ENCODE_FLAGS);
      }

      $aTextBox = true;
      $aList = false;
    }

    if ($aTextBox) {
      $aLegacyContent = $textboxContent($aLegacyContent);
    }
    elseif ($aList) {
      // We are using an unordered list so put aLegacyContent in there
      $aLegacyContent = '<ul><li>' . $aLegacyContent . '</li><ul>';
    }
    else {
      $aLegacyContent = $maybePTagContent($aLegacyContent);
    }
    

    if (!$aError && ($GLOBALS['gaRuntime']['qTestCase'] ?? null)) {
      $pageSubsts['{$PAGE_TITLE}'] = 'Test Case' . DASH_SEPARATOR . $GLOBALS['gaRuntime']['qTestCase'];

      foreach ($GLOBALS['gaRuntime']['siteMenu'] ?? EMPTY_ARRAY as $_key => $_value) {
        $pageSubsts['{$SITE_MENU}'] .= '<li><a href="' . $_key . '">' . $_value . '</a></li>';
      }
    }
    else {
      $pageSubsts['{$PAGE_TITLE}'] = $aMetadata;
    }

    $pageSubsts['{$PAGE_CONTENT}'] = $aLegacyContent;
  }
  else {
    if ($aTextBox || $aList) {
      gfError($ePrefix . 'Mode attributes are deprecated.');
    }

    if (!array_key_exists('title', $aMetadata) && !array_key_exists('content', $aMetadata)) {
      gfError($ePrefix . 'You must specify a title and content');
    }

    $pageSubsts['{$PAGE_TITLE}'] = $aMetadata['title'];

    if (!$contentIsStringish($aMetadata['content']) || in_array($aMetadata, ['jsonEncode', 'phpEncode'])) {
      if ($aMetadata['phpEncode'] ?? null) {
        $pageSubsts['{$PAGE_CONTENT}'] = $textboxContent(var_export($aMetadata['content'], true));
      }
      else {
        $pageSubsts['{$PAGE_CONTENT}'] = $textboxContent(json_encode($aMetadata['content'], JSON_ENCODE_FLAGS));
      }
    }
    else {
      $pageSubsts['{$PAGE_CONTENT}'] = $maybePTagContent($aMetadata['content']);
    }

    foreach ($aMetadata['menu'] ?? EMPTY_ARRAY as $_key => $_value) {
      $pageSubsts['{$SITE_MENU}'] .= '<li><a href="' . $_key . '">' . $_value . '</a></li>';
    }
  }

  if ($pageSubsts['{$SITE_MENU}'] == EMPTY_STRING) {
    $pageSubsts['{$SITE_MENU}'] = '<li><a href="/">Home</a></li>';
  }

  $template = gfSubst('string', $pageSubsts, $template);

  // If we are generating an error from gfError we want to clean the output buffer
  if ($aError) {
    ob_get_clean();
  }

  // Send an html header
  gfHeader('html');

  // write out the everything
  print($template);

  // We're done here
  exit();
}

/**********************************************************************************************************************
* Checks for old versions
*
* @param $aFeature    feature
* @param $aReturn     if true we will return a value else 404
***********************************************************************************************************************/
function gfValidClientVersion($aCheckVersion = null, $aVersion = null) {
  global $gaRuntime;

  $currentApplication = $gaRuntime['currentApplication'];

  // No user agent is a blatantly bullshit state
  if (!$gaRuntime['userAgent']) {
    gfError('Reference Code - ID-10-T');
  }

  // Knock the UA to lowercase so it is easier to deal with
  $userAgent = strtolower($gaRuntime['userAgent']);

  // Check for invalid clients
  foreach (['curl/', 'wget/', 'git/'] as $_value) {
    if (str_contains($userAgent, $_value)) {
      gfError('Reference Code - ID-10-T');
    }
  }

  // This function doesn't work in unifiedMode when the application hasn't been determined yet.
  if ($gaRuntime['unifiedMode'] && is_bool($currentApplication)) {
    return true;
  }

  // ------------------------------------------------------------------------------------------------------------------

  // This is our basic client ua check.
  if (!$aCheckVersion) {
    $oldAndInsecureHackJobs = ['nt 5', 'nt 6.0', 'bsd', 'intel', 'ppc', 'mac', 'iphone', 'ipad', 'ipod', 'android',
                               'goanna/3.5', 'goanna/4.0', 'rv:3.5', 'rv:52.9', 'basilisk/', '55.0', 'mypal/',
                               'centaury/', 'bnavigator/'];

    // Check for old and insecure Windows versions and enemy hackjobs
    foreach ($oldAndInsecureHackJobs as $_value) {
      if (str_contains($userAgent, $_value)) {
        return false;
      }
    }

    // Check if the application slice matches the current site.
    if (!str_contains($userAgent, $currentApplication)) {
      return false;
    }

    return true;
  }

  // ------------------------------------------------------------------------------------------------------------------

  // This is the main meat of this function. To detect old and insecure application versions
  // Try to find the position of the application slice in the UA
  $uaVersion = strpos($userAgent, $currentApplication . SLASH);

  // Make sure we have a position for the application slice
  // If we don't then it ain't gonna match the current add-ons site
  if ($uaVersion === false) {
    return false;
  }

  // Extract the application slice by slicing off everything before it
  // UXP Applications ALWAYS have the application slice at the end of the UA
  $uaVersion = substr($userAgent, $uaVersion, $uaVersion);

  // Extract the application version
  $uaVersion = str_replace($currentApplication . SLASH, EMPTY_STRING, $uaVersion);

  // Make sure we actually have a string
  if (!gfSuperVar('var', $uaVersion)) {
    return false;
  }

  // Set currentVersion to the supplied version else the extracted version from the ua
  $currentVersion = $aVersion ?? $uaVersion;

  // ------------------------------------------------------------------------------------------------------------------

  // Set the old version to compare against 
  $maxOldVersion = TARGET_APPLICATION[$currentApplication]['maxOldVersion'];

  // If we are supplying the version number to check make sure it actually matches the UA.
  if ($aVersion && ($currentVersion != $uaVersion)) {
    return false;
  }

  // NOW we can compare it against the old version.. Finally.
  if (ToolkitVersionComparator::compare($currentVersion, $maxOldVersion) <= 0) {
    return false;
  }

  // Welp, seems it is newer than the currently stated old version so pass
  return true;
}

/**********************************************************************************************************************
* TBD
***********************************************************************************************************************/
function gfGetAppDomainByID($aAppID) {
  global $gaRuntime;
  $targetApplication = array_combine(array_column(TARGET_APPLICATION, 'id'),
                                     array_column(TARGET_APPLICATION, 'domain'));

  return $targetApplication[$aAppID] ?? $gaRuntime['currentSubdomain'] . DOT . $gaRuntime['currentDomain'];
}

/**********************************************************************************************************************
* TBD
***********************************************************************************************************************/
function gfGetAppDomainByName($aAppName) {
  global $gaRuntime;
  $targetApplication = array_combine(array_keys(TARGET_APPLICATION),
                                     array_column(TARGET_APPLICATION, 'domain'));

  return $targetApplication[$aAppName] ?? $gaRuntime['currentSubdomain'] . DOT . $gaRuntime['currentDomain'];
}

/**********************************************************************************************************************
* TBD
***********************************************************************************************************************/
function gfGetAppNameByID($aAppID) {
  global $gaRuntime;
  $targetApplication = array_combine(array_column(TARGET_APPLICATION, 'id'),
                                     array_keys(TARGET_APPLICATION));

  return $targetApplication[$aAppID] ?? $gaRuntime['currentApplication'];
}

/**********************************************************************************************************************
* Get the bitwise value of valid applications from a list of application ids
*
* @param $aTargetApplications   list of targetApplication ids
* @returns                      bitwise int value representing applications
***********************************************************************************************************************/
function gfGetClientBits($aTargetApplications) {
  if (!is_array($aTargetApplications)) {
    gfError(__FUNCTION__ . ': You must supply an array of ids');
  }

  if (!array_is_list($aTargetApplications)) {
    $aTargetApplications = array_keys($aTargetApplications);
  }

  $applications = array_combine(array_column(TARGET_APPLICATION, 'id'), array_column(TARGET_APPLICATION, 'bit'));
  $applicationBits = 0;

  foreach ($applications as $_key => $_value) {
    if (in_array($_key, $aTargetApplications)) {
      $applicationBits |= $_value;
    }
  }

  return $applicationBits;
}

/**********************************************************************************************************************
* Check if the application has the supplied feature
***********************************************************************************************************************/
function gfCheckFeature($aFeature, $aReturn = null) {
  global $gaRuntime;

  if (is_bool($gaRuntime['currentApplication'])) {
    gfError(__FUNCTION__ . ': Unable to determine the application features.');
  }
  
  if (!in_array($aFeature, TARGET_APPLICATION[$gaRuntime['currentApplication']]['features'])) {
    if (!$aReturn) {
      gfErrorOr404('Feature' . SPACE . $aFeature . SPACE . 'is not enabled for' . SPACE .
                   $gaRuntime['currentApplication']);
    }
    return false;
  }

  return true;
}

/**********************************************************************************************************************
* Get categories for a specific XPINSTALL type
***********************************************************************************************************************/
function gfGetCategoriesByType($aType) {
  return gfSuperVar('check', array_filter(CATEGORIES, function($aCat) use($aType) { return $aCat['type'] &= $aType; }));
}

// ====================================================================================================================

?>