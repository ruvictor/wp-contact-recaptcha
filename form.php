<?php
/**
 * Plugin Name: Contact Form Plugin
 * Description: This plugin will generate a contact form
 * Plugin URI: https://vicodemedia.com
 * Author: Victor Rusu
 * Version: 1
**/

function vicode_contact_form(){
    if(isset($_POST['submitted'])) {
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
            $emailTo = get_option('tz_email');
            if (!isset($emailTo) || ($emailTo == '') ){
                $emailTo = get_option('admin_email');
            }
            $subject = '[PHP Snippets] From '.$name;
            $body = "Name: $name \n\nEmail: $email \n\nComments: $comments";
            $headers = 'From: '.$name.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
    
            wp_mail($emailTo, $subject, $body, $headers);
            $emailSent = true;
        }

        // echo "dfdfdff";
    
    }
    $content = '
    <form action="' . get_the_permalink() . '" id="contactForm" method="post">

                <label for="contactName">Name:</label>
                <input type="text" name="contactName" id="contactName" class="required requiredField" />
                <span class="error"> ' . $nameError . '</span>

                <label for="email">Email</label>
                <input type="text" name="email" id="email" class="required requiredField email" />

            <label for="commentsText">Message:</label>
                <textarea name="comments" id="commentsText" rows="5" cols="20" class="required requiredField"></textarea>

            <input type="hidden" name="submitted" id="submitted" value="true" />
                
        <input type="submit" value="Send Email" />
    </form>
    ';
    return $content;
}


add_shortcode( 'vicode_contact_form', 'vicode_contact_form' );