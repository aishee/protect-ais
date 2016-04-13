<?php

// Denied direct access.
defined('ABSPATH') or die("You is bot, spam, .v..v..");

?>


<?php
$form_html = '
		<style type="text/css">
		html {
			background: #fff;
		}
		body {
			background-color: ' . $protect_ais['bg_color'] . ';
			background-image: url(' . $protect_ais['brand_bg'] . ');
			background-size: cover;
			background-position: center center;
			color: ' . $protect_ais['text_color'] . ';
			font-family: "Open Sans", sans-serif;
			width: auto;
			-webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.13);
			box-shadow: 0 0px 0px rgba(0,0,0,0.13);
		}
                p{
                        color: ' . $protect_ais['text_color'] . ';
                }
		input{
			padding: 10px;
			min-width: 100%;
			border: 1px solid #ccc;
			min-height: 20px;
			color: #000;
			font-size: 16px;
			border-radius: 2px;
			margin: 10px auto;
		}
		form{
			max-width:250px;
			margin: 0 auto;
                        padding: calc(25% - 320px);
		}
		#error-page {
			margin-top: 0;
			margin: 0 auto;
			width: 100%;
			max-width: 100%;
                        padding: 50px 0 0;
                        height: calc(100vh - 50px);
		}
			#error-page p {
			font-size: 14px;
			line-height: 1.5;
			margin: 0;
		}			
                        #error-page p.submit {
			padding: 15px 0;
		}

		#error-page code {
			font-family: Consolas, Monaco, monospace;
		}
		ul li {
			margin-bottom: 10px;
			font-size: 14px ;
		}
		a {
			color: ' . $protect_ais['text_color'] . ';
			text-decoration: none;
                        display: block;
		}
		a:hover {
                        color: ' . $protect_ais['text_color'] . ';
                        text-decoration: underline;
		}
		.button {
			background: #f7f7f7;
			border: 1px solid #cccccc;
			color: #555;
			display: inline-block;
			text-decoration: none;
			font-size: 13px;
			line-height: 26px;
			height: 28px;
			margin: 0;
			padding: 1px 10px 1px;
			cursor: pointer;
			-webkit-border-radius: 3px;
			-webkit-appearance: none;
			border-radius: 3px;
			white-space: nowrap;
			-webkit-box-sizing: border-box;
			-moz-box-sizing:    border-box;
			box-sizing:         border-box;
			min-width: 50px;

			-webkit-box-shadow: inset 0 1px 0 #fff, 0 1px 0 rgba(0,0,0,.08);
			box-shadow: inset 0 1px 0 #fff, 0 1px 0 rgba(0,0,0,.08);
		 	vertical-align: top;
		}

		.button.button-large {
			height: 29px;
			line-height: 28px;
			padding: 0 12px;
		}

		.button:hover,
		.button:focus {
			background: #fafafa;
			border-color: #999;
			color: #222;
		}

		.button:focus  {
			-webkit-box-shadow: 1px 1px 1px rgba(0,0,0,.2);
			box-shadow: 1px 1px 1px rgba(0,0,0,.2);
		}

		.button:active {
			background: #eee;
			border-color: #999;
			color: #333;
			-webkit-box-shadow: inset 0 2px 5px -3px rgba( 0, 0, 0, 0.5 );
		 	box-shadow: inset 0 2px 5px -3px rgba( 0, 0, 0, 0.5 );
		}
                .ais-image{
                        max-height: 200px; max-width: 270px;margin: 0 auto;display: inherit;padding: 0 0 20px;
                }

			</style>
                        
			<form name="loginform" id="loginform" action="' . get_bloginfo('url') . '" method="post">
                       ';

if ($protect_ais['ais_image'] != NULL)
    $form_html .= '<a href="' . get_bloginfo('url') . '"><img src="' . $protect_ais['ais_image'] . '" class="ais-image"></a><br>';

$form_html .= '
		<p>
			<input type="hidden" name="ggcap_key" value="ais-login" size="20">
			<label for="user_login">Username<br>
			<input type="text" name="ais_login" id="user_login" class="input" value="" size="20"></label>
		</p>
		<p>
			<label for="user_pass">Password<br>
			<input type="password" name="ais_pass" id="user_pass" class="input" value="" size="20"></label>
		</p>
		 <div class="g-recaptcha" data-sitekey="' . $site_key . '" style="transform:scale(0.90);-webkit-transform:scale(0.90);transform-origin:0 0;-webkit-transform-origin:0 0;"></div>
         <script type="text/javascript"
                    src="https://www.google.com/recaptcha/api.js?hl=<?php echo $lang; ?>">
         </script>
                
                <p><a href="' . get_bloginfo('url') . '/wp-login.php?action=lostpassword">' . __('Lost your password?', 'Protect_Ais') . '</p>';
if ($user_reg == '1'):
    $form_html .= '<p><a href="' . get_bloginfo('url') . '/wp-login.php?action=register">' . __('Register', 'Protect_Ais') . '</p>';
endif;

$form_html .= '<p class="submit">
			<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Log In">
		</p>
			</form>';
?>
