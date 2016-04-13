<?php

// Block direct access.
defined('ABSPATH') or die("You is bot, spam, .v..v..");

if (isset($_POST['_ais_settings_nonce'])):
// Security Check
    if (!wp_verify_nonce(sanitize_text_field($_POST['_ais_settings_nonce']), '_save_ais_settings')):
    // Display Error
        add_settings_error('ais_settings_options', 'ais_security_error', 'Security check failed.', 'error'); // $setting, $code, $message, $type
        
        // Die
        wp_die(__('Security Check Failed. Click <a href="' . get_bloginfo('url') . '/wp-admin/options-general.php?page=protect_ais">here</a> to try again.', 'Secure AXS Settings'), array(
            'response' => '500'
        ));
    else:
        self::validate_settings($_POST['protect_ais']);
        
        // Retreive updated values after saving.
        $this->protect_ais = self::retrieve_settings('protect_ais');
        
        // Reload to pickup any warning or errors.
        echo '<script>location.reload();</script>';
    endif;
endif;




?>


<div class="wrap">
    <?php
echo "<h2>" . __('Access Settings', 'Protect_Ais') . "</h2>";
?>
    <form name="protect_ais" id="protect_ais" method="post" action="">
        <?php
wp_nonce_field('_save_ais_settings', '_ais_settings_nonce')
?>
        <table class="form-table">		
            

            <tbody>
                
                <tr valign="top"><th scope="row"><h4><?php
_e('Security Options', 'Protect_Ais');
?></h4></tr>
                
            	<tr valign="top">
				<th scope="row">
                                    <label for="protect_ais[ais_url]">
                                        <?php
_e("Access URL (i.e ais-login):", "Protect_Ais");
?>
                                    </label></th>
                                    <td>
                                        <input name="protect_ais[ais_url]" type="text" id="ais_url" value="<?php
echo $this->protect_ais['ais_url'];
?>" class="regular-text">
                                        <p class="description">Alphanumeric and dash "-" are only allowed.</p>
                                        <p class="description">Your current secured access login is: <a href="<?php
echo get_bloginfo('url') . '/' . $this->plink . $this->protect_ais['ais_url'];
?>" target="_blank"><?php
echo get_bloginfo('url') . '/' . $this->plink . $this->protect_ais['ais_url'];
?></p>
                                    </td>
                </tr>
                           
            	<tr valign="top">
				<th scope="row"><label for="protect_ais[allow_editors]"><?php
_e("Allow Editors to edit settings:", "Protect_Ais");
?></label></th>
                                <td><input name="protect_ais[allow_editors]" type="checkbox" id="allow_editors" <?php
if ($this->protect_ais['allow_editors'] == 'on'):
    echo 'checked';
endif;
?>>
                                    <p class="description">When Checked, Editors are able to access/change plugin settings.</p>
                                </td>
                </tr>
                
                
                
                
                
                <tr valign="top">
		<th scope="row"><label for="protect_ais[ggcap_key]"><?php
_e("reCAPTCHA site key* ", "Protect_Ais");
?></label></th>
                <td>
                    <input name="protect_ais[ggcap_key]" type="text" 
                           placeholder="" 
                           value="<?php
echo ($this->protect_ais['ggcap_key'] == NULL ? NULL : $this->protect_ais['ggcap_key']);
?>"
                           class="regular-text">
                    <p class="description">Claim your free reCAPTCHA site key and secret key from this link <a href="https://www.google.com/recaptcha/admin" target="_blabk">https://www.google.com/recaptcha/admin</a></p>
                </td>
		</tr>
		
		         <tr valign="top">
		<th scope="row"><label for="protect_ais[ggcap_secret]"><?php
_e("reCAPTCHA secret key* ", "Protect_Ais");
?></label></th>
                <td>
                    <input name="protect_ais[ggcap_secret]" type="text" 
                           placeholder="" 
                           value="<?php
echo ($this->protect_ais['ggcap_secret'] == NULL ? NULL : $this->protect_ais['ggcap_secret']);
?>"
                           class="regular-text">
                </td>
		</tr>
                           
                           
            </tbody>
            

            <tbody>
                
                <tr valign="top"><th scope="row"><h4><?php
_e('Branding Options', 'Protect_Ais');
?></h4></tr>
                
            	<tr valign="top">
				<th scope="row">
                                    <label for="protect_ais[bg_color]">
                                        <?php
_e("Background Color", "Protect_Ais");
?>
                                    </label></th>
                                    <td>
                                        <input name="protect_ais[bg_color]" type="text" id="bg_color" value="<?php
echo $this->protect_ais['bg_color'];
?>" class="regular-text ais-colors">
                                    </td>
		</tr>
            	<tr valign="top">
				<th scope="row">
                                    <label for="protect_ais[text_color]">
                                        <?php
_e("Text Color", "Protect_Ais");
?>
                                    </label></th>
                                    <td>
                                        <input name="protect_ais[text_color]" type="text" id="text_color" value="<?php
echo $this->protect_ais['text_color'];
?>" class="regular-text ais-colors">
                                    </td>
		</tr>
                <tr valign="top">
				<th scope="row">
                                    <label for="protect_ais[brand_logo]">
                                        <?php
_e("Custom Logo", "Protect_Ais");
?>
                                    </label></th>
                                    <td>
                                        
                                        <div id="ais_image_thumb">
										<?php
if ($this->protect_ais['ais_image'] != NULL):
?>
                                        <img src="<?php
    echo $this->protect_ais['ais_image'];
?>" style="max-height: 50px; width: auto;"><br>
                                        <?php
endif;
?>
                                        </div>
                                     
                                        <input name="protect_ais[ais_image]" id="ais_image" type="text" size="36" value="<?php
echo $this->protect_ais['ais_image'];
?>" class="regular-text logo">
                                        <input type="button" id="ais_image_button" class="button-secondary upload-img" value="Select Image">
                                    </td>
		</tr>
		</tr>
                <tr valign="top">
				<th scope="row">
                                    <label for="protect_ais[brand_bg]">
                                        <?php
_e("Custom Background Image", "Protect_Ais");
?>
                                    </label></th>
                                    <td>
                                        <div id="brand_bg_thumb">
										<?php
if ($this->protect_ais['brand_bg'] != NULL):
?>
                                        <img src="<?php
    echo $this->protect_ais['brand_bg'];
?>" style="max-height: 50px; width: auto;"><br>
                                        <?php
endif;
?>
                                        </div>
                                        
                                        <input name="protect_ais[brand_bg]" id="brand_bg" type="text" size="36" value="<?php
echo $this->protect_ais['brand_bg'];
?>" class="regular-text">
                                        <input type="button" id="brand_bg_button" class="button-secondary upload-img" value="Select Image">
                                    </td>
		</tr>
            </tbody>
            
        </table>
             <?php
submit_button();
?>
    </form>
