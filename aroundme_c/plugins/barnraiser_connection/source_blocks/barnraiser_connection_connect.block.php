<a name="connect"></a>
<div class="barnraiser_connection_connect">
    <div class="block">
        <?php
        if (isset($_SESSION['connection_id'])) {
        ?>
        <div class="block_body">
           <table cellspacing="0" cellpadding="2" border="0">
           <?php if (isset($_SESSION['openid_fullname'])) { ?>
                <tr>
                    <td valign="top" class="profile_field">
                      AM_BLOCK_LANGUAGE_FULLNAME
                    </td>
                    <td valign="top" class="profile_value">
                      <?php echo $_SESSION['openid_fullname']; ?>
                    </td>
               </tr>
           <?php } ?>
      
           <?php if (isset($_SESSION['openid_nickname'])) { ?>
               <tr>
                   <td valign="top" class="profile_field">
                      AM_BLOCK_LANGUAGE_NICKNAME
                  </td>
                   <td valign="top" class="profile_value">
                       <?php echo $_SESSION['openid_nickname']; ?>
                   </td>
               </tr>
           <?php } ?>
      
           <?php if (isset($_SESSION['openid_country'])) { ?>
               <tr>
                    <td valign="top" class="profile_field">
                        AM_BLOCK_LANGUAGE_COUNTRY
                    </td>
                    <td valign="top" class="profile_value">
                        <?php echo $this->lang['arr_identity_field']['country'][$_SESSION['openid_country']]; ?>
                    </td>
                </tr>
            <?php } ?>
      
            <?php if (isset($_SESSION['openid_language_code'])) { ?>
                <tr>
                    <td valign="top" class="profile_field">
                        AM_BLOCK_LANGUAGE_LANGUAGE
                    </td>
                    <td valign="top" class="profile_value">
                        <?php echo $this->lang['arr_identity_field']['language'][$_SESSION['openid_language_code']]; ?>
                    </td>
                </tr>
            <?php } ?>
      			
                <tr>
                    <td valign="top" class="profile_field">
                        AM_BLOCK_LANGUAGE_CONNECTIONS
                    </td>
                    <td valign="top" class="profile_value">
                        <?php echo $_SESSION['connection_total'];?><br />
                    </td>
                </tr>
            </table>
	
            <ul>
				<li><a href="index.php?t=network&amp;connection_id=<?php echo $_SESSION['connection_id'];?>">AM_BLOCK_LANGUAGE_ACCOUNT</a></li>
				<li><a href="index.php?disconnect=1">AM_BLOCK_LANGUAGE_DISCONNECT</a></li>
            </ul>
			</div>
        <?php
        }
        else {
        ?>
	    <div class="block_body">
		    <p>
  		        AM_BLOCK_LANGUAGE_CONNECT_INTRO
  		    </p>
  		    
  		    <form method="post">
                <label for="openid_login">OpenID</label>
                <input type="text" id="openid_login" name="openid_login" value="http://example.domain.org" onFocus="this.value=''; return false;" />
                <input type="submit" name="connect"  value="AM_BLOCK_LANGUAGE_GO" />
                <input type="hidden" name="connect" value="1"/>
            </form>

			<?php
			if (isset($account_registration_url)) {
			?>
            <ul>
				<li><a href="index.php?t=login&amp;register=1">AM_BLOCK_LANGUAGE_REGISTER</a></li>
			</ul>
			<?php }?>
        </div>
        <?php }?>
     </div>
</div>