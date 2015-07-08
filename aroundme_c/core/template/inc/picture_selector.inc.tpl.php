<script type="text/javascript">

function buildPictureTag (filename, thumb_90, thumb_35) {
	document.getElementById('picture_selector_tag_display').value = "<img src=\"core/get_file.php?file=" + filename + "\" alt=\"\" />";
	document.getElementById('picture_selector_tag_display_thumb_90').value = "<img src=\"core/get_file.php?file=" + thumb_90 + "\" alt=\"\" />";
	document.getElementById('picture_selector_tag_display_thumb_35').value = "<img src=\"core/get_file.php?file=" + thumb_35 + "\" alt=\"\" />";
	document.getElementById('picture_selector_tag').style.display = 'block';
	document.getElementById('file_selector_tag').style.display = 'none';
}

function buildFileTag(filename) {
	document.getElementById('file_selector_tag_display').value = "<a href=\"core/get_file.php?file=" + filename + "\">" + filename + "</a>";
	document.getElementById('file_selector_tag').style.display = 'block';
	document.getElementById('picture_selector_tag').style.display = 'none';
}

</script>

<a name="picture_selector"></a>
<div class="box" id="core_picture_selector" style="display:none;">
	<div class="box_header">
	    <h1><?php $this->getLanguage('common_file_helper');?></h1>
	</div>

	<div class="box_body">
		<div id="picture_selector_tag" style="display:none;">
			<input type="text" size="60" id="picture_selector_tag_display" value="" style="width:60em;" onclick="javascript:this.focus();this.select();" readonly="true" /> (normal size)<br />
			<input type="text" size="60" id="picture_selector_tag_display_thumb_90" value="" style="width:60em;" onclick="javascript:this.focus();this.select();" readonly="true" /> (thumb 90px)<br />
			<input type="text" size="60" id="picture_selector_tag_display_thumb_35" value="" style="width:60em;" onclick="javascript:this.focus();this.select();" readonly="true" /> (thumb 35px)<br />
		</div>
		<div id="file_selector_tag" style="display:none;">
			<input type="text" size="60" id="file_selector_tag_display" value="" style="width:60em;" onclick="javascript:this.focus();this.select();" readonly="true" /><br />
		</div>
		
		<?php 
		if (isset($pictures)) {
		foreach($pictures as $key => $i):
		?>
		<div class="gallery_item">
			<?php if (isset($i['thumb_35'])) { ?>
				<img src="core/get_file.php?file=<?php echo $i['thumb_35'];?>" alt="" onClick="javascript:buildPictureTag('<?php echo $i['file_name']; ?>', '<?php echo $i['thumb_90'];?>', '<?php echo $i['thumb_35'];?>');" class="cursor_hand" style="border: 1px solid black;" />
			<?php } else { ?>
				<img src="<?php echo AM_TEMPLATE_PATH . $core_config['file']['type'][$i['file_type']]['image'][2]?>" alt="" onclick="javascript:buildFileTag('<?php echo $i['file_name']; ?>');" class="cursor_hand" style="border: 1px solid black;"/>
			<?php } ?>
		</div>
		<?php
		endforeach;
		}
		else {
		?>
		<p>
			<?php $this->getLanguage('common_no_list_items');?>
		</p>
		<?php }?>
		
		<div style="clear: both;"></div>
	</div>
</div>