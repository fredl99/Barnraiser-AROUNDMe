<div class="barnraiser_connection_log">
    <div class="block">
        <div class="block_body">
            <?php
			if (isset($barnraiser_connection_log)) {
			?>
			<ul>
                <?php
                foreach($barnraiser_connection_log as $key => $i):
                ?>
                    <li><span class="datetime"><?php echo strftime("%d %b %H:%M", $i['log_create_datetime']);?>:</span> <span class="body"><?php echo $i['log_body'];?></span></li>
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
    </div>
</div>