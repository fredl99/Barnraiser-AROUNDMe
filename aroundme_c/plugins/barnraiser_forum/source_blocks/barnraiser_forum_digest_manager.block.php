<div class="barnraiser_forum_digest_manager">
	<div class="block">
		<?php
		if (!empty($_SESSION['openid_email'])) {
		?>
			<form action="plugins/barnraiser_forum/set_tracking_notification.php" method="post">
			<div class="block_body">
				<p>
					<input type="radio" name="digest_frequency" value="0" id="id_freq0" checked="checked" /><label for="id_freq0" class="radio_label">AM_BLOCK_LANGUAGE_DIGEST_NEVER</label><br />
					<input type="radio" name="digest_frequency" value="1" id="id_freq1"<?php if(isset($barnraiser_forum_digest_frequency) && $barnraiser_forum_digest_frequency==1) { echo " checked=\"checked\"";}?> /><label for="id_freq1" class="radio_label">AM_BLOCK_LANGUAGE_DIGEST_DAILY</label><br />
					<input type="radio" name="digest_frequency" value="7" id="id_freq7"<?php if(isset($barnraiser_forum_digest_frequency) && $barnraiser_forum_digest_frequency==7) { echo " checked=\"checked\"";}?> /><label for="id_freq7" class="radio_label">AM_BLOCK_LANGUAGE_DIGEST_WEEKLY</label><br />
					<input type="radio" name="digest_frequency" value="30" id="id_freq30"<?php if(isset($barnraiser_forum_digest_frequency) && $barnraiser_forum_digest_frequency==30) { echo " checked=\"checked\"";}?> /><label for="id_freq30" class="radio_label">AM_BLOCK_LANGUAGE_DIGEST_MONTHLY</label>
				</p>
			</div>
	
			<div class="block_footer">
				<input type="submit" name="set_digest_frequency" value="AM_BLOCK_LANGUAGE_SET_DIGEST" />
			</div>
			</form>
		<?php
		}
		else {
		?>
			<div class="block_body">
				<p>
					AM_BLOCK_LANGUAGE_NO_DIGEST_EMAIL_SET
				</p>
			</div>
		<?php }?>
	</div>
</div>