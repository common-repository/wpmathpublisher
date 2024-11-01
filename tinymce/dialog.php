<?php include_once('../constants.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#wpmp_dlg.title}</title>
	<script type="text/javascript" src="../../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script type="text/javascript" src="js/dialog.js"></script>
</head>
<body>

<form onsubmit="WpmpDialog.insert();return false;" action="#">
	<p>{#wpmp_dlg.textOptions}</p>
	<p>{#wpmp_dlg.formula}: <input id="formula" name="formula" type="text" class="text" onchange="WpmpDialog.preview();" onkeyup="WpmpDialog.preview();" style="font-size: 12px; padding: 3px; height: 15px; width: 300px; vertical-align: center;" /></p>
	<p>
		<span>{#wpmp_dlg.size}: <select name="size" onchange="WpmpDialog.preview();"><option value="default" selected="selected">{#wpmp_dlg.defaultValue}</option><?php for($tmpIndex = WPMP_FONT_MIN; $tmpIndex <= WPMP_FONT_MAX; $tmpIndex++) { echo '<option value="'.$tmpIndex.'">'.$tmpIndex.'</option>'; }?></select></span>
		<span style="margin-left: 30px;">{#wpmp_dlg.parse}: <input type="checkbox" name="parse" value="yes" checked="checked" onchange="WpmpDialog.preview();" /></span>
		<br style="clear: both;" />
	</p>
	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="insert" name="insert" value="{#insert}" onclick="WpmpDialog.insert();" />
		</div>

		<div style="float: right">
			<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
		</div>
		<br style="clear: both;" />
	</div>
</form>
<hr />
<p>{#wpmp_dlg.preview}: <span style="font-style: italic;" id="codePreview">{#wpmp_dlg.codePreview}</span></p>
<p>{#wpmp_dlg.textSyntax}: <a href="http://www.xm1math.net/phpmathpublisher/doc/help.html">{#wpmp_dlg.help}</a></p>

</body>
</html>
