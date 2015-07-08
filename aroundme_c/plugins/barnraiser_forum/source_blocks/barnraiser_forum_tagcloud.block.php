<div class="barnraiser_forum_tagcloud">
	<div class="block">
		<div class="block_body">
			<?php
			if (isset($barnraiser_forum_tags)) {
			?>
				<?php
				$tags = "";
				$max_qty = 0;
				$number_of_styles = 5;

				foreach($barnraiser_forum_tags as $key => $t):
					if ($t['tag_total'] > $max_qty) {
						$max_qty = $t['tag_total'];
					}
				endforeach;
				
				
				foreach($barnraiser_forum_tags as $key => $t):

					if ($t['tag_total'] > 0 && $max_qty > 0) {
						$percent = floor(($t['tag_total'] / $max_qty) * 100);
					
						$tag_size = ceil(($number_of_styles/100)*$percent);
						
					}
					else {
						$tag_size = 1;
					}
				
				?>
				<a class="tag" href="index.php?wp=<?php echo $barnraiser_forum_tagcloud_wp;?>&amp;tag=<?php echo $t['tag_name'];?>"><span class="tag_size<?php echo $tag_size;?>"><?php echo $t['tag_name'];?></span></a>&nbsp;<sup><?php echo $t['tag_total'];?></sup>
				<?php
				if (count($barnraiser_forum_tags) > $key+1) {
					echo ", ";
				}
				
				endforeach;
				?>
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
			if (isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $plugin_permissions['barnraiser_forum']['manage_forum']) {
			?>
				<a href="index.php?p=barnraiser_forum&amp;t=maintain&amp;wp=<?php echo $barnraiser_forum_subjects_wp;?>" class="maintain">AM_BLOCK_LANGUAGE_MAINTAIN</a>
			<?php }?>
		</div>
	</div>
</div>