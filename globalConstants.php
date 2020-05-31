<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// This has to be defined using the function at runtime because it is based
// on a variable. However, constants defined with the language construct
// can use this constant by some strange voodoo. Keep an eye on this.
// NOTE: DOCUMENT_ROOT does NOT have a trailing slash.
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);

// Define basic constants for the software
const SOFTWARE_NAME         = 'Phoebus Ascendant';
const SOFTWARE_VERSION      = '3.0.0a1';
const DATASTORE_RELPATH     = '/datastore/';
const OBJ_RELPATH           = '/.obj/';
const BASE_RELPATH          = '/base/';
const COMPONENTS_RELPATH    = '/components/';
const DATABASES_RELPATH     = '/databases/';
const MODULES_RELPATH       = '/modules/';
const LIB_RELPATH           = '/libraries/';

const NEW_LINE              = "\n";

// --------------------------------------------------------------------------------------------------------------------

// Define components
const COMPONENTS = array(
  'site'            => ROOT_PATH . BASE_RELPATH . 'site.php',
  'special'         => ROOT_PATH . BASE_RELPATH . 'special.php',
  'aus'             => ROOT_PATH . COMPONENTS_RELPATH . 'aus/addonUpdateService.php',
  'discover'        => ROOT_PATH . COMPONENTS_RELPATH . 'api/discoverPane.php',
  'download'        => ROOT_PATH . COMPONENTS_RELPATH . 'download/addonDownload.php',
  'integration'     => ROOT_PATH . COMPONENTS_RELPATH . 'api/amIntegration.php',
  'panel'           => ROOT_PATH . COMPONENTS_RELPATH . 'panel/phoebusPanel.php',
);

// Define modules
const MODULES = array(
  'account'         => ROOT_PATH . MODULES_RELPATH . 'classAccount.php',
  'database'        => ROOT_PATH . MODULES_RELPATH . 'classDatabase.php',
  'generateContent' => ROOT_PATH . MODULES_RELPATH . 'classGenerateContent.php',
  'mozillaRDF'      => ROOT_PATH . MODULES_RELPATH . 'classMozillaRDF.php',
  'vc'              => ROOT_PATH . MODULES_RELPATH . 'nsIVersionComparator.php',
);

// Define databases
const DATABASES = array(
  'emailBlacklist'  => ROOT_PATH . DATABASES_RELPATH . 'emailBlacklist.php',
  'searchPlugins'   => ROOT_PATH . DATABASES_RELPATH . 'searchPlugins.php',
);

// Define libraries
const LIBRARIES = array(
  'smarty'          => ROOT_PATH . LIB_RELPATH . 'smarty/libs/Smarty.class.php',
  'safeMySQL'       => ROOT_PATH . LIB_RELPATH . 'safemysql/safemysql.class.php',
  'rdfParser'       => ROOT_PATH . LIB_RELPATH . 'librdf/rdf_parser.php',
);

// --------------------------------------------------------------------------------------------------------------------

const DEVELOPER_DOMAIN = 'addons-dev.palemoon.org';

// Define Domains for Applications
const APPLICATION_DOMAINS = array(
  'addons.palemoon.org'           => 'palemoon',
  'addons-dev.palemoon.org'       => 'palemoon',
  'addons.basilisk-browser.org'   => 'basilisk',
  'addons.binaryoutcast.com'      => ['borealis', 'interlink'],
);

// --------------------------------------------------------------------------------------------------------------------

/* Known Application IDs
 * Application IDs are normally in the form of a {GUID} or user@host ID.
 *
 * Firefox:          {ec8030f7-c20a-464f-9b0e-13a3a9e97384}
 * Thunderbird:      {3550f703-e582-4d05-9a08-453d09bdfdc6}
 * SeaMonkey:        {92650c4d-4b8e-4d2a-b7eb-24ecf4f6b63a}
 * Fennec (Android): {aa3c5121-dab2-40e2-81ca-7ea25febc110}
 * Fennec (XUL):     {a23983c0-fd0e-11dc-95ff-0800200c9a66}
 * Sunbird:          {718e30fb-e89b-41dd-9da7-e25a45638b28}
 * Instantbird:      {33cb9019-c295-46dd-be21-8c4936574bee}
 * Adblock Browser:  {55aba3ac-94d3-41a8-9e25-5c21fe874539} */

const TOOLKIT_ID    = 'toolkit@mozilla.org';
const TOOLKIT_ALTID = 'toolkit@palemoon.org';
const TOOLKIT_BIT   = 1;

// --------------------------------------------------------------------------------------------------------------------

// Define application metadata
const TARGET_APPLICATION = array(
  'palemoon' => array(
    'id'            => '{8de7fcbb-c55c-4fbe-bfc5-fc555c87dbc4}',
    'bit'           => 2,
    'name'          => 'Pale Moon',
    'shortName'     => null,
    'commonType'    => 'browser',
    'siteTitle'     => 'Pale Moon - Add-ons',
    'features'      => ['https', 'extensions', 'extensions-cat', 'themes',
                        'personas', 'language-packs', 'search-plugins']
  ),
  'basilisk' => array(
    'id'            => '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}',
    'bit'           => 4,
    'name'          => 'Basilisk',
    'shortName'     => null,
    'commonType'    => 'browser',
    'siteTitle'     => 'Basilisk: add-ons',
    'features'      => ['https', 'extensions', 'themes', 'personas', 'search-plugins']
  ),
  'borealis' => array(
    'id'            => '{a3210b97-8e8a-4737-9aa0-aa0e607640b9}',
    'bit'           => 8,
    'name'          => 'Borealis Navigator',
    'shortName'     => 'Borealis',
    'commonType'    => 'navigator',
    'siteTitle'     => 'Add-ons - Binary Outcast',
    'features'      => ['unified', 'extensions', 'search-plugins']
  ),
  'interlink' => array(
    'id'            => '{3550f703-e582-4d05-9a08-453d09bdfdc6}',
    'bit'           => 16,
    'name'          => 'Interlink Mail &amp; News',
    'shortName'     => 'Interlink',
    'commonType'    => 'client',
    'siteTitle'     => 'Add-ons - Binary Outcast',
    'features'      => ['unified', 'extensions', 'themes', 'search-plugins', 'disable-xpinstall']
  ),
  'ambassador' => array(
    'id'            => '{4523665a-317f-4a66-9376-3763d1ad1978}',
    'bit'           => 32,
    'name'          => 'Ambassador',
    'shortName'     => null,
    'commonType'    => 'client',
    'siteTitle'     => 'Add-ons - Ambassador',
    'features'      => ['extensions', 'themes', 'disable-xpinstall']
  ),
);

// --------------------------------------------------------------------------------------------------------------------

const XML_TAG               = '<?xml version="1.0" encoding="utf-8" ?>';
const XML_API_SEARCH_BLANK  = '<searchresults total_results="0" />';
const XML_API_LIST_BLANK    = '<addons />';
const XML_API_ADDON_ERROR   = '<error>Add-on not found!</error>';
const RDF_AUS_BLANK         = '<RDF:RDF xmlns:RDF="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:em="http://www.mozilla.org/2004/em-rdf#" />';

// --------------------------------------------------------------------------------------------------------------------

const MANIFEST_FILES = array(
  'install'           => 'install.rdf',
  'chrome'            => 'chrome.manifest',
  'bootstrap'         => 'bootstrap.js',
  'npmJetpack'        => 'package.json',
  'cfxJetpack'        => 'harness-options.json',
  'webex'             => 'manifest.jsom',
);

const XPINSTALL_TYPES = array(
  'app'               => 1, // No longer applicable
  'extension'         => 2,
  'theme'             => 4,
  'locale'            => 8,
  'plugin'            => 16, // No longer applicable
  'multipackage'      => 32, // Forbidden on Phoebus
  'dictionary'        => 64,
  'experiment'        => 128, // Not used in UXP
  'apiextension'      => 256, // Not used in UXP
  'external'          => 512, // Phoebus only
  'persona'           => 1024, // Phoebus only
  'search-plugin'     => 2048, // Phoebus only
);

const EXTENSION_TECHNOLOGY = array(
  'overlay'           => 1,
  'xpcom'             => 2,
  'bootstrap'         => 4,
  'jetpack'           => 8,
);

const INVALID_XPI_TYPES   = 1 | 16 | 32 | 128 | 256 | 512 | 1024 | 2048;
const AUS_XPI_TYPES       = [2 => 'extension', 4 => 'theme', 8 => 'item', 64 => 'item'];

const REGEX_GUID          = '/^\{[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\}$/i';
const REGEX_HOST          = '/[a-z0-9-\._]+\@[a-z0-9-\._]+/i';

const RESTRICTED_IDS  = array(
  'bfc5-fc555c87dbc4',  // Moonchild Productions
  '9376-3763d1ad1978',  // Pseudo-Static
  'b98e-98e62085837f',  // Ryan
  '9aa0-aa0e607640b9',  // Binary Outcast
  'moonchild',          // Moonchild Productions
  'palemoon',           // Moonchild Productions
  'basilisk',           // Moonchild Productions
  'thereisonlyxul',
  'binaryoutcast',      // Binary Outcast
  'mattatobin',         // Binary Outcast
  'mozilla.org',
  'lootyhoof',          // Ryan
  'srazzano'            // BANNED FOR LIFE
);

// --------------------------------------------------------------------------------------------------------------------

const EXTENSION_CATEGORY       = ['name' => 'Extensions',                   'type' => 2, 'bit' => 0];

const CATEGORIES = array(
  'alerts-and-updates'        => ['name' => 'Alerts &amp; Updates',         'type' => 2,    'bit' => 1],
  'appearance'                => ['name' => 'Appearance',                   'type' => 2,    'bit' => 2],
  'bookmarks-and-tabs'        => ['name' => 'Bookmarks &amp; Tabs',         'type' => 2,    'bit' => 4],
  'download-management'       => ['name' => 'Download Management',          'type' => 2,    'bit' => 8],
  'feeds-news-and-blogging'   => ['name' => 'Feeds, News, &amp; Blogging',  'type' => 2,    'bit' => 16],
  'privacy-and-security'      => ['name' => 'Privacy &amp; Security',       'type' => 2,    'bit' => 32],
  'search-tools'              => ['name' => 'Search Tools',                 'type' => 2,    'bit' => 64],
  'social-and-communication'  => ['name' => 'Social &amp; Communication',   'type' => 2,    'bit' => 128],
  'tools-and-utilities'       => ['name' => 'Tools &amp; Utilities',        'type' => 2,    'bit' => 256],
  'web-development'           => ['name' => 'Web Development',              'type' => 2,    'bit' => 512],
  'other'                     => ['name' => 'Other',                        'type' => 2,    'bit' => 1024],
  'themes'                    => ['name' => 'Themes',                       'type' => 4,    'bit' => 0],
  'language-packs'            => ['name' => 'Language Packs',               'type' => 8,    'bit' => 0],
  'dictionaries'              => ['name' => 'Dictionaries',                 'type' => 64,   'bit' => 0],
  'personas'                  => ['name' => 'Personas',                     'type' => 512,  'bit' => 0],
  'search-plugins'            => ['name' => 'Search Plugins',               'type' => 1024, 'bit' => 0],
);

// --------------------------------------------------------------------------------------------------------------------

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
  'Custom'                    => 'Custom License',
  'PD'                        => 'Public Domain',
  'COPYRIGHT'                 => ''
);

?>