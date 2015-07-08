<div class="barnraiser_forum_subject_search">
    <div class="block">
    	<form action="index.php" method="get">
    		<input type="hidden" name="wp" value="<?php echo AM_WEBPAGE_NAME;?>" />
    		<label for="id_subject_search">AM_BLOCK_LANGUAGE_SEARCH</label><br />
    		<input type="text" name="barnraiser_forum_subject_search_text" id="id_subject_search" value="<?php if (isset($_REQUEST['barnraiser_forum_subject_search_text'])) { echo $_REQUEST['barnraiser_forum_subject_search_text'];}?>" />
    		<input type="submit" value="AM_BLOCK_LANGUAGE_GO" />
    	</form>
    </div>
</div>