<?php

require '../vendor/autoload.php';

$transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
    ->setUsername('username@gmail.com')
    ->setPassword('yourpassword');

// Create the Mailer using your created Transport
$mailer = new Swift_Mailer($transport);

// Create a message
$message = (new Swift_Message('Error in MyHTTPServer'))
    ->setFrom(['noreply@myhttpserver.com' => 'John Doe'])
    ->setTo('username@gmail.com')
    ->setBody($_SERVER['error']);

// Send the message
$mailer->send($message);

