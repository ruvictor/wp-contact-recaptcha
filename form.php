<?php
/**
 * Plugin Name: Contact Form Plugin
 * Description: This plugin will generate a contact form
 * Plugin URI: https://vicodemedia.com
 * Author: Victor Rusu
 * Version: 1.0.0
**/


add_action('wp_enqueue_scripts', 'callback_for_style');
function callback_for_style() {
    wp_register_style( 'vicode', plugins_url('/style.css', __FILE__), false, '1.0.0', 'all' );
    wp_enqueue_style( 'vicode' );
}


function vicode_contact_form(){

    // reCAPTCHA v3
    define('SITE_KEY', '');
    define('SECRET_KEY', '');


    if(isset($_POST['submitted'])) {

        $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".SECRET_KEY."&response={$_POST['g-recaptcha-response']}");
        $Return = json_decode($Response);
        // var_dump($Return);

        if($Return->success == true && $Return->score > 0.5){
            $hasError = false;
        }else{
            $googleError = 'reCAPTCHA Error!';
            $hasError = true;
        }
        
        if(trim($_POST['contactName']) === '') {
            $nameError = 'Please enter your name.';
            $hasError = true;
        } else {
            $name = trim($_POST['contactName']);
        }
    
        if(trim($_POST['email']) === '')  {
            $emailError = 'Please enter your email address.';
            $hasError = true;
        } else if (!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", trim($_POST['email']))) {
            $emailError = 'You entered an invalid email address.';
            $hasError = true;
        } else {
            $email = trim($_POST['email']);
        }
    
        if(trim($_POST['comments']) === '') {
            $commentError = 'Please enter a message.';
            $hasError = true;
        } else {
            if(function_exists('stripslashes')) {
                $comments = stripslashes(trim($_POST['comments']));
            } else {
                $comments = trim($_POST['comments']);
            }
        }

        if(!isset($hasError)) {
            $emailTo = 'info@vicodemedia.com';
            $subject = '[Contact Form] From '.$name;
            $body = "Name: $name \n\nEmail: $email \n\nComments: $comments";
            $headers = 'From: '.$name.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
    
            wp_mail($emailTo, $subject, $body, $headers);
        }

        if(!$hasError)
            $submittedSuccess = 'Thank you for contacting us!';
    }

    // validating variables
    $googleError = isset($googleError) ? $googleError : '';
    $nameError = isset($nameError) ? $nameError : '';
    $emailError = isset($emailError) ? $emailError : '';
    $commentError = isset($commentError) ? $commentError : '';
    $submittedSuccess = isset($submittedSuccess) ? $submittedSuccess : '';
    // $submittedSuccess = !isset($hasError) && isset($_POST['submitted']) ? 'Thank you for contacting us!<br>' : '';
    // var_dump($submittedSuccess);
    
    $content = $submittedSuccess . '
    <script src="https://www.google.com/recaptcha/api.js?render=' . SITE_KEY . '"></script>
    <form action="' . get_the_permalink() . '" id="contactForm" method="post">

        <div class="formGroup">
            <label for="contactName">Name:</label>
            <input type="text" name="contactName" id="contactName">
            <span class="error"> ' . $nameError . '</span>
        </div>

        <div class="formGroup">
            <label for="email">Email</label>
            <input type="text" name="email" id="email">
            <span class="error"> ' . $emailError . '</span>
        </div>

        <div class="formGroup">
            <label for="commentsText">Message:</label>
            <textarea name="comments" id="commentsText" rows="5" cols="20"></textarea>
            <span class="error"> ' . $commentError . '</span>
        </div>

        <input type="hidden" name="submitted" id="submitted" value="true" />

        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" />

        <input type="submit" value="Send Email" />
        <div class="formGroup">
            <span class="error"> ' . $googleError . '</span>
        </div>
    </form>

    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute("' . SITE_KEY . '", {action: "homepage"})
            .then(function(token) {
                //console.log(token);
                document.getElementById("g-recaptcha-response").value=token;
            });
        });
    </script>
    ';
    return $content;
}


add_shortcode( 'vicode_contact_form', 'vicode_contact_form' );