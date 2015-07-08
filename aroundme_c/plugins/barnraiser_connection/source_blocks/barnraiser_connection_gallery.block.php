<div class="barnraiser_connection_gallery">
    <div class="block">
        <div class="block_body">
            <?php
            if (isset($barnraiser_connection_inbound_connections)) {
            foreach ($barnraiser_connection_inbound_connections as $key => $i):
            ?>
            <div class="gallery_item">
                 <?php
                 if (!empty($i['connection_avatar'])) {
                 ?>
                     <a href="index.php?t=network&amp;connection_id=<?php echo $i['connection_id'];?>" class="avatar" title="<?php echo $i['connection_nickname']; ?>"><img src="<?php echo $i['connection_avatar'];?>" width="40" height="40" alt="" border="" /></a><br />
                 <?php
                 }
                 elseif (isset($i['connection_openid'])) {
                 ?>
                    <a href="index.php?t=network&amp;connection_id=<?php echo $i['connection_id'];?>" class="no_avatar"><div title="<?php echo $i['connection_nickname']; ?>"></div></a>
                 <?php
                 }
                 else {
                 ?>
                    <div class="avatar_placeholder"></div>
                 <?php }?>
             </div>
             <?php
             endforeach;
             }
             else {
             ?>
             <p>
                 AM_BLOCK_LANGUAGE_NO_ITEMS
             </p>
            <?php }?>
			<div style="clear:both;"></div>
        </div>

        <div class="block_footer">
        	<a href="index.php?t=network">AM_BLOCK_LANGUAGE_NETWORK_LINK</a>
        </div>
    </div>
</div>