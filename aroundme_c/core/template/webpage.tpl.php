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


<form action="index.php?t=webpage&amp;wp=<?php echo AM_WEBPAGE_NAME;?>" method="POST">
<input type="hidden" name="webpage_id" value="<?php if (isset($webpage['webpage_id'])) { echo $webpage['webpage_id'];}?>" />

<div class="box">
	<div class="box_header">
		<h1><?php $this->getLanguage('core_edit_webpage');?></h1>
	</div>

	<div class="box_body">
		<p>
			<label for="id_webpage_body"><?php $this->getLanguage('common_body');?></label><br />
			<textarea id="id_webpage_body" rows="20" cols="100" name="webpage_body" wrap="off"><?php if (isset($webpage['webpage_body'])) { echo $webpage['webpage_body'];}?></textarea>
		</p>

		<p align="right">
			<input type="submit" name="save_webpage" value="<?php $this->getLanguage('common_save');?>" />
			<input type="submit" name="save_go_webpage" value="<?php $this->getLanguage('common_save_go');?>" />
		</p>

		<p>
			<a href="#tag_builder" onclick="javascript:objShowHide('core_plugin_installer');"><?php $this->getLanguage('core_plugin_helper');?></a>
			<a href="#webpage_linker" onclick="javascript:objShowHide('core_webpage_linker');"><?php $this->getLanguage('common_webpage_helper');?></a>
			<a href="#webpage_layouts" onclick="javascript:objShowHide('core_layouts');"><?php $this->getLanguage('core_layout_helper');?></a>
			<a href="#picture_selector" onclick="javascript:objShowHide('core_picture_selector');"><?php $this->getLanguage('common_file_helper');?></a>
		</p>
	</div>
</div>
</form>


<?php
include ('core/template/inc/webpage_linker.inc.tpl.php');
?>


<script type="text/javascript">
function fetchWebpageLayout(layout) {
	url = 'create/layout/'+layout+'/html.layout.php';

	http_request = false;

	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			// set type accordingly to anticipated content type
			http_request.overrideMimeType('text/html');
		}
	}
	else if (window.ActiveXObject) { // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e) {
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) {
			}
		}
	}

	if (!http_request) {
		alert('Cannot create XMLHTTP instance');
		return false;
	}
	http_request.onreadystatechange = displayWebpageLayout;
	http_request.open('POST', url, true);
	http_request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	http_request.send(null);
}

function displayWebpageLayout () {
	if (http_request.readyState == 4) {
		if (http_request.status == 200) {
			layoutHTML = http_request.responseText;

			document.getElementById('core_layout_display').value = layoutHTML;
			document.getElementById('core_layout_container').style.display = 'block';
		}
	}

}
</script>


<a name="webpage_layouts"></a>
<div class="box" id="core_layouts" style="display:none;">
	<div class="box_header">
		<h1><?php $this->getLanguage('core_layouts');?></h1>
	</div>

	<div class="box_body">
		<table cellspacing="0" cellpadding="2" border="0" width="100%">
			<tr>
				<td valign="top">
					<p>
						<?php $this->getLanguage('core_layout_helper_intro');?>
					<p>

					<?php
					foreach($webpage_layouts as $key => $i):
					?>
					<img src="create/layout/<?php echo $i;?>/thumb.png" onClick="javascript:fetchWebpageLayout('<?php echo $i;?>');" style="cursor:pointer;" />
					<?php
					endforeach;
					?>
				</td>
				<td valign="top" width="260">
					<div id="core_layout_container" style="display:none;">
						<p>
							<?php $this->getLanguage('core_layout_helper_instruction');?>
						</p>

						<textarea id="core_layout_display" rows="6" cols="60" onclick="javascript:this.focus();this.select();" readonly="true"></textarea>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>


<script type="text/javascript">
function loadPluginTagBuilder(url) {

	document.getElementById('plugin_builder_display_tag').style.display = 'none';
	document.getElementById('input_builder_display_tag').value = '';

	http_request = false;

	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			// set type accordingly to anticipated content type
			http_request.overrideMimeType('text/html');
		}
	}
	else if (window.ActiveXObject) { // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e) {
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) {
			}
		}
	}

	if (!http_request) {
		alert('Cannot create XMLHTTP instance');
		return false;
	}
	http_request.onreadystatechange = displayPluginTagBuilder;
	http_request.open('POST', url, true);
	http_request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	http_request.send(null);
}

function displayPluginTagBuilder () {
	if (http_request.readyState == 4) {
		if (http_request.status == 200) {
			tagBuilderForm = http_request.responseText;

			document.getElementById('core_layout_tag_builder').innerHTML = tagBuilderForm;
			document.getElementById('core_layout_tag_builder').style.display = 'block';
		}
	}

}
</script>


<a name="tag_builder"></a>
<div class="box" id="core_plugin_installer" style="display:none;">
	<div class="box_header">
		<h1><?php $this->getLanguage('core_plugin_helper');?></h1>
	</div>

	<div class="box_body">
		<div id="plugin_builder_display_tag" style="display:none;">
			<p>
				<?php $this->getLanguage('core_plugin_helper_intro');?>
			</p>

			<p>
				<input type="text" style="width:60em;" id="input_builder_display_tag" value="" onclick="javascript:this.focus();this.select();" readonly="true" />
			</p>
		</div>

		<table cellspacing="0" cellpadding="2" border="0" width="100%">
			<tr>
				<td valign="top" width="40%">
						<?php
						if (isset($plugins)) {
						?>
						<ul>
						<?php
							foreach ($plugins as $key => $i):
								if (is_readable('plugins/'.$i.'/template/inc/tag_builder_menu.inc.tpl.php')) {
									include ('plugins/'.$i.'/template/inc/tag_builder_menu.inc.tpl.php');
								}
							endforeach;
						?>
						
						<?php
						if (isset($blocks)) {
						?>
						<li><?php $this->getLanguage('core_custom');?></li>

						<ul>
							<?php
							foreach ($blocks as $key => $i):
							?>
							<li><a href="#tag_builder" onclick="javascript:buildPluginCustomTag('<?php echo $i['block_name'];?>');"><?php echo $i['block_name'];?></a></li>
							<?php
							endforeach;
							?>
						</ul>
						<?php }?>
						</ul>
						
						<script type="text/javascript">

						function buildPluginCustomTag(name) {
							tag = '<AM_BLOCK plugin="custom" name="'+name+'" />';

							document.getElementById('core_layout_tag_builder').style.display = 'none';
							document.getElementById('plugin_builder_display_tag').style.display = 'block';
							document.getElementById('input_builder_display_tag').value = tag;
						}
						</script>
						<?php }?>
				</td>
				<td valign="top">
					<div id="core_layout_tag_builder" style="display:none;"></div>
				</td>
			</tr>
		</table>
	</div>
</div>

<?php
include ('core/template/inc/picture_selector.inc.tpl.php');
?>