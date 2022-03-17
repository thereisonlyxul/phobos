<?php
// == | Main | ========================================================================================================

$gvAppName = TARGET_APPLICATION['palemoon']['name'];
$gvAppType = TARGET_APPLICATION['palemoon']['commonType'];

$gvDiscoverHTML = <<<HTML_END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>{$gvAppName} - Add-ons - Discover</title>
    <style type="text/css">
      body {
        margin-top: 0px;
        margin-bottom: 0px;
        font: 12px/1.33 "Lucida Grande","Lucida Sans",Helvetica,Arial,sans-serif;
        color: rgb(55, 61, 72);
        text-shadow: 0px 1px 0px rgba(255, 255, 255, 0.3);
      }

      a, a:visited, a:active  {
        color: rgb(22, 54, 101);
      }

      a:hover {
        color: rgb(192, 19, 20);
      }

      #Discover-Wrapper {
        width: 100%
        min-width: 650px;
        margin: 0 auto;
        padding-top: 15px;
        padding-left: 10px;
        padding-right: 10px;
      }

      #Discover-Content {
        margin-top: -5px;
        min-width: 650px;
        width: 100%;
        display: table;
        /* font-family: 'Roboto', Verdana, sans-serif; */
        /* font-size: 14px; */
      }

      #Discover-Header {
        min-width: 650px;
        vertical-align: top;
        margin: 0px;
      }

      #Discover-Content-Left {
        display: table-cell;
      }

      #Discover-Content-Right {
        padding-left: 15px;
        width: 200px;
        display: table-cell;
      }
      
      #Discover-Header-Content {
        margin-bottom: 25px;
        border-width: 1px 1px 0px;
        border-style: solid;
        border-color: rgb(168, 184, 209);
        -moz-border-top-colors: none;
        -moz-border-right-colors: none;
        -moz-border-bottom-colors: none;
        -moz-border-left-colors: none;
        border-image: none;
        border-radius: 8px 8px 8px 8px;
        background: -moz-linear-gradient(center top , rgb(255, 255, 255) 0px, rgb(236, 241, 247) 100%) repeat scroll 0% 0% transparent;
        box-shadow: 0px -3px 0px rgba(58, 78, 103, 0.05) inset, 0px 3px 0px rgba(175, 195, 220, 0.3);
      }
      
      .Discover-Content-Body {
        display: block;
        padding: 20px;
        border-radius: 8px 8px 8px 8px;
        background: none no-repeat scroll 0% 0% rgba(255, 255, 255, 0.35);
        border: 1px solid rgb(168, 184, 209);
        box-shadow: 0px 1px 3px rgba(58, 78, 103, 0.15) inset, 0px 1px 0px rgba(255, 255, 255, 0.5);
        vertical-align: top;
      }
      
      .alignleft {
        float:left;
        text-align:left;
        margin-right:10px;
      }
      
      .alignright {
        float:right;
        text-align:right;
        margin-left:10px;
      }
      
      .aligncenter {
        display:block;
        margin-left:auto;
        margin-right:auto;
      }
      
      .clearfix:after {
        content: ".";
        display: block;
        height: 0;
        clear: both;
        visibility: hidden;
      }
      
      .amobutton {
        width: 235px;
        height: 66px;
        display: inline-block;
        text-align:center;
        vertical-align: top;
        align: center;
        margin-left: 10px;
        margin-right: 10px;
        margin-bottom: 20px;
        padding: 8px 8px;
        background-image: linear-gradient(rgb(255, 255, 255), rgb(236, 241, 247));
        border: 1px solid rgb(183, 195, 215);
        border-radius: 6px 6px 6px 6px;
        box-shadow: 0px -2px 0px rgba(58, 78, 103, 0.08) inset, 0px 2px 0px rgba(190, 210, 230, 0.5);
        text-decoration: none;
        color: rgb(55, 61, 72);
      }
      
      .amobutton:hover {
        border-color: rgb(165, 175, 185);
        box-shadow: 0px -2px 0px rgba(58, 78, 103, 0.1) inset, 0px 2px 0px rgba(190, 210, 230, 0.85);
        transition-property: border-color, box-shadow;
        transition-duration: 0.1s;
        transition-timing-function: ease-out;
        text-decoration: none;
        color: rgb(55, 61, 72);
      }
      
      .amobutton:active {
        position:relative;
        top:1px;
        text-decoration: none;
        color: rgb(55, 61, 72);
      }
      
      #generic-wrap {
        width: 100%;
        margin: 0 auto;
      }
    </style>
  </head>
  <body>
    <div id="Discover-Wrapper">
      <div id="Discover-Header">
        <div id="Discover-Header-Content">
          <img src="/skin/shared/icons/discover/fx2005-extensions.png?1647512107" height= "90px" style="float:left; text-align:left; margin-right:25px; margin-left: 25px; margin-top: 10px; margin-bottom: 25px;" />
          <span style="font-size: 24px; display: block; padding-top: 20px;">{$gvAppName}'s Add-ons Support has changed. Again.</span>
          <span style="display: block; padding-top: 5px; padding-right: 25px;">
            Starting with {$gvAppName} 30.0.0 technological support for unmaintained Firefox Extensions has been restored. 
          </span>
          <span style="display: block; padding-top: 5px; padding-bottom: 20px; padding-right: 25px;">  
            <strong>This was accomplished by changing {$gvAppName}'s internal application ID to the original Firefox identifier. Going forward, the ID that {$gvAppName} 25-29 used will no longer be valid.</strong></br>
            For more information, please see <a href="https://forum.palemoon.org/viewtopic.php?f=1&t=27956" target="_blank">the announcement</a> on the {$gvAppName} forums.
          </span>
          <span style="display: block; padding-top: 5px; padding-right: 25px; padding-bottom: 8px; text-align: center;">
            <strong><em>Please note that the offerings and features of the new Add-ons Site Service will initially be limited. This is a temporary condition due the Milestone (30.0.0) release being pushed up to address a critical security vulnerability affecting then-current and older versions.</em></strong>
          </span>
        </div>
      </div>
      <div id="Discover-Header">
        <div id="Discover-Header-Content">
          <img src="/skin/shared/icons/discover/discover-logo.png" height= "90px" style="float:left; text-align:left; margin-right:25px; margin-left: 25px; margin-top: 20px; margin-bottom: 25px;" />
          <span style="font-size: 24px; display: block; padding-top: 20px;">What are Add-ons?</span>
          <span style="display: block; padding-top: 5px; padding-right: 25px;">
            Add-ons let you customize {$gvAppName} with extra functionality or a different look.
          </span>
          <span style="display: block; padding-top: 5px; padding-bottom: 20px; padding-right: 25px;" padding-bottom: 8px;>  
            There are several types of add-ons. Extensions expand the capabilities of the {$gvAppType}, themes allow you to personalize {$gvAppName}'s visual aesthetic, and plugins further extend the {$gvAppType} with specialized external components such as the Adobe Flash Player.
          </span>
        </div>
      </div>
      <div id="Discover-Content">
        <div id="Discover-Content-Left">
          <div class="Discover-Content-Body">
            <p style="text-align: center;">
              <strong>You can take advantage of {$gvAppName}'s exceptional extensibility by installing add-ons from:</strong>
            </p>
            <div style="margin: 0 auto; width: 825px; text-align: center; vertical-align: top;">
            <a class="amobutton" href="https://addons.palemoon.org/" target="_blank"><img class="alignleft" src="/skin/shared/icons/apps/palemoon64.png?1647512107" /><p><strong>{$gvAppName} Add-ons Site</strong></p><p><small>Browse add-ons for {$gvAppName}</small></p></a>
            
            </div>
          </div>
        </div>
        <div id="Discover-Content-Right" style="display: none;">
          <div class="Discover-Content-Body">
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
HTML_END;

gfOutput($gvDiscoverHTML, 'html');

// ====================================================================================================================

?>