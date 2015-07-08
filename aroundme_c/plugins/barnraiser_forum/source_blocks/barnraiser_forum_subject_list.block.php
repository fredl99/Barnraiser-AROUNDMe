<div class="barnraiser_forum_subject_list">
    <div class="block">
        <div class="block_body">
			<?php
			if (isset($barnraiser_forum_subjects)) {
			?>
			<ul>
				<?php
				foreach ($barnraiser_forum_subjects as $key => $i):
				$link_css = "";
			
				if (isset($_REQUEST['subject_id']) && $_REQUEST['subject_id'] == $i['subject_id']) {
					$link_css = " class=\"highlight\"";
				}
				?>
				<li>
					<div class="li_avatar">
					<?php
					if (!empty($i['connection_avatar'])) {
					?>
						<a href="index.php?t=network&amp;connection_id=<?php echo $i['connection_id'];?>" class="avatar" title="<?php echo $i['connection_nickname'];?>"><img src="<?php echo $i['connection_avatar'];?>" width="40" height="40" alt="" border="" /></a>
					<?php
					}
					else {
					?>
						<a href="index.php?t=network&amp;connection_id=<?php echo $i['connection_id'];?>" class="no_avatar" title="<?php echo $i['connection_nickname'];?>"><div title="<?php echo $i['connection_nickname']; ?>"></div></a>
					<?php }?>
					</div>
					<div class="li_content">
						<a href="index.php?wp=<?php echo $i['webpage'];?>&amp;subject_id=<?php echo $i['subject_id'];?>"<?php echo $link_css;?> class="title"><?php echo $i['subject_title'];?></a><br />
						<span class="datetime"><?php echo strftime("%d %b %G %H:%M", $i['subject_create_datetime']);?></span>:
						<span class="body"><?php echo $i['subject_body'];?></span>
						<span class="more"><a href="index.php?wp=<?php echo $i['webpage'];?>&amp;subject_id=<?php echo $i['subject_id'];?>"<?php echo $link_css;?>>AM_BLOCK_LANGUAGE_MORE</a></span>
					</div>
				</li>
				<?php 
				endforeach;
				?>
			</ul>
			<div style="clear: both;"></div>
			<?php
            }
            else {
            ?>
            <p>
                AM_BLOCK_LANGUAGE_NO_ITEMS
            </p>
            <?php }?>
		</div>

		<div class="block_footer">
        	<?php
			if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_forum']['add_subject']) {
			?>
				<a href="index.php?p=barnraiser_forum&amp;t=edit_subject&amp;wp=<?php echo $barnraiser_forum_subjects_wp;?>" class="add">AM_BLOCK_LANGUAGE_ADD</a>
			<?php
			}
			else {
			?>
			<span class="disabled_link" onclick="javascript:showInterfaceSystemMessage(event, 'no_blog_add_title', 'no_blog_add_message');">AM_BLOCK_LANGUAGE_ADD</span>
			<span style="display:none;">
				<span id="no_blog_add_title">AM_BLOCK_LANGUAGE_PERMISSION_PROBLEM</span>
				<span id="no_blog_add_message">
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
			if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_forum']['manage_forum']) {
			?>
				<a href="index.php?p=barnraiser_forum&amp;t=maintain&amp;wp=<?php echo $barnraiser_forum_subjects_wp;?>" class="maintain">AM_BLOCK_LANGUAGE_MAINTAIN</a>
			<?php }?>
		</div>
    </div>
</div>