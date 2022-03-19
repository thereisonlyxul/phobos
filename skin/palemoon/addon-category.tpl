<h1>{$PAGE_TITLE}</h1>

<p>{$PAGE_DESCRIPTION}</p>

<div>
{foreach $PAGE_DATA as $_value}
  <a href="/addon/{$_value.slug|lower}" class="fake-table-row category-addon hosted-extensions">
    {if $_value.hasIcon == true}
    <img src="/datastore/addons/{$_value.slug|lower}/icon.png" class="category-addon-icon alignleft" width="32px" height="32px">
    {else}
      {if $_value.type == 4}
        {assign "ICON_TYPE" "theme"}
      {elseif $_value.type == 8}
        {assign "ICON_TYPE" "locale"}
      {elseif $_value.type == 64}
        {assign "ICON_TYPE" "dictionary"}
      {else}
        {assign "ICON_TYPE" "extension"}
      {/if}
    <img src="/skin/shared/icons/types/{$ICON_TYPE}32.png" class="category-addon-icon alignleft" width="32px" height="32px">
    {/if}
      <div class="category-addon-content"><strong>{$_value.name}</strong>
        <br />
        <small>{$_value.description|truncate:190:"&hellip;"}</small>
      </div>
    </a>
  {foreachelse}
    <p><em>There are <strong style="color: #BF0000;">currently</strong> no available add-ons of this type.</em></p>
  {/foreach}
</div>
