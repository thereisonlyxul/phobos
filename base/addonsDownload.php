<?php
// == | Setup | =======================================================================================================

gfImportModules('database', 'addonManifest');

$gaRuntime['qAddonID'] = gfSuperVar('get', 'id');
$gaRuntime['qVersion'] = gfSuperVar('get', 'version') ?? 'latest';

// ====================================================================================================================

// == | Main | ========================================================================================================

// Sanity
if ($gaRuntime['qAddonID'] == null) {
  gfErrorOr404('Missing minimum required arguments.');
}

if (!$gaRuntime['validClient']){
  gfErrorOr404('Client check failed.');
}

if (!$gaRuntime['validVersion']) {
  gfErrorOr404('Version check failed.');
}

$addon = $gmAddonManifest->getOneByID($gaRuntime['qAddonID']);

if (!$addon) {
  gfErrorOr404('Add-on not found.');
}
$slug = strtolower($addon['addon']['slug']);
$xpiFile = gfBuildPath(ROOT_PATH, DATASTORE_RELPATH, 'addons', $slug, $addon['xpinstall']['filename']);

if (!file_exists($xpiFile)) {
  gfErrorOr404('XPI File does not exist.');
}

$addon['xpinstall']['size'] = filesize($xpiFile);

gfHeader('xpi');
header("Content-Disposition: inline; filename=\"{$slug}-{$addon['xpinstall']['installManifest']['version']}.xpi\"");
header("Content-Length:" . SPACE . $addon['xpinstall']['size']);
header("Cache-Control: no-cache");
header("X-Accel-Redirect:" . SPACE . gfStripRootPath($xpiFile));


// We're done here
exit();

// ====================================================================================================================

?>