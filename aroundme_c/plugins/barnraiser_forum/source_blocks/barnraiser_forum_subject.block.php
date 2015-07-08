<div class="barnraiser_forum_subject">
    <div class="block">
    	<?php
    	if (isset($barnraiser_forum_subject)) {
    	?>

			<div class="block_body">
				<h1><?php echo ucfirst($barnraiser_forum_subject['subject_title']);?></h1>
	
				<p class="body">
					<?php echo $barnraiser_forum_subject['subject_body'];?>
				</p>
	
				<p>
					<a href="index.php?t=network&amp;connection_id=<?php echo $barnraiser_forum_subject['connection_id'];?>" class="connection_id"><?php echo $barnraiser_forum_subject['connection_nickname']?></a>
	
					<span class="datetime"><?php echo strftime("%d %b %G %H:%M", $barnraiser_forum_subject['subject_create_datetime']);?></span>
	
					<?php
					if (!empty($barnraiser_forum_subject['subject_edit_datetime'])) {
					?>
					(AM_BLOCK_LANGUAGE_EDIT_DATETIME <span class="datetime"><?php echo strftime("%d %b %G %H:%M", $barnraiser_forum_subject['subject_edit_datetime']);?></span>)
					<?php }?>
				</p>
			</div>
			
			<div class="block_footer">
				<?php
				if (isset($_SESSION['connection_id']) && $_SESSION['connection_id'] == $barnraiser_forum_subject['connection_id']) {
				if ($_SESSION['connection_permission'] & $plugin_permissions['barnraiser_forum']['add_subject']) {
				?>
					<a href="index.php?p=barnraiser_forum&amp;t=edit_subject&amp;wp=<?php echo AM_WEBPAGE_NAME;?>&amp;subject_id=<?php echo $barnraiser_forum_subject['subject_id'];?>">AM_BLOCK_LANGUAGE_EDIT</a>
				<?php
				}
				else {
				?>
				<span class="disabled_link" onclick="javascript:showInterfaceSystemMessage(event, 'no_subject_edit_title', 'no_subject_edit_message');">AM_BLOCK_LANGUAGE_EDIT</span>
				<span style="display:none;">
					<span id="no_subject_edit_title">AM_BLOCK_LANGUAGE_PERMISSION_PROBLEM</span>
					<span id="no_subject_edit_message">
						<?php
						if (isset($_SESSION['connection_id'])) {
							$connection_txt = 'AM_BLOCK_LANGUAGE_ACCOUNT_LINK_EDIT';
							$connection_txt = str_replace('SYS_KEYWORD_CONNECTION_ID', $_SESSION['connection_id'], $connection_txt);
							echo $connection_txt;
						}
						?>
					</span>
				</span>
				<?php }?>
				<?php }?>

				<?php
				if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_forum']['add_subject']) {
				?>
					<a href="index.php?p=barnraiser_forum&amp;t=edit_subject&amp;wp=<?php echo AM_WEBPAGE_NAME;?>" class="add">AM_BLOCK_LANGUAGE_ADD</a>
				<?php
				}
				else {
				?>
				<span class="disabled_link" onclick="javascript:showInterfaceSystemMessage(event, 'no_subject_add_title', 'no_subject_add_message');">AM_BLOCK_LANGUAGE_ADD</span>
				<span style="display:none;">
					<span id="no_subject_add_title">AM_BLOCK_LANGUAGE_PERMISSION_PROBLEM</span>
					<span id="no_subject_add_message">
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
					<a href="index.php?p=barnraiser_forum&amp;t=maintain&amp;wp=<?php echo AM_WEBPAGE_NAME;?>" class="maintain">AM_BLOCK_LANGUAGE_MAINTAIN</a>
				<?php }?>

				<?php
				if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_forum']['add_reply']) {
				?>
					<a href="#add_reply_form" onmouseover="javascript:getActiveText();" onclick="javascript:addQuote();">AM_BLOCK_LANGUAGE_QUOTE</a>
				<?php }?>
				
				<?php
				if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_forum']['manage_forum']) {
				?>
					<form action="plugins/barnraiser_forum/update_subject.php" method="post">
					<input type="hidden" name="subject_locked" value="<?php echo $barnraiser_forum_subject['subject_locked'];?>" />
					<input type="hidden" name="subject_sticky" value="<?php echo $barnraiser_forum_subject['subject_sticky'];?>" />
					<input type="hidden" name="subject_id" value="<?php echo $barnraiser_forum_subject['subject_id'];?>" />
	
					<?php
					if (!empty($barnraiser_forum_subject['subject_locked'])) {
						$sub_lock = 'AM_BLOCK_LANGUAGE_UNLOCK';
					}
					else {
						$sub_lock = 'AM_BLOCK_LANGUAGE_LOCK';
					}
	
					if (!empty($barnraiser_forum_subject['subject_sticky'])) {
						$sub_stick = 'AM_BLOCK_LANGUAGE_UNSTICK';
					}
					else {
						$sub_stick = 'AM_BLOCK_LANGUAGE_STICK';
					}
					?>
					<input type="submit" name="update_subject_locked" value="<?php echo $sub_lock;?>" />
					<input type="submit" name="update_subject_sticky" value="<?php echo $sub_stick;?>" />
					</form>
				<?php }?>

				<?php
				if (isset($_SESSION['connection_permission'])) {
				?>
					<form action="plugins/barnraiser_forum/set_tracking_notification.php" method="post">
					<input type="hidden" name="subject_id" value="<?php echo $barnraiser_forum_subject['subject_id'];?>" />
	
					<?php
					if (!empty($barnraiser_forum_subject['tracking'][0]['subject_id'])) {
					?>
					<input type="submit" name="remove_subject_tracking" value="AM_BLOCK_LANGUAGE_REMOVE_TRACKING" />
					<?php
					}
					else {
					?>
					<input type="submit" name="set_subject_tracking" value="AM_BLOCK_LANGUAGE_TRACK" />
					<input type="submit" name="set_subject_notify" value="AM_BLOCK_LANGUAGE_TRACK_NOTIFY" />
					<?php }?>
					</form>
				<?php }?>
			</div>

		<?php
		}
		else {
		?>
			<?php
			if (isset($barnraiser_forum_subjects_list)) {
			?>
				<div class="block_body">
					<table cellspacing="0" cellpadding="2" border="0" width="100%">
						<?php
						foreach($barnraiser_forum_subjects_list as $i):
						?>
						<tr>
							<td valign="top">
								<?php
								if (!empty($i['connection_avatar'])) {
								?>
									<a href="index.php?t=network&amp;connection_id=<?php echo $i['connection_id'];?>" class="avatar" title="<?php echo $i['connection_nickname'];?>"><img src="<?php echo $i['connection_avatar'];?>" width="40" height="40" alt="" border="" /></a><br />
								<?php
								}
								else {
								?>
									<a href="index.php?t=network&amp;connection_id=<?php echo $i['connection_id'];?>" class="no_avatar" title="<?php echo $i['connection_nickname'];?>"><div title="<?php echo $i['connection_nickname']; ?>"></div></a>
								<?php }?>
							</td>
							<td valign="top">
								<p><a href="index.php?wp=<?php echo AM_WEBPAGE_NAME;?>&amp;subject_id=<?php echo $i['subject_id'];?>" class="title"><?php echo $i['subject_title']; ?></a></p>

								<p class="comments">
									<?php echo str_replace('AM_SYS_KEYWORD_TOTAL', $i['tot_replies'], 'AM_BLOCK_LANGUAGE_TOTAL_REPLIES');?><br />
									<?php
									if (isset($i['latest_comment']) && !empty($i['latest_comment'])) {
										$reply_author_date = 'AM_BLOCK_LANGUAGE_REPLY_AUTHOR_DATETIME';
										$reply_author_date = str_replace ('AM_SYS_KEYWORD_CONNECTION_ID', $i['latest_comment'][0]['connection_id'], $reply_author_date);
										$reply_author_date = str_replace ('AM_SYS_KEYWORD_NICKNAME', $i['latest_comment'][0]['connection_nickname'], $reply_author_date);
										$reply_author_date = str_replace ('AM_SYS_KEYWORD_DATETIME', strftime("%d %b %G %H:%M", $i['latest_comment'][0]['reply_create_datetime']), $reply_author_date);
									}
									?>
								</p>
								
								<?php
								if (!empty($i['tags'])) {
								?>
									<p class="tags">
									AM_BLOCK_LANGUAGE_TAGS 
									<?php
									foreach($i['tags'] as $keyt => $t):
									?>
									<a class="tag" href="index.php?wp=<?php echo AM_WEBPAGE_NAME;?>&amp;tag=<?php echo $t['tag_name'];?>"><?php echo $t['tag_name'];?></a>
									<?php
									if (count($i['tags']) > $keyt+1) {
										echo ", ";
									}
									endforeach;
									?>
									</p>
								<?php }?>

								
							</td>
							<td valign="top" align="right">
								<span class="datetime"><?php echo strftime("%d %b %G %H:%M", $i['subject_create_datetime']);?></span><br />

								<?php
								if (isset($i['subject_sticky']) && $i['subject_sticky'] == '1') {
								?>
									<span class="sticky" title="AM_BLOCK_LANGUAGE_STICKY">&uarr;</span>
								<?php }?>
								<?php
								if (isset($i['subject_locked']) && $i['subject_locked'] == '1') {
								?>
									<span class="locked" title="AM_BLOCK_LANGUAGE_LOCKED">&#164;</span>
								<?php } ?>
							</td>
						</tr>
					<?php
					endforeach;
					?>
					</table>
					
					<?php
						$url = 'index.php?' . http_build_query($_GET);
						echo $this->paging($total_nr_of_rows_subjects, $max_list_rows, $url, 'subjects');
					?>
				</div>
			<?php
			}
			else {
			?>
				<div class="block_body">
					<p>
						AM_BLOCK_LANGUAGE_NO_ITEMS
					</p>
				</div>
			<?php }?>

			<div class="block_footer">
				<?php
				if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_forum']['add_subject']) {
				?>
					<a href="index.php?p=barnraiser_forum&amp;t=edit_subject&amp;wp=<?php echo AM_WEBPAGE_NAME;?>" class="add">AM_BLOCK_LANGUAGE_ADD</a>&nbsp;
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
					<a href="index.php?p=barnraiser_forum&amp;t=maintain&amp;wp=<?php echo AM_WEBPAGE_NAME;?>" class="maintain">AM_BLOCK_LANGUAGE_MAINTAIN</a>
				<?php }?>
			</div>
		<?php }?>
	</div>

	<?php
	if (isset($barnraiser_forum_subject)) {
	?>
	<div class="replies">
		<div class="block">
			<div class="block_body">
				<?php
				if (isset($barnraiser_forum_subject_replies)) {
				?>
				<div class="reply_header">
					<?php
						$g = $_GET;
						unset($g['all'], $g['recommended']);
					
						$url_normal = "index.php?" . http_build_query($g);
						$g['all'] = '1';
						$url_all = "index.php?" . http_build_query($g);
						unset($g['all']);
						$g['recommended'] = '1';
						$url_recommended = "index.php?" . http_build_query($g);
					?>
				
					<p>
						<?php if (isset($_REQUEST['recommended'])) { ?>
						
							<b><a href="<?php echo $url_normal;?>#list_replies">AM_BLOCK_LANGUAGE_RECOMMEND_NORMAL</a>&nbsp;<a href="<?php echo $url_all; ?>">AM_BLOCK_LANGUAGE_RECOMMEND_ALL</a>&nbsp;AM_BLOCK_LANGUAGE_RECOMMEND_RECOMMENDED</b>
						
						<?php } elseif (isset($_REQUEST['all'])) { ?>
							
							<b><a href="<?php echo $url_normal; ?>#list_replies">AM_BLOCK_LANGUAGE_RECOMMEND_NORMAL</a>&nbsp;all&nbsp;<a href="<?php echo $url_recommended;?>">AM_BLOCK_LANGUAGE_RECOMMEND_RECOMMENDED</a></b>
						
						<?php } else { ?>
							
							<b>normal&nbsp;<a href="<?php echo $url_all; ?>">AM_BLOCK_LANGUAGE_RECOMMEND_ALL</a>&nbsp;<a href="<?php echo $url_recommended;?>&amp;recommended=1#list_replies">AM_BLOCK_LANGUAGE_RECOMMEND_RECOMMENDED</a></b>
						
						<?php } ?>
					</p>
				</div>
				<?php }?>

				<?php
				if (isset($barnraiser_forum_subject_replies)) {
				foreach ($barnraiser_forum_subject_replies as $key => $i):
				?>
				<a name="reply_id<?php echo $i['reply_id'];?>"></a>
		
				<div id="reply_id<?php echo $i['reply_id'];?>">
					<div class="reply">
						<div class="reply_header">
						<form action="plugins/barnraiser_forum/update_subject.php" method="post">
							<span style="float:right;">
									<?php
									if (isset($i['connection_id']) && isset($_SESSION['connection_id']) && $i['connection_id'] == $_SESSION['connection_id']) {
									?>
										AM_BLOCK_LANGUAGE_RECOMMEND_RECOMMEND
									<?php
									}
									elseif (!empty($i['recommendation_connection_id'])) {
									?>
										AM_BLOCK_LANGUAGE_RECOMMEND_RECOMMENDED
									<?php
									}
									else {
									?>
										<?php
										if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_forum']['reply_recommend']) {
										?>
											<span id="reply_<?php echo $i['reply_id']; ?>_recommend"><a href="#reply_id<?php echo $i['reply_id']; ?>" onclick="recommendComment('<?php echo $i['reply_id']; ?>');">AM_BLOCK_LANGUAGE_RECOMMEND_RECOMMENDED</a></span>
										<?php
										}
										else {
										?>
											AM_BLOCK_LANGUAGE_CONNECT_TO_RECOMMEND
										<?php } ?>
									<?php } ?>
		
								(<span id="recommendation_<?php echo $i['reply_id']; ?>"><?php echo $i['total_recommendations']; ?></span>)
								
								<a href="#add_reply_form" onmouseover="javascript:getActiveText();" onclick="javascript:addQuote(<?php echo $i['reply_id'];?>, '<?php echo strftime("%d %b %G %H:%M", $i['reply_create_datetime']);?>');">
								AM_BLOCK_LANGUAGE_QUOTE
								</a>
								
								<?php
								if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_forum']['reply_filter']) {
								?>
									<?php if ($i['reply_archived'] == 1) { ?>
										<input type="submit" name="unreject[<?php echo $i['reply_id']; ?>]" value="AM_BLOCK_LANGUAGE_REJECT_REMOVE" />
									<?php } else { ?>
										<input type="submit" name="reject[<?php echo $i['reply_id']; ?>]" value="AM_BLOCK_LANGUAGE_REJECT" />
									<?php } ?>
								<?php } ?>
							</span>
							</form>
				
							<span class="datetime"><?php echo strftime("%d %b %G %H:%M", $i['reply_create_datetime']);?></span>
							
							<a href="index.php?t=network&amp;connection_id=<?php echo $i['connection_id'];?>" class="connection_id"><?php echo $i['connection_nickname']?></a>
							<br />
						</div>
					
						<div class="reply_body">
							<?php echo $i['reply_body'];?>
						</div>
					</div>
				</div>
				<?php
				endforeach;
    			$url = 'index.php?' . http_build_query($_GET);
    			echo $this->paging($total_nr_of_rows_replies, $max_list_rows, $url, 'replies');
				}
				else {
				?>
					<p>
						AM_BLOCK_LANGUAGE_NO_ITEMS
					</p>
				<?php }?>
	
	
				<div class="add">
					<?php
					if (isset($barnraiser_forum_subject['subject_locked']) && $barnraiser_forum_subject['subject_locked'] == 1) {
					?>
						<p>
							<b>AM_BLOCK_LANGUAGE_LOCKED_NO_ADD</b>
						</p>
					<?php
					}
					elseif (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_forum']['add_reply']) {
					?>
						<form action="plugins/barnraiser_forum/add_reply.php?wp=<?php echo AM_WEBPAGE_NAME;?>" method="post">
						<input type="hidden" name="subject_id" value="<?php echo $barnraiser_forum_subject['subject_id'];?>" />
					
						<a name="add_reply_form"></a>
						<textarea name="reply_body" id="reply_body" cols="80" rows="5"></textarea>
					
						<input type="submit" name="insert_reply" value="AM_BLOCK_LANGUAGE_ADD_REPLY" /><br />
					
						</form>
					<?php }?>
				</div>
			</div>

			<?php
			if (!isset($_SESSION['connection_permission']) || !($_SESSION['connection_permission'] & $plugin_permissions['barnraiser_forum']['add_reply'])) {
			?>
			<div class="block_footer">
				<span class="disabled_link" onclick="javascript:showInterfaceSystemMessage(event, 'no_subject_reply_add_title', 'no_subject_reply_add_message');">AM_BLOCK_LANGUAGE_ADD_REPLY</span>
				<span style="display:none;">
					<span id="no_subject_reply_add_title">AM_BLOCK_LANGUAGE_PERMISSION_PROBLEM</span>
					<span id="no_subject_reply_add_message">
						AM_BLOCK_LANGUAGE_PERMISSION_PROBLEM_REPLY_INTRO
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
	
	<script type="text/javascript" language="javascript">
	//<![CDATA[
	<?php if (!empty($_SESSION['connection_id'])) { ?>
		var connection_id = "<?php echo $_SESSION['connection_id']; ?>";
	<?php } ?>
	
	<?php if (!empty($barnraiser_forum_subject['subject_id'])) { ?>
		var subject_id = "<?php echo $barnraiser_forum_subject['subject_id']; ?>";
		var wp = "<?php echo AM_WEBPAGE_NAME; ?>";
	<?php } ?>
	var http_request = false;
	
	function makeRequest(url, parameters, destination) {
	
		http_request = false;
		
		if (window.XMLHttpRequest) { // Mozilla, Safari,...
			http_request = new XMLHttpRequest();
			if (http_request.overrideMimeType) {
				// set type accordingly to anticipated content type
				http_request.overrideMimeType('text/xml');
				//http_request.overrideMimeType('text/html');
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
		http_request.onreadystatechange = destination;
		http_request.open('POST', url, true); 
		http_request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		http_request.send(parameters);
	}
	
	function addQuote(id, datetime) {
		if (quotetext) {
			url = "index.php?wp=<?php echo AM_WEBPAGE_NAME; ?>&amp;subject_id=<?php echo $barnraiser_forum_subject['subject_id']; ?>";
			
			if (id) {
				/* quoting a reply */
				url += "&amp;reply_id=" + id + "#reply_id" + id;
				quote = "<blockquote cite=\"" + url + "\"><p>" + quotetext + "</p></blockquote><cite><a href=\"" + url + "\">AM_BLOCK_LANGUAGE_QUOTE_REPLY_DATETIME " + datetime + "</a></cite>";
			}
			else {
				/* quoting the main subject */
				quote = "<blockquote cite=\"" + url + "\"><p>" + quotetext + "</p></blockquote><cite><a href=\"" + url + "\">AM_BLOCK_LANGUAGE_QUOTE_REPLY_AUTHOR</a></cite>";
			}
			
			reply = document.getElementById('reply_body').value;

			if(reply != "") {
				reply += "\n\n";
			}
			
			reply += quote+"\n\n";

			document.getElementById('reply_body').value = reply;

			location.hash='#add_reply_form';
		}
		else {
			alert("AM_BLOCK_LANGUAGE_QUOTE_HIGHLIGHT");
		}
	}
	
	
	var quotetext = "";
	function getActiveText() {
		quotetext = (document.all) ? document.selection.createRange().text : document.getSelection();
	}
	
	function recommendComment(id) {
		var str = "reply_id=" + id + "&connection_id=" + connection_id + "&subject_id=" + subject_id + "&wp=" + wp;
		makeRequest('plugins/barnraiser_forum/recommend_reply.php', str, recommend_reply);
	}
	
	function recommend_reply() {
	
		if (http_request.readyState == 4) {
			if (http_request.status == 200) {
				var result = http_request.responseText.split("|");
	
				document.getElementById('recommendation_' + result[0]).innerHTML = result[1];
				document.getElementById('reply_' + result[0] + '_recommend').innerHTML = "AM_BLOCK_LANGUAGE_RECOMMEND_RECOMMENDED";
			}
		}
	}
	//]]>
	</script>
	<?php }?>
</div>