<?php
/**
 * Ad page - this page is iframed in container page and refreshed on increment gallery slide views.
 */
require_once('lib/config.inc.php'); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>

<script language="javascript" type="text/javascript" src="/cc-common/flashobject.js"></script>
<script language="javascript" type="text/javascript" src="/cc-common/js/jquery-1.7.1.js"></script>
<script>if (window.jQuery && typeof(Prototype) != 'undefined') jQuery.noConflict();</script>
<script language="javascript" type="text/javascript" src="/cc-common/js/CcJS.js"></script>
<script language="javascript" type="text/javascript" src="/cc-common/js/tabcontent.js"></script>
<script language="JavaScript1.2" type="text/javascript" src="/cc-common/polling_tool/poll.js?now"></script>
<script language="javascript" type="text/javascript" src="/cc-common/templates/functionsihrv4.js"></script>
<script language="javascript" type="text/javascript" src="/cc-common/templates/presslaff.js"></script>
<script language="javascript" type="text/javascript" src="/cc-common/templates/addisplay/DARTheader.js"></script>
<script language="javascript" type="text/javascript" src="/cc-common/templates/addisplay/DFPheader.js?1343148125"></script>
<script language="javascript" type="text/javascript" src="/cc-common/templates/addisplay/browdet.js"></script>
<script language="javascript" type="text/javascript">
  BrowserDetect.init();
  dclk_isDartRichMediaLoaded = true;
</script>

<style type="text/css">
	body {
		margin-left: 0px;
		margin-top: 0px;
	}
</style>

</head>
<body>

<div id="DARTad300x250"><script type="text/javascript">DFP.pushAd({div:"DARTad300x250",size:"300x250",position:"3307"} );</script></div>

<script type="text/javascript">
	var $dart_site="<?php echo AD_MARKET; ?>";
	var $dart_station="<?php echo AD_STATION; ?>";
	var $dart_market="<?php echo AD_MARKET; ?>";
	var $dart_contentFormat="<?php echo AD_FORMAT ?>";
	var $dart_content1="&content1=";
	var $dart_content2="&content2=";
	var $dart_localcontent="embeddableGallery";
	var $dart_override_set="yes";
	var $dart_override_number;
	var $dart_override_positions_array=['3307','3328','3330','5014','5050','7000','7004','7010',''];
	var $dart_override_splits_array=['0','0','0','0','0','0','0','0',''];
</script>

<script type="text/javascript" src="/cc-common/templates/addisplay/DARTfooter.js"></script>
<script type="text/javascript" src="/cc-common/js/writeCapture.js"></script>

<div id="wallpaperAd" style="max-height: 1px; overflow: hidden"></div>

<script type="text/javascript">
	DFP.launchAds();
</script>

<script type="text/javascript">
	if(document.location.protocol=='http:'){
		var Tynt=Tynt||[];Tynt.push('bOa0NM1Her35hWadbi-bnq');Tynt.i={"c":false};
		(function(){var s=document.createElement('script');s.async="async";s.type="text/javascript";s.src='http://tcr.tynt.com/ti.js';var h=document.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);})();
	}
</script>

</body>
</html>