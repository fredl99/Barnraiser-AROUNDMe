<?php
// -----------------------------------------------------------------------
// This file is part of AROUNDMe
// 
// Copyright (C) 2003-2008 Barnraiser
// http://www.barnraiser.org/
// info@barnraiser.org
// 
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; see the file COPYING.txt.  If not, see
// <http://www.gnu.org/licenses/>
// -----------------------------------------------------------------------

?>

<form action="index.php?t=overview" method="POST">

<div id="col_left_70">
	<div class="box">
		<?php
		if(isset($webspaces)) {
		?>
		<div class="box_header">
			<h1><?php $this->getLanguage('core_latest_webspaces');?></h1>
		</div>
	
		<div class="box_body">
			<ul>
				<?php
				foreach($webspaces as $g):
				?>
				<li><a href="<?php echo $g['webspace_url']; ?>"><?php echo $g['webspace_title']; ?></a>, <?php echo $g['webspace_create_datetime']; ?></li>
				<?php
				endforeach;
				?>
			</ul>
		</div>

		<?php
		}
		elseif (isset($search_webspaces)) {
		?>
		<div class="box_header">
			<h1><?php $this->getLanguage('core_search_results');?></h1>
		</div>
	
		<div class="box_body">
			<ul>
				<?php
				foreach($search_groups as $g):
				?>
				<li><b><?php $this->getLanguage('core_relevance'); ?>: </b> <?php echo $g['percentage'];?>%<br />
					<a href="<?php echo $g['webspace_url']; ?>"><?php echo $g['webspace_title']; ?></a>, <?php echo $g['webspace_create_datetime']; ?></li>
				<?php
				endforeach;
				?>
			</ul>
		</div>
	
		<?php
		}
		else {
		?>
		<div class="box_header">
			<h1><?php $this->getLanguage('common_search');?></h1>
		</div>
	
		<div class="box_body">
			<p>
				<?php $this->getLanguage('common_no_list_items');?>
			</p>
		</div>
		<?php } ?>
	</div>
</div>
		
<div id="col_right_30">
	<div class="box">
		
		<div class="box_header">
			<h1><?php $this->getLanguage('common_search');?></h1>
		</div>
	
		<div class="box_body">
			<p>
				<input type="text" name="search" />
				&nbsp;
				<input type="submit" value="<?php $this->getLanguage('common_go');?>" /><br>
			</p>

			<ul>
				<li><a href="index.php?t=overview"><?php $this->getLanguage('core_webspaces');?></a></li>
			</ul>
		</div>
	</div>

	<?php
	if ($webspace_creation_type == 1 || $webspace_creation_type == 2) {
	?>
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('core_create_webspace');?></h1>
		</div>
	
		<div class="box_body">
			<p>
				<?php $this->getLanguage('core_create_webspace_intro');?>
			</p>

			<ul>
				<li><a href="create/create.php"><?php $this->getLanguage('core_create_webspace');?></a></li>
			</ul>
		</div>
	</div>
	<?php }?>
</div>
</form>