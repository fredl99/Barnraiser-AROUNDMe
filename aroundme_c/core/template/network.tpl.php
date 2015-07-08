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

<?php
if (isset($_SESSION['connection_id'])) {
if (isset($display) && $display == 'applicants') {
?>
<div id="col_left_50">
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('core_applicants');?></h1>
		</div>

		<div class="box_body">
			<?php
			if (isset($webspace_applicants)) {
			?>
			<table cellspacing="0" cellpadding="4" border="0" width="100%">
				<tr>
					<td valign="top">
						<b><?php $this->getLanguage('core_name');?></b>
					</td>
					<td valign="top">
						<b><?php $this->getLanguage('common_openid');?></b>
					</td>
				</tr>
				<?php
				foreach ($webspace_applicants as $key => $i):
				?>
				<tr>
					<td valign="top">
						<a href="index.php?t=network&amp;applicant_id=<?php echo $i['applicant_id'];?>"><?php echo $i['applicant_nickname'];?></a>
					</td>
					<td valign="top">
						<a href="<?php echo $i['applicant_openid'];?>" target="_new"><?php echo $i['applicant_openid'];?></a>
					</td>
				</tr>
				<?php
				endforeach;
				?>
			</table>
			<?php
			}
			else {
			?>
			<p>
				<?php $this->getLanguage('common_no_list_items');?>
			</p>
			<?php }?>
		</div>
	</div>
</div>

<div id="col_right_50">
	<form action="index.php?t=network" method="post">
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('core_applicant_details');?></h1>
		</div>

		<div class="box_body">
			<?php
			if (isset($webspace_applicant)) {
			?>
			<input type="hidden" name="applicant_id" value="<?php echo $webspace_applicant['applicant_id'];?>" />

			<table cellspacing="0" cellpadding="6">
				<tr>
					<td valign="top">
						<b><?php $this->getLanguage('common_nickname');?></b>
					</td>
					<td valign="top">
						<?php echo $webspace_applicant['applicant_nickname'];?>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<b><?php $this->getLanguage('common_openid');?></b>
					</td>
					<td valign="top">
						<a href="<?php echo $webspace_applicant['applicant_openid'];?>" target="_new"><?php echo $webspace_applicant['applicant_openid'];?></a>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<b><?php $this->getLanguage('common_email');?></b>
					</td>
					<td valign="top">
						<a href="mailto:<?php echo $webspace_applicant['applicant_email'];?>"><?php echo $webspace_applicant['applicant_email'];?></a>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<b><?php $this->getLanguage('common_note');?></b>
					</td>
					<td valign="top">
						<?php echo $webspace_applicant['applicant_note'];?>
					</td>
				</tr>
			</table>

			<p>
				<label for="id_response"><?php $this->getLanguage('core_response');?></label><br />
				<?php
				$responce_message = $this->lang['core_response_message'];
				$responce_message = str_replace('SYS_KEYWORD_NICKNAME', $webspace_applicant['applicant_nickname'], $responce_message);
				$url = str_replace('REPLACE', AM_WEBSPACE_NAME, $domain_replace_pattern);
				$responce_message = str_replace('SYS_KEYWORD_URL', $url, $responce_message);
				$responce_message = str_replace('SYS_KEYWORD_OPENID_NICKNAME', $_SESSION['openid_nickname'], $responce_message);
				$responce_message = str_replace('SYS_KEYWORD_OPENID', $webspace_applicant['applicant_openid'], $responce_message);
				?>

				<textarea id="id_response" cols="50" rows="6" name="response_email"><?php echo $responce_message;?></textarea>
			</p>

			<p align="right">
				<input type="submit" name="deny_applicant" value="<?php $this->getLanguage('core_deny_access');?>" />&nbsp;
				<input type="submit" name="accept_applicant" value="<?php $this->getLanguage('core_allow_access');?>" />
			</p>
			<?php
			}
			else {
			?>
			<p>
				<?php $this->getLanguage('core_applications_intro');?>
			</p>
			<?php }?>
		</div>
	</div>
	</form>
</div>

<?php
}
elseif (isset($display) && $display == 'permissions') {
?>

<div id="col_left_70">
	<?php
	if (isset($plugin_permissions) && isset($arr_group)) {
	?>
	<form action="index.php?t=network" method="post">
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('code_plugin_permissions');?></h1>
		</div>

		<div class="box_body">

			<table cellspacing="0" cellpadding="6" border="0" width="100%">
				<?php
				foreach ($plugin_permissions as $key => $i):
				?>
				<tr>
					<td colspan="<?php echo count($arr_group)+1;?>">
						<b>
						<?php
						if (isset($this->lang['plugin_'.$key]['txt_plugin_title'])) {
							echo  $this->lang['plugin_'.$key]['txt_plugin_title'];
						}
						else {
							echo $key;
						}
						?></b>
					</td>
				</tr>
				<tr>
					<td><br /></td>
					<?php
					foreach ($arr_group as $keyg => $g):
					?>
					<td align="center">
						<?php
						if (isset($this->lang['arr_group_name'][$keyg])) {
							echo  $this->lang['arr_group_name'][$keyg];
						}
						else {
							echo $keyg;
						}
						?>
					</td>
					<?php
					endforeach;
					?>
				</tr>
				<?php
				foreach ($i as $keyr => $r): // resources
				?>
				<tr>
					<td>
						<?php
						if (isset($this->lang['plugin_'.$key]['resource'][$keyr])) {
							echo  $this->lang['plugin_'.$key]['resource'][$keyr];
						}
						else {
							echo $keyr;
						}
						?>
					</td>
					<?php
					foreach ($arr_group as $keyg => $g):
					?>
					<td align="center">
						<?php
						$checked = "";

						if ($r & $g) {
							$checked = " checked=\"checked\"";
						}
						?>

						<input type="checkbox" name="plugin_permissions[<?php echo $key;?>][<?php echo $keyr;?>][<?php echo $keyg;?>]" value="<?php echo $g;?>"<?php echo $checked;?> />
					</td>
					<?php
					endforeach;
					?>
				</tr>
				<?php
				endforeach;
				endforeach;
				?>
			</table>

			<p align="right">
				<input type="submit" name="update_plugin_permissions" value="<?php $this->getLanguage('common_save');?>" />
			</p>
		</div>
	</div>
	</form>
	<?php }?>
</div>

<div id="col_right_30">
	<form action="index.php?t=network" method="post">
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('core_default_permissions');?></h1>
		</div>

		<div class="box_body">

			<table cellspacing="0" cellpadding="4" border="0" width="100%">
				<?php
				foreach ($arr_group as $key => $i):
				?>
				<tr>
					<td valign="top">
						<?php
						if (isset($this->lang['plugin_'.$key]['txt_plugin_title'])) {
							echo  $this->lang['plugin_'.$key]['txt_plugin_title'];
						}
						else {
							echo $key;
						}
						?>
					</td>
					<td valign="top" align="right">
						<?php
						$checked = "";

						if (intval($webspace['default_permission']) & intval($i)) {
							$checked = " checked=\"checked\"";
						}
						?>
						<input type="checkbox" name="bitwise_operators[]" value="<?php echo $i;?>" <?php echo $checked;?> style="margin:2px;" />
					</td>
				</tr>
				<?php
				endforeach;
				?>
			</table>

			<p align="right">
				<input type="submit" name="update_default_permission" value="<?php $this->getLanguage('common_save');?>" />
			</p>

			<ul>
				<li><a href="index.php?t=network"><?php $this->getLanguage('common_list');?></a></li>
			</ul>
		</div>
	</div>
	</form>
</div>

<?php
}
elseif (isset($connection)) {
?>

<script type="text/javascript">

function fetchPopLog(identity) {
	str = 'core/get_xml.php';
	p = 'file=' + identity+'/aroundme.xml';

	makeRequest(str, p, displayPopLog);
}


function makeRequest(url, parameters, destination) {

	http_request = false;

	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			// set type accordingly to anticipated content type
			http_request.overrideMimeType('text/xml');
			//http_request.overrideMimeType('text/html');
		}
	}
	else if (window.ActiveXObject) { // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e) {
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) {
			}
		}
	}

	if (!http_request) {
		alert('Cannot create XMLHTTP instance');
		return false;
	}
	http_request.onreadystatechange = destination;
	http_request.open('POST', url, true);
	http_request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	http_request.send(parameters);
}


function displayPopLog() {
	if (http_request.readyState == 4) {
		if (http_request.status == 200) {
			xmlDoc = http_request.responseXML;

			failure = xmlDoc.getElementsByTagName('failure');

			if (failure.length == 0) {
				friends = xmlDoc.getElementsByTagName('inbound');
				number_of_friends = friends.length;

				//we can also get each friend (your friends friends) and perhaps display their name/id in a list
				//alert(friends[0].getElementsByTagName('identity')[0].firstChild.nodeValue);

				log_entries = xmlDoc.getElementsByTagName('logentry');
				number_of_log_entries = log_entries.length;

				nr_of_connectons_outbound = 0;
				is_vouched_outbound = false;
				reference_outbound = false;

				for(k=0; k < number_of_friends; k++) {
					if (friends[k].getElementsByTagName('identity')[0].firstChild.nodeValue == "<?php echo $_SESSION['openid_identity']; ?>") {
					
						if (friends[k].getElementsByTagName('connections')[0].firstChild.nodeValue != "undefined") {
							nr_of_connectons_outbound = friends[k].getElementsByTagName('connections')[0].firstChild.nodeValue;
						}
					
						if (friends[k].getElementsByTagName('is_vouched')[0].firstChild.nodeValue != "undefined") {
							is_vouched_outbound = friends[k].getElementsByTagName('is_vouched')[0].firstChild.nodeValue;
						}
					
						if (is_vouched_outbound != 0) {
							if (friends[k].getElementsByTagName('reference')[0].firstChild.nodeValue != "undefined") {
								reference_outbound = friends[k].getElementsByTagName('reference')[0].firstChild.nodeValue;
							}
						}
					}
				}

				nickname = xmlDoc.getElementsByTagName('me_nickname')[0].firstChild.nodeValue;

				output = "";

				if (number_of_log_entries > 0) {
					output += "<ul>";
					for(i = number_of_log_entries-1; i >= Math.max(0, number_of_log_entries-11); i--) {
						datetime = log_entries[i].getElementsByTagName('datetime')[0].firstChild.nodeValue;

						/*dateObj = new Date(datetime * 1000);
						dateformat = dateObj.getDate() + '/' + dateObj.getMonth() + ' ' + dateObj.getHours() + ':' + dateObj.getMinutes();*/

						entry = log_entries[i].getElementsByTagName('entry')[0].firstChild.nodeValue;
						output += "<li>" + datetime + ": " + entry + "</li>";
					}
					output += "</ul>";
				}
				else {
					output += "<p><?php $this->getLanguage('core_no_entries');?></p>";
				}

			}
			else { // there was a failure
				output = "<p><?php $this->getLanguage('core_no_network');?></p>"
			}
			document.getElementById('connection_poplog').innerHTML = output;
		}
		else {
			alert('<?php $this->getLanguage('core_request_error');?>');
		}
	}
}
</script>

<div id="col_left_50">
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('core_identity');?></h1>
		</div>

		<div class="box_body">
			<table cellspacing="0" cellpadding="4" border="0" width="100%">
				<tr>
					<?php
					if (isset($connection['connection_avatar'])) {
					?>
					<td width="100" valign="top">
						<img src="<?php echo $connection['connection_avatar'];?>" width="100" height="100" alt="avatar" />
					</td>
					<?php }?>
					<td valign="top">

						<table cellspacing="0" cellpadding="4" border="0">
							<tr>
								<td valign="top">
									<b><?php $this->getLanguage('common_openid');?></b>
								</td>
								<td valign="top">
									<a href="<?php echo $connection['connection_openid'];?>"><?php echo $connection['connection_openid'];?></a>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<b><?php $this->getLanguage('common_nickname');?></b>
								</td>
								<td valign="top">
									<?php echo $connection['connection_nickname'];?>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<b><?php $this->getLanguage('common_email');?></b>
								</td>
								<td valign="top">
									<?php echo $connection['connection_email'];?>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<b><?php $this->getLanguage('common_fullname');?></b>
								</td>
								<td valign="top">
									<?php echo $connection['connection_fullname'];?>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<b><?php $this->getLanguage('common_country');?></b>
								</td>
								<td valign="top">
									<?php
									if (isset($connection['connection_country'])) {
										$connection['connection_country'] = strtoupper($connection['connection_country']);

										if (isset($this->lang['arr_identity_field']['country'][$connection['connection_country']])) {
											echo $this->lang['arr_identity_field']['country'][$connection['connection_country']];
										}
										else {
											echo $connection['connection_country'];
										}
									}
									?>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<b><?php $this->getLanguage('common_language');?></b>
								</td>
								<td valign="top">
									<?php
									if (isset($connection['connection_language'])) {
										$connection['connection_language'] = strtoupper($connection['connection_language']);

										if (isset($this->lang['arr_identity_field']['language'][$connection['connection_language']])) {
											echo $this->lang['arr_identity_field']['language'][$connection['connection_language']];
										}
										else {
											echo $connection['connection_language'];
										}
									}
									?>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<b><?php $this->getLanguage('common_create_datetime');?></b>
								</td>
								<td valign="top">
									<?php echo strftime("%d %b %G %H:%M", $connection['connection_create_datetime']);?>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<b><?php $this->getLanguage('common_last_datetime');?></b>
								</td>
								<td valign="top">
									<?php echo strftime("%d %b %G %H:%M", $connection['connection_last_datetime']);?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>

			<ul>
				<li><a href="index.php?t=network"><?php $this->getLanguage('common_list');?></a></li>
			</ul>
		</div>
	</div>


	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('common_am_menu_account');?></h1>
		</div>

		<div class="box_body">
			<p>
				<?php $this->getLanguage('core_group_membership_intro');?>
			</p>

			<ul>
				<?php
				foreach ($arr_group as $keyg => $g):
				if ($connection['connection_permission'] & $g) {
				?>
					<li>
					<?php
					if (isset($this->lang['arr_group_name'][$keyg])) {
						echo  $this->lang['arr_group_name'][$keyg];
					}
					else {
						echo $keyg;
					}
					?>
					</li>
				<?php
				}
				endforeach;
				?>
			</ul>

			<p>
				<?php $this->getLanguage('core_permissions_allow_levels');?>
			</p>

			<table cellspacing="0" cellpadding="4" border="0" width="100%">
				<tr>
					<td valign="top">
						<h2><?php $this->getLanguage('core_permissions_allowed');?></h2>

						<ul>
							<?php
							foreach ($plugin_permissions as $key => $i):
							foreach ($i as $keyr => $r): // resources
							if ($r & $connection['connection_permission']) {
							?>

							<li>
								<?php
								if (isset($this->lang['plugin_'.$key]['resource'][$keyr])) {
									echo  $this->lang['plugin_'.$key]['resource'][$keyr];
								}
								else {
									echo $keyr;
								}
								?>
							</li>

							<?php
							unset($plugin_permissions[$key][$keyr]);
							}
							endforeach;
							endforeach;
							?>
						</ul>
					</td>
					<td valign="top">
						<h2><?php $this->getLanguage('core_permissions_denied');?></h2>

						<ul>
							<?php
							foreach ($plugin_permissions as $key => $i):
							foreach ($i as $keyr => $r): // resources
							?>

							<li>
								<?php
								if (isset($this->lang['plugin_'.$key]['resource'][$keyr])) {
									echo  $this->lang['plugin_'.$key]['resource'][$keyr];
								}
								else {
									echo $keyr;
								}
								?>
							</li>

							<?php
							endforeach;
							endforeach;
							?>
						</ul>
					</td>
				</tr>
			</table>

			<p>
				<?php $this->getLanguage('core_permissions_notes');?>
			</p>
		</div>
	</div>

	<?php
	if (isset($arr_group) && isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $arr_group['maintainer']) {
	?>

	<form action="index.php?t=network" method="post">
	<input type="hidden" name="connection_id" value="<?php echo $connection['connection_id'];?>" />

	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('core_group_allocation');?></h1>
		</div>

		<div class="box_body">
			<table cellspacing="0" cellpadding="4" border="0" width="100%">
				<?php
				foreach ($arr_group as $key => $i):
				$checked = "";
				if (intval($connection['connection_permission']) & intval($i)) {
					$checked = " checked=\"checked\"";
				}
				?>
				<tr>
					<td valign="top">
						<?php
						if (isset($this->lang['arr_group_name'][$key])) {
							echo  $this->lang['arr_group_name'][$key];
						}
						else {
							echo $key;
						}
						?>
					</td>
					<td valign="top" width="1">
						<input type="checkbox" name="bitwise_operators[]" value="<?php echo $i;?>" <?php echo $checked;?> style="margin:2px;" />
					</td>
				</tr>
				<?php
				endforeach;
				?>
			</table>

			<p align="right">
				<input type="submit" name="update_connection" value="<?php $this->getLanguage('common_save');?>" />
			</p>
		</div>
	</div>

	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('core_set_as_banned');?></h1>
		</div>

		<div class="box_body">
			<?php
			$checked = "";

			if (isset($connection['status_id']) && $connection['status_id'] == 1) { // connection status - 1 = barred, 2 = active
				$checked = " checked=\"checked\"";
			}
			?>

			<p>
				<label for="id_status_id"><?php $this->getLanguage('core_barred');?></label>
				<input id="id_status_id" type="checkbox" name="status_id" value="1"<?php echo $checked;?> />
				<br />
			</p>

			<p align="right">
				<input type="submit" name="update_connection" value="<?php $this->getLanguage('common_save');?>" />
			</p>
		</div>
	</div>
	</form>
	<?php }?>
</div>

<div id="col_right_50">
		<?php
		if (isset($_SESSION['connection_id']) && $connection['connection_id'] == $_SESSION['connection_id'] && isset($account_management_includes)) {
		?>
		<div class="box">
			<div class="box_header">
				<h1><?php $this->getLanguage('core_management_options');?></h1>
			</div>

			<div class="box_body">
				<?php
				if (isset($account_management_includes)) {
					foreach ($account_management_includes as $key => $i):
						if (is_file('plugins/' . $i . '/template/inc/account_manage.inc.tpl.php')) {
							include_once('plugins/' . $i . '/template/inc/account_manage.inc.tpl.php');
						}
					endforeach;
				}
				?>
			</div>
		</div>
		<?php }?>
	
		<div class="box">
			<div class="box_header">
				<h1><?php $this->getLanguage('core_contributions');?></h1>
			</div>

			<div class="box_body">
				<?php
				if (isset($contribution_includes)) {
					foreach ($contribution_includes as $key => $i):
						if (is_readable('plugins/' . $i . '/template/inc/account_contributions.inc.tpl.php')) {
							include_once('plugins/' . $i . '/template/inc/account_contributions.inc.tpl.php');
						}
					endforeach;
				}
				?>
			</div>
		</div>


		<div class="box">
			<div class="box_header">
				<h1><?php $this->getLanguage('core_log');?></h1>
			</div>

			<div class="box_body" id="connection_poplog">
				<?php $this->getLanguage('common_loading');?>...
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	fetchPopLog('<?php echo $connection['connection_openid']; ?>');
</script>

<?php
}
else {
?>
<div id="col_left_70">
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('core_connections');?></h1>
		</div>

		<div class="box_body">
			<?php
			if (isset($connections)) {
			?>
			<table cellspacing="2" cellpadding="4" border="0" width="100%">
				<tr>
					<td valign="top">
						<br />
					</td>
					<td valign="top">
						<b><?php $this->getLanguage('common_nickname');?></b>
					</td>
					<td valign="top">
						<b><?php $this->getLanguage('common_country');?></b>
					</td>
					<td valign="top">
						<b><?php $this->getLanguage('common_language');?></b>
					</td>
					<td valign="top">
						<b><?php $this->getLanguage('common_create_datetime');?></b>
					</td>
					<td valign="top">
						<b><?php $this->getLanguage('common_last_datetime');?></b>
					</td>
				</tr>
				<?php
				foreach ($connections as $key => $i):
				?>
				<tr>
					<td valign="top">
						<?php
						if (!empty($i['connection_avatar'])) {
						?>
							<a href="index.php?t=network&amp;connection_id=<?php echo $i['connection_id'];?>" class="avatar"><img src="<?php echo $i['connection_avatar'];?>" width="40" height="40" alt="" border="" /></a>
						<?php
						}
						elseif (isset($i['connection_openid'])) {
						?>
							<a href="index.php?t=network&amp;connection_id=<?php echo $i['connection_id'];?>" class="no_avatar"><div style="width:40px; height:40px;" title="<?php echo $i['connection_nickname']; ?>"></div></a>
						<?php
						}
						else {
						?>
							<div class="avatar_placeholder" style="width:40px; height:40px;"></div>
						<?php }?>
					</td>
					<td valign="top">
						<a href="index.php?t=network&amp;connection_id=<?php echo $i['connection_id'];?>"><?php echo $i['connection_nickname'];?></a>
					</td>
					<td valign="top">
						<?php
						if (isset($i['connection_country'])) {
							$i['connection_country'] = strtoupper($i['connection_country']);

							if (isset($this->lang['arr_identity_field']['country'][$i['connection_country']])) {
								echo $this->lang['arr_identity_field']['country'][$i['connection_country']];
							}
							else {
								echo $i['connection_country'];
							}
						}
						?>
					</td>
					<td valign="top">
						<?php
						if (isset($i['connection_language'])) {
							$i['connection_language'] = strtoupper($i['connection_language']);

							if (isset($this->lang['arr_identity_field']['language'][$i['connection_language']])) {
								echo $this->lang['arr_identity_field']['language'][$i['connection_language']];
							}
							else {
								echo $i['connection_language'];
							}
						}
						?>
					</td>
					<td valign="top">
						<?php echo strftime("%d %b %G %H:%M", $i['connection_create_datetime']);?>
					</td>
					<td valign="top">
						<?php
						if (isset($i['connection_last_datetime'])) {
						?>
						<?php echo strftime("%d %b %G %H:%M", $i['connection_last_datetime']);?>
						<?php }?>
						<br />
					</td>
					<td valign="top" align="right">
						<?php
						if (isset($i['invitee_connection_id'])) {
						?>
						<?php echo $i['invitee_connection_id'];?>
						<?php }?>
					</td>
				</tr>
				<?php
				endforeach;
				?>
			</table>
			<?php
			}
			else {
			?>
			<p>
				<?php echo $lang['error']['no_connections'];?>
			</p>
			<?php }?>

			<?php
   			$url = 'index.php?' . http_build_query($_GET);
   			echo $this->paging($total_nr_of_rows, $max_list_rows, $url, 'connections');
   			?>
		</div>
	</div>
</div>

<div id="col_right_30">
	<form action="index.php?t=network" method="post">
	<div class="box">
		<div class="box_header">
			<h1><?php $this->getLanguage('core_options');?></h1>
		</div>

		<div class="box_body">
			<p>
				<label for="id_search"><?php $this->getLanguage('common_search');?></label><br />
				<input type="text" name="search_text" id="id_search" value="<?php if (isset($_POST['search_text'])) { echo $_POST['search_text'];}?>" />
			</p>

			<p>
				<label for="id_filter"><?php $this->getLanguage('core_filter');?></label><br />
				<input type="radio" name="filter" value="0" checked="checked" /> All<br />
				<?php
				foreach ($arr_group as $keyg => $g):
				$checked = "";

				if (isset($_POST['filter']) && $_POST['filter'] == $g) {
					$checked = " checked=\"checked\"";
				}
				?>
				<input type="radio" name="filter" value="<?php echo $g;?>"<?php echo $checked;?> />
				<?php
				if (isset($this->lang['arr_group_name'][$keyg])) {
					echo  $this->lang['arr_group_name'][$keyg];
				}
				else {
					echo $keyg;
				}
				?><br />
				<?php
				endforeach;
				?>
			</p>

			<p align="right">
				<input type="submit" name="search" value="<?php $this->getLanguage('common_search');?>" />
			</p>

			<ul>
				<?php
				if (isset($arr_group) && isset($_SESSION['connection_permission']) && $_SESSION['connection_permission'] & $arr_group['maintainer']) {
				?>
				<li><a href="index.php?t=network&amp;v=permissions"><?php $this->getLanguage('core_manage_permissions');?></a></li>
				<?php }?>
			</ul>
		</div>
	</div>
	</form>
</div>
<?php
}
}
else {
?>

<div class="box">
	<div class="box_header">
		<h1><?php $this->getLanguage('core_connect_to_view');?></h1>
	</div>

	<div class="box_body">
		<p>
			<?php $this->getLanguage('core_connect_to_view_intro');?>
		</p>

		<ul>
			<li><a href="index.php?t=login"><?php $this->getLanguage('common_connect');?></a></li>
		</ul>
	</div>
</div>
<?php }?>