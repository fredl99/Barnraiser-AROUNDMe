<div class="barnraiser_forum_recommended_reply_list">
    <div class="block">
        <div class="block_body">
			<?php
			if (isset($barnraiser_forum_recommended_reply_list)) {
			?>
			<ul>
				<?php
				foreach ($barnraiser_forum_recommended_reply_list as $key => $i):
				?>
				<li>
					<a href="index.php?t=network&amp;connection_id=<?php echo $i['connection_id'];?>" class="connection"><?php echo $i['connection_nickname'];?></a> recommended:
					<span class="body"><?php echo $i['reply_body'];?></span>
					<a href="index.php?wp=<?php echo $i['webpage'];?>&amp;subject_id=<?php echo $i['subject_id'];?>#reply<?php echo $i['reply_id'];?>" class="datetime"><?php echo strftime("%d %b %G %H:%M", $i['recommendation_datetime']);?></a>
					<span class="total">(<?php echo $i['total_recommendations'];?>)</span>
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
    </div>
</div>