<?php
// -----------------------------------------------------------------------
// This file is part of AROUNDMe
// 
// Copyright (C) 2003-2008 Barnraiser
// http://www.barnraiser.org/
// info@barnraiser.org
// 
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; see the file COPYING.txt.  If not, see
// <http://www.gnu.org/licenses/>
// -----------------------------------------------------------------------

?>

<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	
	<title><?php $this->getLanguage('core_style_html_title');?></title>
	<style type="text/css">
	<!--
	@import url(<?php echo AM_TEMPLATE_PATH;?>css/aroundme.css);
	-->
	</style>
	
	<?php
	if (isset($update_mother)) {
	?>
	<script type="text/javascript">
		window.opener.location.reload(true);
	</script>
	<?php }?>
</head>

<body>

<div id="am_menu_container">
	<ul>
		<?php
		$link_css = "";
		if (!isset($style)) {
			$link_css = " class=\"highlight\"";
		}
		?>
		<li><a href="stylesheet_editor.php"<?php echo $link_css;?>><?php $this->getLanguage('core_styles');?></a></li>
		
		<li><a href="#" onclick="javascript:self.close();"><?php $this->getLanguage('common_close');?></a></li>
	</ul>
</div>	


<?php
if (!empty($GLOBALS['am_error_log'])) {
?>
<div id="error_container">
	<div class="content">
		<?php
		foreach($GLOBALS['am_error_log'] as $key => $i):
		?>
			<?php
			if (isset($this->lang['arr_am_error'][$i[0]])) {
				echo $this->lang['arr_am_error'][$i[0]];
			}
			else {
				echo $i[0];
			}
	
			if (!empty($i[1])) {
				echo ": " . $i[1];
			}?>
			<br />
		<?php
		endforeach;
		?>
	</div>
</div>
<?php }?>


<form action="stylesheet_editor.php" method="POST">

<?php
if (isset($style) || isset($_REQUEST['add_style'])) {
?>
<div class="box">

	<div class="box_body">
		<input type="hidden" name="stylesheet_id" value="<?php if (isset($style['stylesheet_id'])) { echo $style['stylesheet_id'];}?>" />
	
		<p>
			<label for="id_stylesheet_name"><?php $this->getLanguage('common_title');?></label>
			<input type="text" id="id_stylesheet_name" name="stylesheet_name" value="<?php if (isset($style['stylesheet_name'])) { echo $style['stylesheet_name'];}?>" />
		</p>

		<p>
			<label for="id_stylesheet_body"><?php $this->getLanguage('core_stylesheet');?></label>
			<textarea id="id_stylesheet_body" name="stylesheet_body" rows="25" cols="90" style="width:24em;" wrap="off"><?php if (isset($style['stylesheet_body'])) { echo $style['stylesheet_body'];}?></textarea>
		</p>

		<p align="right">
			<input type="submit" name="save_stylesheet" value="<?php $this->getLanguage('common_save');?>" />
			<input type="button" name="preview" value="<?php $this->getLanguage('common_preview');?>" onclick="previewStylesheet();"/>
		</p>
		<script type="text/javascript">
			function previewStylesheet() {
				window.opener.document.getElementById('css').innerHTML = document.getElementById('id_stylesheet_body').value;
			}
		</script>
	</div>
</div>
<?php
}
else {
?>
 <div class="box">
	<div class="box_body">
		<p>
			<?php $this->getLanguage('core_simple_editor_intro');?>
		</p>
	
		<?php
		if (isset($styles)) {
		?>
		<table cellspacing="0" cellpadding="2" border="0" width="100%">
		<tr>
			<td valign="top">
				<b><?php $this->getLanguage('core_name');?></b>
			</td>
			<td align="center" valign="top">
				<b><?php $this->getLanguage('core_current');?></b>
			</td>
			<td>
				<br />
			</td>
		</tr>
		<?php
		foreach($styles as $key => $i):
		?>
		<tr>
			<td valign="top">
				<a href="stylesheet_editor.php?stylesheet_id=<?php echo $i['stylesheet_id'];?>"><?php echo $i['stylesheet_name'];?></a>
			</td>
			<td align="center" valign="top">
				<?php
				$checked = "";
				if (isset($css['stylesheet_id']) && $css['stylesheet_id'] == $i['stylesheet_id']) {
					$checked = " checked=\"checked\"";
				}
				?>
				<input type="radio" name="default_style_id" value="<?php echo $i['stylesheet_id'];?>"<?php echo $checked;?> />
			</td>
			<td align="right" valign="top">
				<?php
				if (isset($css['stylesheet_id']) && $css['stylesheet_id'] != $i['stylesheet_id']) {
				?>
				<input type="checkbox" name="delete_style_ids[]" value="<?php echo $i['stylesheet_id'];?>" />
				<?php }?>
				<br />
			</td>
		</tr>
		<?php
		endforeach;
		?>
		</table>

		<p align="right">
			<input type="submit" name="delete_webspace_styles" value="<?php $this->getLanguage('core_delete_selected');?>" />
			<input type="submit" name="set_current_webspace_style" value="<?php $this->getLanguage('core_set_default');?>" />
		</p>
		<?php }?>

		<ul>
			<li><a href="stylesheet_editor.php?add_style=1"><?php $this->getLanguage('core_add_stylesheet');?></a></li>
		</ul>
	</div>
</div>
<?php }?>

</form>
</body>
</html>