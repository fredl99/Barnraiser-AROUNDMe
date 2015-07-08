<li><?php $this->getLanguage('plugin_barnraiser_connection_plugin_title');?></li>


<ul>
	<li><a href="#tag_builder" onclick="javascript:loadPluginTagBuilder('plugins/barnraiser_connection/block_tag_builder.php?tag=gallery');"><?php $this->getLanguage('plugin_barnraiser_connection_block_gallery');?></a></li>
	<li><a href="#tag_builder" onclick="javascript:loadPluginTagBuilder('plugins/barnraiser_connection/block_tag_builder.php?tag=connect');"><?php $this->getLanguage('plugin_barnraiser_connection_block_connect');?></a></li>
	<li><a href="#tag_builder" onclick="javascript:loadPluginTagBuilder('plugins/barnraiser_connection/block_tag_builder.php?tag=log');"><?php $this->getLanguage('plugin_barnraiser_connection_block_log');?></a></li>
</ul>


<script type="text/javascript">

function buildPluginBarnraiserConnectionTag(name) {
	tag = '<AM_BLOCK plugin="barnraiser_connection" name="'+name+'" ';
	
	if (document.getElementById('tag_builder_form').tag_builder_element_limit && document.getElementById('tag_builder_form').tag_builder_element_limit.value) {
		tag += 'limit="'+document.getElementById('tag_builder_form').tag_builder_element_limit.value+'" ';
	}
	
	tag += '/>';
	
	document.getElementById('plugin_builder_display_tag').style.display = 'block';
	document.getElementById('input_builder_display_tag').value = tag;
}
</script>