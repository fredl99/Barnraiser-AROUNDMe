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
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title><?php $this->getLanguage('common_html_title');?></title>

		<style type="text/css">
		<!--
		@import url(../core/template/css/aroundme.css);
		-->
		</style>
		
		<!--[if IE]>
		<style type="text/css">
		@import url(../core/template/css/aroundme-IE.css);
		</style>
		<![endif]-->

		<script type="text/javascript" src="../core/template/js/functions.js"></script>
	</head>
	
	<body id="am_admin">
		<?php
		if (!empty($GLOBALS['am_error_log'])) {
		?>
		<div id="error_container">
			<div class="content">
				<?php
				foreach($GLOBALS['am_error_log'] as $key => $i):
				?>
					<?php
					if (isset($this->lang['arr_am_error'][$i[0]])) {
						echo $this->lang['arr_am_error'][$i[0]];
					}
					else {
						echo $i[0];
					}

					if (!empty($i[1])) {
						echo ": " . $i[1];
					}?>
					<br />
				<?php
				endforeach;
				?>
			</div>
		</div>
		<?php }?>

		<div id="body_container">
			
			<form method="post">
			
			<?php
			if (isset($stage) && $stage < 6) {
			?>
			<div id="col_left_50">
				<div class="box">
					<div class="box_header">
						<h1><?php $this->getLanguage('create_create');?></h1>
					</div>

					<div class="box_body">
						<p>
							<?php $this->getLanguage('create_create_intro');?>
						</p>

						<ol>
							<?php
							if (isset($stage) && $stage > 1) {
							?>
							<li><del><?php $this->getLanguage('create_terms');?></del></li>
							<?php
							}
							else {
							?>
							<li><?php $this->getLanguage('create_terms');?></li>
							<?php }?>


							<?php
							if (isset($stage) && $stage > 2) {
							?>
							<li><del><?php $this->getLanguage('create_connect');?></del></li>
							<ul>
								<li><b><?php echo $_SESSION['openid_nickname'];?></b></li>
								<li><b><?php echo $_SESSION['openid_identity'];?></b></li>
								<li><b><?php echo $_SESSION['openid_email'];?></b></li>
							</ul>
							<?php
							}
							else {
							?>
							<li><?php $this->getLanguage('create_connect');?></li>
							<?php }?>


							<?php
							if (isset($stage) && $stage > 3) {
							?>
							<li><del><?php $this->getLanguage('create_design');?></del></li>
							<?php
							}
							else {
							?>
							<li><?php $this->getLanguage('create_design');?></li>
							<?php }?>


							<?php
							if (isset($stage) && $stage > 4) {
							?>
							<li><del><?php $this->getLanguage('create_settings');?></li>
							<?php
							}
							else {
							?>
							<li><?php $this->getLanguage('create_settings');?></li>
							<?php }?>

							<li><?php $this->getLanguage('create_webspace_url');?></li>
						</ol>
					</div>
				</div>
			</div>

			<div id="col_right_50">
				<?php
				if (isset($stage) && $stage == 1) {
				?>

				<div class="box">
					<div class="box_header">
						<h1><?php $this->getLanguage('create_terms');?></h1>
					</div>

					<div class="box_body">
						<div style="width:100%; height:380px;overflow:auto">
							<?php
							if (is_readable('language/' . AM_DEFAULT_LANGUAGE_CODE . '/terms_of_use.lang.php')) {
								include_once('language/' . AM_DEFAULT_LANGUAGE_CODE . '/terms_of_use.lang.php');
							}
							?>
						</div>

						<p align="right">
							<input type="submit" name="reject_terms" value="<?php $this->getLanguage('create_disagree');?>" />
							<input type="submit" name="accept_terms" value="<?php $this->getLanguage('create_agree');?>" />
						</p>
					</div>
				</div>

				<?php
				}
				elseif (isset($stage) && $stage == 2) {
				?>

				<div class="box">
					<div class="box_header">
						<h1><?php $this->getLanguage('create_connect');?></h1>
					</div>

					<div class="box_body">
						<p>
							<label for="id_openid"><?php $this->getLanguage('common_openid');?></label><br />
							<input type="text" id="openid_login" name="openid_login" value="http://example.domain.org" onFocus="this.value=''; return false;" />
						</p>

						<p align="right">
							<input type="submit" name="connect" value="<?php $this->getLanguage('common_connect');?>" />
						</p>

						<h3><?php $this->getLanguage('create_openid');?></h3>

						<p>
							<?php $this->getLanguage('create_openid_intro');?>
						</p>
						</div>
					</div>

					<?php
					}
					elseif (isset($stage) && $stage == 3) {
					?>

					<div class="box">
						<div class="box_header">
							<h1><?php $this->getLanguage('create_design');?></h1>
						</div>

						<div class="box_body">
							<input type="hidden" id="theme_name" name="theme_name" value="" />
							<input type="hidden" id="theme_css" name="theme_css" value="" />

							<p>
								<?php $this->getLanguage('create_design_intro');?>
							</p>

							<?php
							if (isset($themes)) {
							?>
							<script type="text/javascript">

								function viewThumbs(theme) {
									var v = document.getElementById('thumbs').getElementsByTagName('div');
									for(i=0;i<v.length;i++) {
										v[i].style.display = "none";
									}
									document.getElementById('output_thumb').innerHTML = "";
									document.getElementById(theme+'_thumb').style.display = "block";
									document.getElementById('id_layout').value = theme;

								}

								function viewThumb(path, css, theme) {
									document.getElementById('output_thumb').innerHTML = "<img src=\""+path+"\"/>";
									document.getElementById('theme_name').value = theme;
									document.getElementById('theme_css').value = css;
								}

							</script>

							<div id="theme_names">
								<p>
									<?php $this->getLanguage('create_select_theme');?>
								</p>

								<?php
								foreach($themes as $key => $v) {
								?>
								<h3><?php echo $lang['arr_theme'][$key]['name'];?></h3>
								<p>
									<?php echo $lang['arr_theme'][$key]['description'];?>
								</p>

								<ul>
									<?php foreach($v['thumb'] as $t) { ?>
										<?php
										$tmp = explode('/', $t);
										$tmp = explode('.', $tmp[count($tmp)-1]);
										?>

										<li><a href="#" onclick="viewThumb('<?php echo $t; ?>', '<?php echo $tmp[0];?>', '<?php echo $key;?>');"><?php echo $lang['arr_theme'][$key]['style'][$tmp[0]];?></a></li>
									<?php } ?>
								</ul>
								<?php } ?>
							</div>

							<div id="output_thumb"></div>

							<script type="text/javascript">
								viewThumb('themes/smorgasbord/thumb/yellow.png', 'yellow', 'smorgasbord');
							</script>
							<?php }?>

							<p align="right">
								<input type="submit" name="apply_design" value="<?php $this->getLanguage('create_apply_design');?>" />
							</p>
						</div>
					</div>

					<?php
					}
					elseif (isset($stage) && $stage == 4) {
					?>

					<div class="box">
						<div class="box_header">
							<h1><?php $this->getLanguage('create_settings');?></h1>
						</div>

						<div class="box_body">
							<p>
								<?php $this->getLanguage('create_settings_intro');?>
							</p>

							<p>
								<label for="id_title"><?php $this->getLanguage('common_title');?></label>
								<input name="webspace_title" id="id_title" type="text" value="<?php if (isset($_POST['title'])) echo stripslashes($_POST['title']); ?>" />
							</p>



							<?php
							if (count($arr_language['pack']) > 1) {
							?>

							<p>
								<label for="id_language_id"><?php $this->getLanguage('common_language');?></label>
								<select name="language_code" id="id_language_code">
									<?php
									foreach($arr_language['pack'] as $key => $i):
										$selected = "";
										if (isset($_POST['language_code']) && $_POST['language_code'] == $key) {
											$selected = "selected=\"selected\"";
										}

										if (isset($this->lang['arr_language'][$key])) {
											$language_name = ucfirst(strtolower($this->lang['arr_language'][$key]));
										}
										else {
											$language_name = $i;
										}
									?>
									<option value="<?php echo $key;?>" <?php echo $selected; ?>><?php echo $language_name;?></option>
									<?php
									endforeach;
									?>
								</select>
							</p>
							<?php }?>

							<p>
								<?php $this->getLanguage('create_locked_intro');?>
							</p>

							<p>
								<label for="id_lock" style="width:300px;"><?php $this->getLanguage('create_locked');?></label><input type="checkbox" name="webspace_locked" id="id_lock" <?php if (isset($_POST['lock']) && !empty($_POST['lock'])) echo "checked=\"checked\""; ?>/>
							</p>

							<p align="right">
								<input type="submit" name="configure" value="<?php $this->getLanguage('create_apply_settings');?>" />
							</p>
						</div>
					</div>

					<?php
					}
					elseif (isset($stage) && $stage == 5) {
					?>

					<div class="box">
						<div class="box_header">
							<h1><?php $this->getLanguage('create_webspace_url');?></h1>
						</div>

						<div class="box_body">
							<?php if (isset($webspace_name)) { ?>
								<p>
									<?php $this->getLanguage('create_webspace_url_display');?>
								</p>

								<h3><?php echo str_replace('REPLACE', $webspace_name, $config_url);?></h3>

								<p>
									<input type="submit" name="reject_webspace_name" value="<?php $this->getLanguage('create_choose_again');?>" />
									<input type="submit" name="complete" value="<?php $this->getLanguage('create_choose_create');?>"/>
									<input type="hidden" name="webspace_name" value="<?php if (isset($webspace_name)) { echo $webspace_name;}?>" />
								</p>
							<?php } else { ?>
								<p>
									<?php
									$ws_url = str_replace('REPLACE', 'example', $config_url);
									$webspace_url_choose = $this->lang['create_webspace_url_choose'];
									$webspace_url_choose = str_replace('AM_SYS_KEYWORD_URL', $ws_url, $webspace_url_choose);
									echo $webspace_url_choose;
									?>
								</p>

								<p>
									<label for="id_webspace_name"><?php $this->getLanguage('create_webspace_name');?></label><input type="text" name="webspace_name" id="id_webspace_name" value="<?php if (isset($_POST['webspace_name'])) { echo $_POST['webspace_name'];}?>" />
									<input type="submit" name="test_webspace_name" value="<?php $this->getLanguage('create_choose');?>"/>
								</p>
								<?php
								if (isset($confirm_webspace_name)) {
								?>
									<p>
										<?php $this->getLanguage('create_webspace_domain_not_working');?>
									</p>
									<p>
										<input type="submit" value="<?php $this->getLanguage('create_choose_again');?>" name="reject_webspace_name"/>
										<input type="submit" value="yes" name="complete"/>
									</p>
								<?php }?>
							<?php } ?>
						</div>
					</div>
					<?php }?>
				</div>
			</div>
			<?php
			}
			elseif (isset($stage) && $stage == 6) {
			?>
			
			<div class="box">
				<div class="box_header">
					<h1><?php $this->getLanguage('create_complete');?></h1>
				</div>

				<div class="box_body">
					<p>
						<?php $this->getLanguage('create_complete_intro');?>
					</p>
					
					<ul>
						<li><?php $this->getLanguage('create_owner_nickname');?> <?php echo $_SESSION['openid_nickname'];?></li>
						<li><?php $this->getLanguage('create_owner_openid');?> <?php echo $_SESSION['openid_identity'];?></li>
						<li><?php $this->getLanguage('create_owner_email');?> <?php echo $_SESSION['openid_email'];?></li>
						<li><?php $this->getLanguage('create_owner_url');?> <?php echo str_replace('REPLACE', $_SESSION['webspace_name'], $config_url);?></li>
						<li><?php $this->getLanguage('create_owner_created');?> <?php echo strftime("%d %b %G %H:%M", $_SESSION['webspace_create_datetime']);?></li>
					</ul>

					<?php
					if ($webspace_creation_type == 2) { // automatic
					?>
					<p>
						<?php
						$ws_url = str_replace('REPLACE', $_SESSION['webspace_name'], $config_url);
						$webspace_url = $this->lang['create_goto_webspace'];
						$webspace_url = str_replace('AM_SYS_KEYWORD_URL', $ws_url, $webspace_url);
						echo $webspace_url;
						?>
					</p>
					<?php
					}
					else {
					?>
					<p>
						<?php $this->getLanguage('create_complete_pending');?>
					</p>
					<?php }?>
				</div>
			</div>
			<?php }?>
			</form>
		</div>
	</body>
</html>