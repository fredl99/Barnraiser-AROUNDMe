<div class="plugin_wiki_history">
	<div class="block">
		<div class="block_body">
			<?php
			if (isset($barnraiser_wiki_history)){
			?>
				<ul>
					<?php
					foreach ($barnraiser_wiki_history as $key => $i):
					$link_css = "";
	
					if (isset($_REQUEST['revision_id']) && $_REQUEST['revision_id'] == $i['revision_id']) {
						$link_css = " class=\"highlight\"";
					}
					?>
					<li>
					<span class="nickname"><a href="index.php?t=network&amp;connection_id=<?php echo $i['connection_id'];?>"<?php echo $link_css;?>><?php echo $i['connection_nickname'];?></a></span>
					<span class="revision"><a href="index.php?wp=<?php echo AM_WEBPAGE_NAME;?>&amp;revision_id=<?php echo $i['revision_id'];?>"<?php echo $link_css;?>><?php echo strftime("%d %b %G %H:%M", $i['revision_create_datetime']);?></a>
					<?php
					if ($i['current_revision_id'] == $i['revision_id']) {
						echo "*";
					}
					?></span>
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