<div class="barnraiser_blog_entry">
	<div class="block">
		<div class="block_body">
			<?php
			if(isset($barnraiser_blog_entry)) {
			?>
			<p>
				<b class="title"><?php echo ucfirst($barnraiser_blog_entry['blog_title']);?></b>
			</p>

			<p>
				<span class="body"><?php echo $barnraiser_blog_entry['blog_body'];?></span>
			</p>

			<p>
				<span class="datetime"><?php echo strftime("%d %b %G", $barnraiser_blog_entry['blog_create_datetime']);?></span>

				<?php
				if (!empty($barnraiser_blog_entry['blog_edit_datetime'])) {
				?>
				 (AM_BLOCK_LANGUAGE_LAST_EDIT_DATETIME: <span class="datetime"><?php echo strftime("%d %b %G %H:%M", $barnraiser_blog_entry['blog_edit_datetime']);?></span>) 
				<?php }?>
					
				<a href="index.php?t=network&amp;connection_id=<?php echo $barnraiser_blog_entry['connection_id'];?>" class="connection_id"><?php echo $barnraiser_blog_entry['connection_nickname']?></a>
			</p>
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
			if (isset($_SESSION['connection_id']) && isset($barnraiser_blog_entry['connection_id']) && $_SESSION['connection_id'] == $barnraiser_blog_entry['connection_id']) {
			if ($_SESSION['connection_permission'] & $plugin_permissions['barnraiser_blog']['add_blog_entry']) {
			?>
				<a href="index.php?p=barnraiser_blog&amp;t=edit_blog&amp;wp=<?php echo AM_WEBPAGE_NAME;?>&amp;blog_id=<?php echo $barnraiser_blog_entry['blog_id'];?>" class="edit">AM_BLOCK_LANGUAGE_EDIT</a>&nbsp;
			<?php
			}
			else {
			?>
			<span class="disabled_link" onclick="javascript:showInterfaceSystemMessage(event, 'no_blog_edit_title', 'no_blog_edit_message');">AM_BLOCK_LANGUAGE_EDIT</span>&nbsp;
			<span style="display:none;">
				<span id="no_blog_edit_title">AM_BLOCK_LANGUAGE_PERMISSION_PROBLEM</span>
				<span id="no_blog_edit_message">
					<?php
					if (isset($_SESSION['connection_id'])) {
						$connection_txt = 'AM_BLOCK_LANGUAGE_ACCOUNT_LINK_EDIT';
						$connection_txt = str_replace('SYS_KEYWORD_CONNECTION_ID', $_SESSION['connection_id'], $connection_txt);
						echo $connection_txt;
					}?>
				</span>
			</span>
			<?php }?>
			<?php }?>

			<?php
			if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_blog']['add_blog_entry']) {
			?>
				<a href="index.php?p=barnraiser_blog&amp;t=edit_blog&amp;wp=<?php echo AM_WEBPAGE_NAME;?>" class="add">AM_BLOCK_LANGUAGE_ADD</a>
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
			if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_blog']['manage_blog']) {
			?>
				<a href="index.php?p=barnraiser_blog&amp;t=maintain&amp;wp=<?php echo AM_WEBPAGE_NAME;?>" class="maintain">AM_BLOCK_LANGUAGE_MAINTAIN</a>
			<?php }?>
		</div>
	</div>


	<?php
	if(isset($barnraiser_blog_entry)) {
	?>
	<div class="share">
		<div class="block">
			<div class="block_body">
				<h2>AM_BLOCK_LANGUAGE_SHARE_TITLE</h2>
		
				<?php
				$url = str_replace('REPLACE', AM_WEBSPACE_NAME, $domain_replace_pattern);
				$url .= "index.php?wp=" . AM_WEBPAGE_NAME . "&blog_id=" . $barnraiser_blog_entry['blog_id'];
				$url = urlencode($url);
				?>
				<ul>
					<li>
						<a href="http://del.icio.us/post?url=<?php echo $url;?>&amp;title=<?php echo urlencode($barnraiser_blog_entry['blog_title']);?>"><img src="plugins/barnraiser_blog/template/img/delicious.png" alt="del.icio.us logo" border="0" /></a>
						<a href="http://del.icio.us/post?url=<?php echo $url;?>&amp;title=<?php echo urlencode($barnraiser_blog_entry['blog_title']);?>">AM_BLOCK_LANGUAGE_SHARE_DELICIOUS</a>
					</li>

					<li>
					<a href="http://digg.com/submit?phase=2&amp;url=<?php echo $url;?>&amp;title=<?php echo urlencode($barnraiser_blog_entry['blog_title']);?>"><img src="plugins/barnraiser_blog/template/img/digg.png" alt="Digg logo" border="0" /></a>
					<a href="http://digg.com/submit?phase=2&amp;url=<?php echo $url;?>&amp;title=<?php echo urlencode($barnraiser_blog_entry['blog_title']);?>">AM_BLOCK_LANGUAGE_SHARE_DIGG</a>
					</li>
					
					<li>
					<a href="http://www.stumbleupon.com/submit?url=<?php echo $url;?>&amp;title=<?php echo urlencode($barnraiser_blog_entry['blog_title']);?>"><img src="plugins/barnraiser_blog/template/img/stumbleupon.png" alt="StumbleUpon logo" border="0" /></a>
					<a href="http://www.stumbleupon.com/submit?url=<?php echo $url;?>&amp;title=<?php echo urlencode($barnraiser_blog_entry['blog_title']);?>">AM_BLOCK_LANGUAGE_SHARE_STUMBLEUPON</a>
					</li>
					
					<li>
					<a href="http://www.technorati.com/faves?add=<?php echo $url;?>&amp;title=<?php echo urlencode($barnraiser_blog_entry['blog_title']);?>"><img src="plugins/barnraiser_blog/template/img/technorati.png" alt="Technorati logo" border="0" /></a>
					<a href="http://www.technorati.com/faves?add=<?php echo $url;?>&amp;title=<?php echo urlencode($barnraiser_blog_entry['blog_title']);?>">AM_BLOCK_LANGUAGE_SHARE_TECHNORATI</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<?php }?>
	

	<?php
	if (isset($barnraiser_blog_entry['blog_allow_comment']) && $barnraiser_blog_entry['blog_allow_comment'] == 1) {
	?>
	<div class="comments">
		<div class="block">
			<div class="block_body">
				<?php
				if (isset($barnraiser_blog_comments)) {
				?>
				
				<?php
				foreach ($barnraiser_blog_comments as $key => $i):
				?>
				<a name="comment_id<?php echo $i['comment_id'];?>"></a>

				<div id="comment_id<?php echo $i['comment_id'];?>">
					<div class="comment">
						<form action="plugins/barnraiser_blog/add_hide_comment.php" method="post">
				        <input type="hidden" name="comment_id" value="<?php echo $i['comment_id'];?>" />
						
						<div class="comment_header">
							<span class="datetime"><?php echo strftime("%d %b %G %H:%M", $i['comment_create_datetime']);?></span>
							&nbsp;
							<a href="index.php?t=network&amp;connection_id=<?php echo $i['connection_id'];?>" class="connection_id"><?php echo $i['connection_nickname']?></a>
							<br />
						</div>
	
						<div class="comment_body">
							<?php echo $i['comment_body'];?>
						</div>
						
						<div class="comment_footer">
							<?php
							if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_blog']['comment_filter']) {
							if (empty($i['comment_hidden'])) {
							?>
							<input type="submit" name="hide_comment" value="AM_BLOCK_LANGUAGE_COMMENT_HIDE" />
							<?php
							}
							else {
							?>
							<input type="submit" name="show_comment" value="AM_BLOCK_LANGUAGE_COMMENT_SHOW" />
							<?php }}?>
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
				        AM_BLOCK_LANGUAGE_NO_COMMENTS
					</p>
				<?php }?>
			
				<?php
				if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_blog']['add_comment']) {
				?>
				<div class="add">
					<form action="plugins/barnraiser_blog/add_hide_comment.php?wp=<?php echo AM_WEBPAGE_NAME;?>" method="post">
					<input type="hidden" name="blog_id" value="<?php echo $barnraiser_blog_entry['blog_id'];?>" />

					<a name="add_comment_form"></a>
					<textarea name="comment_body" id="comment_body" cols="80" rows="5"></textarea>
 	
					<input type="submit" name="insert_comment" value="AM_BLOCK_LANGUAGE_ADD_COMMENT" /><br />
 	
					</form>
				</div>
				<?php }?>
			</div>

			<?php
			if (!isset($_SESSION['connection_permission']) || !($_SESSION['connection_permission'] & $plugin_permissions['barnraiser_blog']['add_comment'])) {
			?>
			<div class="block_footer">
				<span class="disabled_link" onclick="javascript:showInterfaceSystemMessage(event, 'no_blog_comment_add_title', 'no_blog_comment_add_message');">AM_BLOCK_LANGUAGE_ADD_COMMENT</span>&nbsp;
				<span style="display:none;">
					<span id="no_blog_comment_add_title">AM_BLOCK_LANGUAGE_PERMISSION_PROBLEM</span>
					<span id="no_blog_comment_add_message">
						AM_BLOCK_LANGUAGE_PERMISSION_PROBLEM_COMMENT_INTRO
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
			</div>
			<?php }?>
		</div>
	</div>
	<?php } ?>
</div>