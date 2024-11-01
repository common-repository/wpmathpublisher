<?php
$myWpMathPublisher =& new WpMathPublisher;

// check if updates have to be saved
if(isset($_POST['submit']['defaults'])) {
	$myWpMathPublisher->toDefaults();
	?>
	<div class="updated">
		<p><strong><?php _e('Options resetted to default values') ?></strong></p>
	</div>
	<?php
} else if(isset($_POST['submit']['clearCache'])) {
	$myWpMathPublisher->clearCache();
	?>
	<div class="updated">
		<p><strong><?php _e('Cache cleared'); ?></strong></p>
	</div>
	<?php
} else if(isset($_POST['submit']['save'])) {
	$message = $myWpMathPublisher->checkRGB($_POST['fontColorRed'], $_POST['fontColorGreen'], $_POST['fontColorBlue'], $_POST['fontColorAlpha']);
	$message .= $myWpMathPublisher->checkRGB($_POST['backColorRed'], $_POST['backColorGreen'], $_POST['backColorBlue'], $_POST['backColorAlpha']);
	?>
	<div class="updated">
		<p><strong><?php _e('Options saved'); ?></strong></p>
		<?php 
		if(!empty($message)) {
			echo '<p style="margin-left: 50px;">';
			_e('Update may not have been successfull:');
			echo '<em>'; echo $message; echo '</em>'; 
			echo '</p>';
		} else {
			$font = array('red' => $_POST['fontColorRed'], 'green' => $_POST['fontColorGreen'], 'blue' => $_POST['fontColorBlue'], 'alpha' => $_POST['fontColorAlpha']);
			$back = array('red' => $_POST['backColorRed'], 'green' => $_POST['backColorGreen'], 'blue' => $_POST['backColorBlue'], 'alpha' => $_POST['backColorAlpha']);
			
			update_option('wpmathpublisher_font', $font);
			update_option('wpmathpublisher_back', $back);
			
			$myWpMathPublisher->readOptions();
		}
		?>
	</div>
	<?php
}

?>
<div class="wrap">
	<div id="icon-themes" class="icon32"><br /></div>
	<h2><?php _e('WpMathPublisher Design'); ?></h2>
	<form name="wpmathpublisherColors" method="post" action="">
		<fieldset class="options">
			<!-- default submit button -->
			<input type="submit" name="submit[save]" value="<?php _e('Save'); ?>" style="display: none;" />
			<div style="font-style: italic; font-size: 8pt; float: right; border: 1px dashed #000000; padding: 5px; width: 250px;">
				<strong><?php _e('Information:'); ?></strong>
				<br /><br />
				<?php _e('Colors are to be defined in RGB components'); ?>
				<br /><br />
				<?php _e('Allowed RGB range is 0 to 255'); ?>
				<br />
				<?php _e('Allowed transparency range is from 0 (opaque) to 127 (transparent)'); ?>
				<br /><br />
				<div style="border: 1px solid #000000; color: rgb(<?php echo $myWpMathPublisher->font['red']; ?>, <?php echo $myWpMathPublisher->font['green']; ?>, <?php echo $myWpMathPublisher->font['blue']; ?>); background-color: rgb(<?php echo $myWpMathPublisher->back['red']; ?>, <?php echo $myWpMathPublisher->back['green']; ?>, <?php echo $myWpMathPublisher->back['blue']; ?>); width: 100px; padding: 20px; margin: 0 auto; text-align: center;"><?php _e('Colors without transparency'); ?></div>
				<br /><br />
				<input class="button" type="submit" tabindex="2" name="submit[defaults]" value="<?php _e('Reset to defaults'); ?>" />
			</div>
			<h3><?php _e('Font color'); ?></h3>
			<table class="editform">
				<tr>
					<td style="width: 150px;"><?php _e('Red:'); ?></td>
					<td><input style="text-align: right;" name="fontColorRed" type="text" id="fontColorRed" value="<?php echo $myWpMathPublisher->font['red']; ?>" size="10" /></td>
				</tr><tr>
					<td style="width: 150px;"><?php _e('Green:'); ?></td>
					<td><input style="text-align: right;" name="fontColorGreen" type="text" id="fontColorGreen" value="<?php echo $myWpMathPublisher->font['green']; ?>" size="10" /></td>
				</tr><tr>
					<td style="width: 150px;"><?php _e('Blue:'); ?></td>
					<td><input style="text-align: right;" name="fontColorBlue" type="text" id="fontColorBlue" value="<?php echo $myWpMathPublisher->font['blue']; ?>" size="10" /></td>
				</tr><tr>
					<td style="width: 150px;"><?php _e('Transparency:'); ?></td>
					<td><input style="text-align: right;" name="fontColorAlpha" type="text" id="fontColorAlpha" value="<?php echo $myWpMathPublisher->font['alpha']; ?>" size="10" /></td>
				</tr>
			</table>
			<h3><?php _e('Background color'); ?></h3>
			<table class="editform">
				<tr>
					<td style="width: 150px;"><?php _e('Red:'); ?></td>
					<td><input style="text-align: right;"name="backColorRed" type="text" id="backColorRed" value="<?php echo $myWpMathPublisher->back['red']; ?>" size="10" /></td>
				</tr><tr>
					<td style="width: 150px;"><?php _e('Green:'); ?></td>
					<td><input style="text-align: right;"name="backColorGreen" type="text" id="backColorGreen" value="<?php echo $myWpMathPublisher->back['green']; ?>" size="10" /></td>
				</tr><tr>
					<td style="width: 150px;"><?php _e('Blue:'); ?></td>
					<td><input style="text-align: right;"name="backColorBlue" type="text" id="backColorBlue" value="<?php echo $myWpMathPublisher->back['blue']; ?>" size="10" /></td>
				</tr><tr>
					<td style="width: 150px;"><?php _e('Transparency:'); ?></td>
					<td><input style="text-align: right;"name="backColorAlpha" type="text" id="backColorAlpha" value="<?php echo $myWpMathPublisher->back['alpha']; ?>" size="10" /></td>
				</tr>
			</table>
			<div style="margin-top: 20px; margin-left: 90px;">
				<input class="button-primary" type="submit" tabindex="1" name="submit[save]" value="<?php _e('Save Changes'); ?>" />
				&nbsp;&nbsp;&nbsp;
				<input class="button" type="reset" value="<?php _e('Reset'); ?>" />
			</div>
		</fieldset>
	</form>
	<br /><br />
	<form name="wpmathpublisherFiles" method="post" action="">
		<fieldset class="options">
			<h3><?php _e('Empty Cache'); ?></h3>
			<p><?php _e('Click this button to empty the image cache. This can be necessary if you changed colors and want your old images to be rendered again:'); ?></p>
			<input class="button" style="text-align: right;"type="submit" tabindex="2" name="submit[clearCache]" value="<?php _e('Clear cache'); ?>" />
		</fieldset>
	</form>
	<br /><hr /><br />
	<h3><?php _e('User Guide'); ?></h3>
	<p>
		<?php _e('To use this plugin is quite easy. The most simple use is just to to convert formulas into readable images'); ?>
		<br />
		<div style="font-size: 8pt; line-height: 20px; margin: 0 0 50px 0; padding: 0 0 0 40px; position: relative;">
			<img style="position: absolute; top: 13px; left: 10px;" src="<?php echo WP_PLUGIN_URL; ?>/wpmathpublisher/example.png" alt="Example" />
			[math]1 = e^{i pi}[/math]&nbsp;&nbsp;&nbsp;
			<em><?php _e('will be converted to'); ?></em>&nbsp;&nbsp;&nbsp;
			<?php echo mathimage('1 = e^{i pi}', 12, $myWpMathPublisher->imgPath); ?>
		</div>
		<?php _e('If you don\'t like the size of the text in the images you can adjust it just by passing an additional attribute "size"'); ?>
		<div style="font-size: 8pt; line-height: 20px; margin: 0 0 50px 0; padding: 0 0 0 40px; position: relative;">
			<img style="position: absolute; top: 21px; left: 10px;" src="<?php echo WP_PLUGIN_URL; ?>/wpmathpublisher/example.png" alt="Example" />
			[math size="20"]1 = e^{i pi}[/math]&nbsp;&nbsp;&nbsp;
			<em><?php _e('will be converted to'); ?></em>&nbsp;&nbsp;&nbsp;
			<?php echo mathimage('1 = e^{i pi}', 20, $myWpMathPublisher->imgPath); ?>
			<br />
			[math size="10"]1 = e^{i pi}[/math]&nbsp;&nbsp;&nbsp;
			<em><?php _e('will be converted to'); ?></em>&nbsp;&nbsp;&nbsp;
			<?php echo mathimage('1 = e^{i pi}', 10, $myWpMathPublisher->imgPath); ?>
		</div>
		<?php _e('In any case there even is an option if you do NOT want you\'re code to be parsed to an image. Just add the attribute noparse="true"'); ?>
		<div style="font-size: 8pt; line-height: 20px; margin: 0 0 50px 0; padding: 0 0 0 40px; position: relative;">
			<img style="position: absolute; top: 13px; left: 10px;" src="<?php echo WP_PLUGIN_URL; ?>/wpmathpublisher/example.png" alt="Example" />
			[math]1 = e^{i pi}[/math]&nbsp;&nbsp;&nbsp;
			<em><?php _e('will be converted to'); ?></em>&nbsp;&nbsp;&nbsp;
			<?php echo $myWpMathPublisher->parseMath(array(), '1 = e^{i pi}'); ?>
			<br />
			[math noparse="false"]1 = e^{i pi}[/math]&nbsp;&nbsp;&nbsp;
			<em><?php _e('will be converted to'); ?></em>&nbsp;&nbsp;&nbsp;
			<?php echo $myWpMathPublisher->parseMath(array('noparse' => 'false'), '1 = e^{i pi}'); ?>
			<br />
			[math noparse="true"]1 = e^{i pi}[/math]&nbsp;&nbsp;&nbsp;
			<em><?php _e('will be converted to'); ?></em>&nbsp;&nbsp;&nbsp;
			<?php echo $myWpMathPublisher->parseMath(array('noparse' => 'true'), '1 = e^{i pi}'); ?>
		</div>
		<?php _e('Of course you can combine all attributes:'); ?>
		<div style="font-size: 8pt; line-height: 20px; margin: 0 0 50px 0; padding: 0 0 0 40px; position: relative;">
			<img style="position: absolute; top: 14px; left: 10px;" src="<?php echo WP_PLUGIN_URL; ?>/wpmathpublisher/example.png" alt="Example" />
			[math size=13]1 = e^{i pi}[/math]&nbsp;&nbsp;&nbsp;
			<em><?php _e('will be converted to'); ?></em>&nbsp;&nbsp;&nbsp;
			<?php echo $myWpMathPublisher->parseMath(array('size' => 13), '1 = e^{i pi}'); ?>
			<br />
			[math size=13 noparse="true"]1 = e^{i pi}[/math]&nbsp;&nbsp;&nbsp;
			<em><?php _e('will be converted to'); ?></em>&nbsp;&nbsp;&nbsp;
			<?php echo $myWpMathPublisher->parseMath(array('noparse' => 'true', 'size' => '13'), '1 = e^{i pi}'); ?>
		</div>
	</p>
</div>