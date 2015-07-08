<div class="barnraiser_contact_email">
    <div class="block">
		<form method="post">
        <div class="block_body">
        	<?php
        	if (isset($barnraiser_contact_email_ok)) {
            	if (isset($barnraiser_contact_email_sent)) {
            	?>
					<p>
						<span class="interface_message">AM_BLOCK_LANGUAGE_EMAIL_SENT</span>
					</p>
				<?php
				}
				else {
				?>
					<p>
						<label for="barnraiser_contact_id_nickname">AM_BLOCK_LANGUAGE_FROM</label>
						<input type="text" id="barnraiser_contact_id_nickname" name="barnraiser_contact_nickname" value="<?php if (isset($_POST['barnraiser_contact_nickname'])) { echo $_POST['barnraiser_contact_nickname'];}?>" />
					</p>

					<p>
						AM_BLOCK_LANGUAGE_REPLY_EMAIL_INTRO
					</p>
	
					<p>
						<label for="barnraiser_contact_id_email">AM_BLOCK_LANGUAGE_REPLY_EMAIL</label>
						<input type="text" id="barnraiser_contact_id_email" name="barnraiser_contact_email" value="<?php if (isset($_POST['barnraiser_contact_email'])) { echo $_POST['barnraiser_contact_email'];}?>" />
					</p>

					<p>
						AM_BLOCK_LANGUAGE_COPY <input type="checkbox" name="barnraiser_contact_copy" value="1"<?php if (isset($_POST['barnraiser_contact_copy'])) { echo " checked=\"checked\"";}?>  />
					</p>
	
					<p>
						<label for="barnraiser_contact_id_subject">AM_BLOCK_LANGUAGE_SUBJECT</label>
						<input type="text" id="barnraiser_contact_id_subject" name="barnraiser_contact_subject" value="<?php if (isset($_POST['barnraiser_contact_subject'])) { echo $_POST['barnraiser_contact_subject'];}?>" />
					</p>
	
					<p>
						<label for="barnraiser_contact_id_message">AM_BLOCK_LANGUAGE_MESSAGE</label>
						<textarea name="barnraiser_contact_message" id="barnraiser_contact_id_message" cols="28" rows="6"><?php if (isset($_POST['barnraiser_contact_message'])) { echo $_POST['barnraiser_contact_message'];}?></textarea>
					</p>

					<?php
					if (!isset($_SESSION['connection_id'])) {
					?>
					<h2>AM_BLOCK_LANGUAGE_CHALLENGE</h2>

					<?php
					if (isset($maptcha)) {
					?>
					<p>
						<?php echo $maptcha; ?>
					</p>
					<p>
						<label for="id_maptcha">AM_BLOCK_LANGUAGE_RESPONSE</label>
						<input type="text" name="maptcha_text" id="id_maptcha" value="" />
					</p>
					
					<p class="note">
						AM_BLOCK_LANGUAGE_CHALLENGE_EXAMPLE
					</p>
					<?php }?>
		
					<?php }?>
				<?php }?>
			<?php
            }
            else {
            ?>
				<p>
					AM_BLOCK_LANGUAGE_RECEIVER_NOT_SET
				</p>
            <?php }?>
        </div>

		 <div class="block_footer">
			<?php
        	if (isset($barnraiser_contact_email_ok) && !isset($barnraiser_contact_email_sent)) {
            ?>
			<input type="submit" name="barnraiser_contact_send_email" value="AM_BLOCK_LANGUAGE_SEND" />
			<?php }?>
			
            <?php
        	if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_contact']['manage_contact']) {
        	?>
				<a href="index.php?p=barnraiser_contact&amp;t=maintain&amp;wp=<?php echo AM_WEBPAGE_NAME;?>" class="maintain">AM_BLOCK_LANGUAGE_MAINTAIN</a>
        	<?php }?>
		 </div>
		</form>
    </div>
</div>