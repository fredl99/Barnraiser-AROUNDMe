<div id="navigation">
	<ul>
		<?php
		$link_css = "";
		if (!defined('AM_WEBPAGE_NAME') || defined('AM_WEBPAGE_NAME') && AM_WEBPAGE_NAME == "home") {
		$link_css = " class=\"highlight\"";
		}
		?>
		<li><a href="index.php"<?php echo $link_css;?>>home</a></li>
		<?php
		$link_css = "";
		if (defined('AM_WEBPAGE_NAME') && AM_WEBPAGE_NAME == "blog") {
		$link_css = " class=\"highlight\"";
		}
		?>
		<li><a href="index.php?wp=blog"<?php echo $link_css;?>>blog</a></li>
		<?php
		$link_css = "";
		if (defined('AM_WEBPAGE_NAME') && AM_WEBPAGE_NAME == "forum") {
		$link_css = " class=\"highlight\"";
		}
		?>
		<li><a href="index.php?wp=forum"<?php echo $link_css;?>>forum</a></li>
		<?php
		$link_css = "";
		if (defined('AM_WEBPAGE_NAME') && AM_WEBPAGE_NAME == "wiki") {
		$link_css = " class=\"highlight\"";
		}
		?>
		<li><a href="index.php?wp=wiki"<?php echo $link_css;?>>wiki</a></li>
	</ul>
</div>

<div style="clear:both;"></div>