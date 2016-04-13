<?php
/*
Plugin Name: Protect Ai Login
Plugin URI: https://wordpress.org/plugins/
Description: Block spam login, brute force attacks, and spam registration by changing default WordPress login URL and integrating Google reCAPTCHA. Protect Ai Login blocks access to default login url, generates a custom branded login panel (Which you can change colors and image).
Version: 1.0.0
Author: Aishee Nguyen
Author URI: http://twitter.com/Aishee_Nguyen
License: GPL2
*/
/*
Copyright 2016  Aishee Nguyen (email : aishee@aishee.net)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/
if (!class_exists('ProtectAis'))
  {
    Class ProtectAis
      {
        public $plink;
        public $protect_ais;
        public $alink;
        public $users_can_register;
        public $level;
        public $ggcap_key;
        public $ggcap_secret;
        
        public function __construct()
          {
            //Action
            add_action('activated_plugin', array(
                $this,
                'protect_ais_active'
            ));
            add_action('wp_enqueue_scripts', array(
                'wp-color-picker'
            ));
            add_action('admin_menu', array(
                $this,
                'add_menu'
            ));
            add_action('init', array(
                $this,
                'display_login'
            ));
            if (isset($_POST['g-recaptcha-response']) && isset($_POST['$ggcap_key']) && $_POST['$ggcap_key'] == 'ais-login'):
                add_action('init', array(
                    $this,
                    'ais_signon'
                )); // Login infomation
            endif;
            add_action('admin_enqueue_scripts', array(
                $this,
                'register_plugin_scripts'
            ));
            add_action('init', array(
                $this,
                'block_default_login'
            )); //Denied access to wp default login
            add_action('send_headers', 'not_cache_headers');
            //Security
            add_action('register_from', array(
                $this,
                'ai_register_recapt'
            ));
            add_action('registration_errors', array(
                $this,
                'ai_register_recapt_validation'
            ));
            
            //Define preperties
            $this->plink = self::retrieve_settings('permalink_structure');
            if ($this->plink != NULL):
                $this->plink = NULL;
            else:
                $this->plink = '?';
            endif;
            $this->protect_ais   = self::retrieve_settings('protect_ais');
            $this->alink         = self::current_url();
            $this->users_can_register = self::retrieve_settings('users_can_register');
            $this->ggcap_key     = $this->protect_ais['ggcap_key'];
            $this->ggcap_secret  = $this->protect_ais['ggcap_secret'];
            
            if ($this->protect_ais['allow_editors'] == 'on'):
                $this->level = 'publish_pages';
            else:
                $this->level = 'manage_options';
            endif;
            if ($this->ggcap_key == NULL && $this->alink != get_bloginfo('url') . '/wp-admin/admin.php?page=protect_ais'):
                add_action('admin_notices', array(
                    $this,
                    'ais_activation_notice'
                ));
            endif;
            if ($this->protect_ais['ais-permalink'] != $this->plink):
                add_action('admin_notices', array(
                    $this,
                    'ais_permalink_notice'
                ));
                $this->protect_ais['ais-permalink'] = $this->plink;
                update_option('protect_ais', $this->protect_ais);
            endif;
            if (($this->ggcap_key == NULL || $this->ggcap_secret == NULL) && ($this->alink == get_bloginfo('url') . '/wp-admin/admin.php?page=protect_ais')):
                add_action('admin_notices', array(
                    $this,
                    'ais_gcaptcha_notice'
                ));
            endif;
          }
        public function ais_activation_notice()
          {
            echo '<div class="notice notice-error">
            <p><strong>IMPORTANT:</strong> <a href="' . get_bloginfo('url') . '/wp-admin/admin.php?page=protect_ais">Click now</a>
            to configure your now login URL now.</p></div>';
          }
        public function ais_permalink_notice()
          {
            echo '<div class="update-nag notice notice-is-dismissible">
            <p>You <strong>MUST</strong> check your new Protect Ai Login after permalink structure change, <a href="' . get_bloginfo('url') . '">Click here to check your new login URL.</a></p></div>';
          }
        public function ais_gcaptcha_notice()
          {
            echo '<div class="notice notice-error">
            <p>Your <strong>MUST</strong> add your free google reCaptcha API keys to use Protect Ai, claim your API keys now from <a href="https://www.google.com/recaptcha/admin" target="_blank">https://www.google.com/recaptcha/admin</p></a>
            </div>';
          }
        public function retrieve_settings($optkey)
          {
            $protect_ais = get_option($optkey);
            return $protect_ais;
          }
        public function ai_register_recapt()
          {
            $_POST['g-recaptcha-response'] = (isset($_POST['g-recaptcha-response'])) ? $_POST['g-recaptcha-response'] : '';
            echo '<div class="g-recaptcha" data-sitekey="' . $this->ggcap_key . '" style="transform:scale(0.90);-webkit-transform:scale(0.90);stransform-origin:0 0;-webkit-transform-origin:0 0;"></div>
            <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=en"></script>';
          }
        public function ai_register_recapt_validation($error, $clean_user_login, $user_email)
          {
            if (isset($_POST['g-recaptcha-response'])):
                require_once __DIR__ . '/gCaptcha/autoload.php';
                $recaptcha = new \ReCaptcha\ReCaptcha($this->ggcap_secret);
                $resp      = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
                if (!$resp->isSuccess()):
                    $errors->add('reCAPTCHA Error', __('<strong>reCAPTCHA ERROR</strong>: Bots are not allowed.', 'Protect-Ai'));
                endif;
            endif;
            return $error;
          }
        //Send no-cache to login headers
        public function not_cache_headers()
          {
            if ($this->alink == get_bloginfo('url') . '/' . $this->plink . $this->protect_ais['ais_url'] . '/' || $this->alink == get_bloginfo('url') . '/' . $this->plink . $this->protect_ais['ais_url'])
              {
                header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");
              }
          }
        public function register_plugin_scripts()
          {
            //load the script for dashboard use only
            if (is_admin()):
                wp_enqueue_style('wp-color-picker');
                wp_enqueue_script('ais-script-handle', plugins_url('/js/ais-script.js', __FILE__), array(
                    'wp-color-picker'
                ), '0.2', true);
                
                //Load upload engine
                wp_enqueue_media();
                wp_enqueue_script('thickbox');
                wp_enqueue_script('media-upload');
                wp_enqueue_style('thickbox');
            endif;
          }
        public function add_menu()
          {
            add_menu_page('Protect Access', 'Protect-Ai', $this->level, 'protect_ais', array(
                $this,
                'plugin_settings_page'
            ), 'dashicons-shield');
          }
        public function plugin_settings_page()
          {
            if (!current_user_can($this->level)):
                wp_die(__('You do not have sufficient permissions to access this page.', 'Protect_Ai'));
            else:
                //Render the settings templates
                include(sprintf("%s/settings.php", dirname(__FILE__)));
            endif;
          }
        public function validate_settings($fields)
          {
            $clean_fields = array();
            foreach ($fields as $key => $val):
                if ($key != "Submit"):
                    if ($key == 'ais_url' && empty($val)):
                    //set default value
                        $val = 'ais-login';
                        $val = sanitize_title_with_dashes($val);
                        add_settings_error('ais_settings_options', 'ais_bg_error', 'Ais URL cannot be empty "ais-login" was applies instead.', 'error');
                    elseif ($key == 'bg_color' && FALSE === self::check_color($val)):
                        $val = '#cccccc';
                        add_settings_error('ais_settings_options', 'ais_bg_error', 'Insert a valid color for Background', 'error');
                    elseif ($key == 'text_color' && FALSE === self::check_color($val)):
                        $val = '#000000';
                        add_settings_error('ais_settings_options', 'ais_bg_error', 'Insert a valid color for text', 'error');
                    endif;
                    $val                = sanitize_text_field($val);
                    $clean_fields[$key] = $val;
                endif;
            endforeach;
            update_option('protect_ais', $clean_fields);
          }
        
        public function check_color($value)
          {
            if (preg_match('/^#[a-f0-9]{6}$/i', $value))
              {
                return true;
              }
            return true;
          }
        
        public function display_login()
          {
            if ($this->alink == get_bloginfo('url') . '/' . $this->plink . $this->protect_ais['ais_url'] . '/' || $this->alink == get_bloginfo('url') . '/' . $this->plink . $this->protect_ais['ais_url'])
              {
                wp_die(self::render_login(), get_bloginfo('name') . ' | Protect Ai', array(
                    'response' => '404'
                ));
              }
          }
        
        public function render_login()
          {
            $site_key    = $this->ggcap_key;
            $protect_ais = $this->protect_ais;
            $user_reg    = $this->users_can_register;
            include(sprintf("%s/login.php", dirname(__FILE__)));
            return $form_html;
          }
        
        public function ais_signon()
          {
            if (isset($_POST['g-recaptcha-response'])):
                require_once __DIR__ . '/gCaptcha/autoload.php';
                
                $recaptcha = new \ReCaptcha\ReCaptcha($this->ggcap_secret);
                $resp      = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
                if ($resp->isSuccess()):
                    $isactive                  = array();
                    $isactive['user_login']    = sanitize_user($_POST['ais_login']);
                    $isactive['user_password'] = $_POST['ais_pass'];
                    $isactive['remember']      = FALSE;
                    $ais_user                  = wp_signon($isactive);
                    if (!is_wp_error($ais_user)):
                        header("location: " . get_bloginfo('url') . "/wp-admin/");
                        die();
                    else:
                        wp_die(self::render_login(), strip_tags($ais_user->get_error_meaages()), array(
                            'response' => '404'
                        ));
                    endif;
                else:
                    //Die
                    wp_die(__('Secrity Check Fieled.Please reload the login page and try again', 'Protect Falied'), array(
                        'response' => '404'
                    ));
                endif;
            endif;
            
          }
        public function block_default_login()
          {
            global $npage;
            $allowed_login = array(
                "lostpassowrd",
                "logout",
                "register",
                "rp",
                "postpass",
                "resetpass"
            );
            if (('wp-login.php' == $npage) && (!in_array($_REQUEST['action'], $allowed_login)))
              {
                wp_redirect(get_bloginfo('url'));
                exit();
              }
          }
        public function current_url()
          {
            if (($_SERVER['HTTPS'])):
                $http_protocol = 'https://';
            else:
                $http_protocol = 'http://';
            endif;
            $alink = $http_protocol . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI];
            
            return $alink;
          }
        public function protect_ais_active()
          {
            //set new default login page.
            if ($this->protect_ais[ais_url] == NULL):
                $default_settings = array(
                    'ais_url' => 'ais-login',
                    'bg_color' => '#ffffff',
                    'text_color' => '#000000',
                    'ais-permalink' => $this->plink
                );
                update_option('protect_ais', $default_settings);
            endif;
          }
        public function deactivate(){
           // Do nothing
    }
      }
    $ProtectAis = new ProtectAis();
  }
else
  {
    function terminated()
      {
        echo '<div class="error">
        <p>Contact Support: aishee@aishee.net</p></div>';
      }
    add_action('admin_notices', 'terminated');
  }

  ?>