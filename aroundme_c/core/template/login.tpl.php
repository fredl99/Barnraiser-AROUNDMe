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

<form method="post">
<input type="hidden" name="return_to" value="<?php if (isset($_REQUEST['return_to'])) { echo $_REQUEST['return_to']; } elseif (isset($_SERVER['HTTP_REFERER'])) { echo $_SERVER['HTTP_REFERER'];}?>" />

<?php
if (isset($display) && $display == 'append_connection') {
?>
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('core_append_connection');?></h1>
		</div>
		
		<div class="box_body">
			<p>
				<?php $this->getLanguage('core_append_connection_intro');?>
			</p>

			<?php
			foreach ($arr_openid_srg['required_fields'] as $key):
			if (isset($this->lang['arr_identity_field'][$key])) { // it's a dropdown
			?>
			<p>
				<label for="openid_<?php echo $key;?>"><?php $this->getLanguage('common_' . $key);?></label>

				<select id="openid_<?php echo $key;?>" name="connection_required_fields[<?php echo $key;?>]">
					<option value="0" selected="selected"><?php $this->getLanguage('txt_select_none');?></option>
					<?php
					foreach ($this->lang['arr_identity_field'][$key] as $selectkey => $s):
					?>
					<option value="<?php echo $selectkey;?>"<?php if(isset($connection['connection_' . $key]) && $connection['connection_' . $key] == $selectkey) { echo " selected=\"selected\"";}?>><?php echo $s;?></option>
					<?php
					endforeach;
					?>
				</select>
			</p>
			<?php
			}
			else {
			?>
			<p>
				<label for="openid_<?php echo $key;?>"><?php $this->getLanguage('common_' . $key);?></label>
				<input type="text" id="openid_<?php echo $key;?>" name="connection_required_fields[<?php echo $key;?>]" value="<?php if (isset($connection['connection_' . $key])) { echo $connection['connection_' . $key];}?>" />*
			</p>
			<?php
			}
			endforeach;
			?>

			<?php
			if (!empty($arr_openid_srg['optional_fields'])) {
			foreach ($arr_openid_srg['optional_fields'] as $key):
			if (isset($this->lang['arr_identity_field'][$key])) { // it's a dropdown
			?>
			<p>
				<label for="openid_<?php echo $key;?>"><?php $this->getLanguage('common_' . $key);?></label>

				<select id="openid_<?php echo $key;?>" name="connection_optional_fields[<?php echo $key;?>]">
					<option value="0" selected="selected"><?php $this->getLanguage('common_none');?></option>
					<?php
					foreach ($this->lang['arr_identity_field'][$key] as $selectkey => $s):
					?>
					<option value="<?php echo $selectkey;?>"<?php if(isset($connection['connection_' . $key]) && $connection['connection_' . $key] == $selectkey) { echo " selected=\"selected\"";}?>><?php echo $s;?></option>
					<?php
					endforeach;
					?>
				</select>
			</p>
			<?php
			}
			else {
			?>
			<p>
				<label for="openid_<?php echo $key;?>"><?php $this->getLanguage('common_' . $key);?></label>
				<input type="text" id="openid_<?php echo $key;?>" name="connection_optional_fields[<?php echo $key;?>]" value="<?php if (isset($connection['connection_' . $key])) { echo $connection['connection_' . $key];}?>" />
			</p>
			<?php
			}
			endforeach;
			}
			?>

			<p align="right">
				<input type="submit" name="update_connection" value="<?php $this->getLanguage('common_continue');?>" />
			</p>
		</div>
	</div>
<?php
}
else {
?>

	<?php 
	if (!empty($_REQUEST['register']) && isset($openid_account_registration_url)) {
	?>
		<div class="box">
			<div class="box_header">
				<h1><?php $this->getLanguage('common_register'); ?></h1>
			</div>
			
			<div class="box_body">
				<p>
					<?php $this->getLanguage('core_register_intro');?>
				</p>
					
				<?php
				$stylesheet = 'http://' . $_SERVER['SERVER_NAME'] . '/core/template/css/aroundme.css';
				$return_to = 'http://' . $_SERVER['SERVER_NAME'];
				?>
				<iframe style="height:520px;" id="remote_register" src="<?php echo $openid_account_registration_url;?>&amp;stylesheet=<?php echo $stylesheet; ?>&amp;return_to=<?php echo $return_to; ?>"></iframe>
			</div>
		</div>
	<?php
	}
	else {
	?>
		<div id="col_left_50">
			<div class="box">
				<div class="box_header">
					<h1><?php $this->getLanguage('common_connect');?></h1>
				</div>
			
				<div class="box_body">
					<p>
						<?php $this->getLanguage('core_connect_intro');?>
					</p>
					
					<p>
						<label for="openid_login"><?php $this->getLanguage('common_openid');?></label>
						<input type="text" id="openid_login" name="openid_login" value="http://example.domain.org" onFocus="this.value=''; return false;" />
					</p>
				
					<p align="right">
						<input type="submit" name="connect" value="<?php $this->getLanguage('common_connect');?>" />
						<input type="hidden" name="connect" value="1"/>
					</p>
				</div>
			</div>
		</div>
		
		<div id="col_right_50">
			<?php
			if (!empty($openid_account_registration_url)) {
			?>
			<div class="box">
				<div class="box_header">
					<h1><?php $this->getLanguage('common_register');?></h1>
				</div>
	
				<div class="box_body">
	
					<p>
						<?php $this->getLanguage('core_register_intro');?>
					</p>
	
					<ul>
						<li><a href="index.php?t=login&amp;register=1"><?php $this->getLanguage('common_register');?></a></li>
					</ul>
				</div>
			</div>
			<?php }?>

			<div class="box">
				<div class="box_header">
					<h1><?php $this->getLanguage('core_get_openid');?></h1>
				</div>
	
				<div class="box_body">
					<p>
						<?php $this->getLanguage('core_get_openid_intro');?>
					</p>
				</div>
			</div>
		</div>
	<?php }?>
<?php }?>
</form>