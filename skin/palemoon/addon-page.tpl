{if $PAGE_DATA.hasIcon == true}
<img src="/datastore/addons/{$PAGE_DATA.slug|lower}/icon.png" style="height: 48px; width: 48px; margin-top: 16px;" class="alignright">
{else}
  {if $PAGE_DATA.type == 4}
    {assign "ICON_TYPE" "theme"}
  {elseif $PAGE_DATA.type == 8}
    {assign "ICON_TYPE" "locale"}
  {elseif $PAGE_DATA.type == 64}
    {assign "ICON_TYPE" "dictionary"}
  {else}
    {assign "ICON_TYPE" "extension"}
  {/if}
<img src="/skin/shared/icons/types/{$ICON_TYPE}64.png" style="height: 48px; width: 48px; margin-top: 16px;" class="alignright">
{/if}
<h1>
  {$PAGE_DATA.name}
</h1>

<p style="margin-top: -12px">
  By: {if $PAGE_DATA.ownerUsername == 'mattatobin'}Binary Outcast{else}{$PAGE_DATA.ownerDisplayName}{/if}
</p>

<h3>About this add-on</h3>

{if $PAGE_DATA.type == 2 || $PAGE_DATA.type == 4 || $PAGE_DATA.type == 64}
  <p>
    {$PAGE_DATA.content}
  </p>

  {if $PAGE_DATA.hasPreview == true}
    <h3>Preview</h3>
    <img src="/datastore/addons/{$PAGE_DATA.slug}/preview.png" class="aligncenter" style="max-width: 750px"/>
  {/if}
{elseif $PAGE_DATA.type == 8}
  <p>Please note the installation instructions, just installing the language pack and letting Pale Moon restart is not enough! Also keep in mind that these language packs are a convenience and that the browser is and remains an English language product at its heart, so something like the Safe Mode dialog, about box and default bookmarks folder names will be in English.</p>
  <div class="instruction-infobox">
    <h3>Installation instructions</h3>

    <p>
      A few simple steps is all that is needed to install these language packs. You have the choice of 2 different methods, either by installing the Locale Switcher extension or by using the instructions to perform a one-time preference change:
    </p>

    <p>
      <strong>Extension method:</strong>
    </p>

    <ol>
      <li>Download the language pack .xpi from this page (below). Choose to immediately "install" in the Pale Moon browser (the default when left-clicking), skipping the need to save it first.</li>
      <li>Install <a href="/addon/locale-switcher/">Pale Moon Locale Switcher from this site.</a></li>
      <li>Click the new globe icon with colored bubbles in your toolbar, and select the language you prefer from the drop-down.</li>
      <li>Let the browser restart when asked.</li>
    </ol>

    <p>
      <strong>Preference method:</strong>
    </p>

    <ol>
      <li>Download the language pack .xpi from this page (below). You may also choose to immediately "install" in the Pale Moon browser (the default when left-clicking), skipping the need to save it first (go to step 3). You do not have to restart Pale Moon yet.</li>
      <li>If you downloaded the .xpi first, double-click the .xpi in explorer/other file manager. Confirm that you want to install the .xpi in your browser. This will add the language pack to Pale Moon. You do not have to restart Pale Moon yet.</li>
      <li>To actually switch to the new language, you also have to make a configuration change. Go to the advanced configuration editor (type <a href="about:config">about:config</a> in the address bar and press enter).</li>
      <li>Find the setting general.useragent.locale which is set to "en-US" by default. Double-click it to change.</li>
      <li>Enter the language code for your locale, including region if applicable. E.g.: "fr" if you live in France, "ja" if you live in Japan, "es-MX" if you want Mexican Spanish. Use the same code as the file name of the language pack you downloaded.</li>
      <li>Close Pale Moon completely and restart it.</li>
    </ol>
  </div>
  <p>And that's it! You can now use Pale Moon in your native language.</p>
  <p>Note that these language packs only change the interface language. They don't change the language used for the spellchecker.</p>
{/if}

<p style="text-align: center; padding: 10px;">
{if $VALID_CLIENT}
  {if $VALID_VERSION}
  <a class="dllink_green" href="/?component=download&id={$PAGE_DATA.id}">
    <img border="0" src="/skin/palemoon/download.png" alt="" style="width: 24px; height: 24px; position: relative; top: 7px; right: 4px;" />
    <span>Install Now</span>
  </a>
  {else}
  <a class="dllink_blue" href="http://palemoon.org/" target="_blank">
    <img border="0" src="/skin/palemoon/download.png" alt="" style="width: 24px; height: 24px; position: relative; top: 7px; right: 4px;" />
    <span>Upgrade Pale Moon</span>
  </a>
  {/if}
{else}
  <a class="dllink_blue" href="http://palemoon.org/" target="_blank">
    <img border="0" src="/skin/palemoon/download.png" alt="" style="width: 24px; height: 24px; position: relative; top: 7px; right: 4px;" />
    <span>Only on Pale Moon</span>
  </a>
{/if}
</p>
