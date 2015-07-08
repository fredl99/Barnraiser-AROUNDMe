<div class="barnraiser_wiki_page">
	<div class="block">
		<div class="block_body">
			<?php
			if(isset($barnraiser_wiki_page['wikipage_id'])) {
			?>
			<p>
				<?php echo $barnraiser_wiki_page['revision_body'];?>
			</p>
	
			<p>
				<span class="datetime"><?php echo strftime("%d %b %G %H:%M", $barnraiser_wiki_page['revision_create_datetime']);?></span> 
				<a href="index.php?t=network&amp;connection_id=<?php echo $barnraiser_wiki_page['connection_id'];?>" class="connection_id"><?php echo $barnraiser_wiki_page['connection_nickname']?></a>
			</p>
			<?php
			}
			else {
			?>
			<p>
				AM_BLOCK_LANGUAGE_NO_WIKI_PAGE
			</p>
			<?php }?>
		</div>

		<div class="block_footer">
			<?php
			if (isset($barnraiser_wiki_wikipage) && $barnraiser_wiki_page['wikipage_name'] != $barnraiser_wiki_wikipage) {
			?>
				<a href="index.php?wp=<?php echo AM_WEBPAGE_NAME;?>">AM_BLOCK_LANGUAGE_WIKI_HOME</a>
			<?php }?>
			
			<?php
			if (isset($barnraiser_wiki_page['revision_id']) && $barnraiser_wiki_page['revision_id'] != $barnraiser_wiki_page['current_revision_id']) {
			?>
				<a href="index.php?wp=<?php echo AM_WEBPAGE_NAME;?>&amp;wikipage=<?php echo $barnraiser_wiki_page['wikipage_name'];?>">AM_BLOCK_LANGUAGE_CURRENT_REVISION</a>
			<?php }?>

			<?php
			if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_wiki']['edit_page']) {
			?>
				<a href="index.php?p=barnraiser_wiki&amp;t=edit_wikipage&amp;wikipage=<?php echo $barnraiser_wiki_page['wikipage_name'];?>&amp;wp=<?php echo AM_WEBPAGE_NAME;?>" class="edit">AM_BLOCK_LANGUAGE_EDIT</a>
			<?php
			}
			else {
			?>
			<span class="disabled_link" onclick="javascript:showInterfaceSystemMessage(event, 'no_wiki_edit_title', 'no_wiki_edit_message');">AM_BLOCK_LANGUAGE_EDIT</span>
			<span style="display:none;">
				<span id="no_wiki_edit_title">AM_BLOCK_LANGUAGE_PERMISSION_PROBLEM</span>
				<span id="no_wiki_edit_message">
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
			if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_wiki']['manage_wiki']) {
			?>
				<a href="index.php?p=barnraiser_wiki&amp;t=maintain&amp;wp=<?php echo AM_WEBPAGE_NAME;?>" class="maintain">AM_BLOCK_LANGUAGE_MAINTAIN</a>
			<?php }?>
		</div>
	</div>

	<?php
	if(isset($barnraiser_wiki_page['wikipage_id']) && !empty($barnraiser_wiki_page['wikipage_allow_note'])) {
	?>
	<div class="notes">
		<div class="block">
			<div class="block_body">
				<?php
				if (isset($barnraiser_wiki_notes)) {
				?>
				
				<?php
				foreach ($barnraiser_wiki_notes as $key => $i):
				?>
				<a name="note_id<?php echo $i['note_id'];?>"></a>

				<div id="note_id<?php echo $i['note_id'];?>">
					<div class="note">
						<form action="plugins/barnraiser_wiki/add_del_note.php" method="post">
				        <input type="hidden" name="note_id" value="<?php echo $i['note_id'];?>" />
						
						<div class="note_header">
							<?php echo strftime("%d %b %G %H:%M", $i['note_create_datetime']);?>
							&nbsp;
							<a href="index.php?t=network&amp;connection_id=<?php echo $i['connection_id'];?>" class="connection_id"><?php echo $i['connection_nickname']?></a>
							<br />
						</div>
	
						<div class="note_body">
							<?php echo $i['note_body'];?>
						</div>
						
						<div class="note_footer">
							<?php
							if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_wiki']['manage_wiki']) {
							?>
							<input type="submit" name="delete_note" value="AM_BLOCK_LANGUAGE_DELETE_NOTE" />
							<?php }?>
						</div>
						</form>
					</div>
				</div>
				<?php
				endforeach;
				}
				else {
				?>
					<p>
				        AM_BLOCK_LANGUAGE_NO_NOTES
					</p>
				<?php }?>
			</div>

			<form action="plugins/barnraiser_wiki/add_del_note.php?wp=<?php echo AM_WEBPAGE_NAME;?>" method="post">
			<?php
			if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_wiki']['add_note']) {
			?>
				<div class="add">
					<input type="hidden" name="wikipage_id" value="<?php echo $barnraiser_wiki_page['wikipage_id'];?>" />
					<input type="hidden" name="wikipage_name" value="<?php echo $barnraiser_wiki_page['wikipage_name'];?>" />
					<textarea name="note_body" id="note_body" cols="80" rows="5"></textarea>
				</div>
			<?php }?>

			
			<div class="block_footer">
				<?php
				if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_wiki']['add_note']) {
				?>
					<input type="submit" name="insert_note" value="AM_BLOCK_LANGUAGE_ADD_NOTE" />
				<?php
				}
				else {
				?>
					<span class="disabled_link" onclick="javascript:showInterfaceSystemMessage(event, 'no_wiki_note_add_title', 'no_wiki_note_add_message');">AM_BLOCK_LANGUAGE_ADD_NOTE</span>
					<span style="display:none;">
						<span id="no_wiki_note_add_title">AM_BLOCK_LANGUAGE_PERMISSION_PROBLEM</span>
						<span id="no_wiki_note_add_message">
							AM_BLOCK_LANGUAGE_PERMISSION_PROBLEM_NOTE_INTRO
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
			</div>
			</form>
		</div>
	</div>
	<?php }?>
</div>