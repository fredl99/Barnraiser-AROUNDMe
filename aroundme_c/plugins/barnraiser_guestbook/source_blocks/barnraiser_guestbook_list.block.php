<script type="text/javascript">
	//<![CDATA[
	function clean() {
		if (document.getElementById('id_guestbook_body').value == 'AM_BLOCK_LANGUAGE_PROMPT') {
			document.getElementById('id_guestbook_body').value = '';
			return false;
		}
	}

	function imposeMaxLength(obj, max) {
		//return (Object.value.length <= MaxLen);
		if (obj.value.length > max) {
			obj.value = obj.value.substring(0, max);
		}
		else  {
			document.getElementById('guestbook_counter').innerHTML = 'AM_BLOCK_LANGUAGE_CHARACTERS_REMAINING '+(max - obj.value.length);
		}
	}
	//]]>
</script>

<div class="barnraiser_guestbook_list">
    <div class="block">
        <div class="block_body">
            <?php
            if (isset($barnraiser_guestbook_entries)){
            ?>
            <ul>
				<?php
				foreach ($barnraiser_guestbook_entries as $key => $i):
				?>
				<li>
					<div class="li_avatar">
						<?php
						if (!empty($i['connection_avatar'])) {
						?>
							<a href="index.php?t=network&amp;connection_id=<?php echo $i['connection_id'];?>" class="avatar"><img src="<?php echo $i['connection_avatar'];?>" width="40" height="40" alt="" border="" /></a>
						<?php
						}
						else {
						?>
							<a href="index.php?t=network&amp;connection_id=<?php echo $i['connection_id'];?>" class="no_avatar"><div title="<?php echo $i['connection_nickname']; ?>"></div></a>
						<?php }?>
					</div>
					<div class="li_content">
                        <a href="index.php?t=network&amp;connection_id=<?php echo $i['connection_id'];?>" class="connection"><?php echo $i['connection_nickname']?></a>
                        <span class="datetime"><?php echo strftime("%d %b %G %H:%M", $i['guestbook_create_datetime']);?></span><br />
                        <span class="body"><?php echo $i['guestbook_body']?></span>
                    </div>
				</li>
                <?php
                endforeach;
                ?>
            </ul>
            <?php
            }
            else {
            ?>
            <p>
                AM_BLOCK_LANGUAGE_NO_ITEMS
            </p>
            <?php }?>
        </div>

		<form action="plugins/barnraiser_guestbook/add_guestbook.php?wp=<?php echo AM_WEBPAGE_NAME;?>" method="post">
		<?php
		if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_guestbook']['add_entry']) {
		?>
		<div class="add">
			<textarea rows="4" cols="34" name="guestbook_body" id="id_guestbook_body" onkeyup="imposeMaxLength(this, 200);" onFocus="clean();">AM_BLOCK_LANGUAGE_PROMPT</textarea><br />
			<span id="guestbook_counter">AM_BLOCK_LANGUAGE_CHARACTERS_REMAINING 200</span>
		</div>
		<?php }?>

		<div class="block_footer">
			<?php
			if (!isset($_SESSION['connection_permission']) || !($_SESSION['connection_permission'] & $plugin_permissions['barnraiser_forum']['add_reply'])) {
			?>
			<span class="disabled_link" onclick="javascript:showInterfaceSystemMessage(event, 'no_guestbook_add_title', 'no_guestbook_add_message');">AM_BLOCK_LANGUAGE_ADD</span>&nbsp;
			<span style="display:none;">
				<span id="no_guestbook_add_title">AM_BLOCK_LANGUAGE_PERMISSION_PROBLEM</span>
				<span id="no_guestbook_add_message">
					AM_BLOCK_LANGUAGE_PERMISSION_PROBLEM_INTRO 
					<?php
					if (isset($_SESSION['connection_id'])) {
						$connection_txt = 'AM_BLOCK_LANGUAGE_ACCOUNT_LINK_ADD';
						$connection_txt = str_replace('SYS_KEYWORD_CONNECTION_ID', $_SESSION['connection_id'], $connection_txt);
						echo $connection_txt;
					}
					else {
					?>
					AM_BLOCK_LANGUAGE_CONNECT_FIRST
					<?php }?>
				</span>
			</span>
			<?php }?>
			
        	<?php
			if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_guestbook']['maintain']) {
        	?>
            	<a href="index.php?p=barnraiser_guestbook&amp;t=maintain&amp;wp=<?php echo AM_WEBPAGE_NAME;?>" class="maintain">AM_BLOCK_LANGUAGE_MAINTAIN</a>
        	<?php }?>

        	<?php
			if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_guestbook']['add_entry']) {
			?>
				<input type="submit" name="insert_guestbook" value="AM_BLOCK_LANGUAGE_ADD" onClick="clean();" />
			<?php }?>
        </div>
        </form>
    </div>
</div>