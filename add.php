<?php

include('PHPMailer.php');
include('Exception.php');
include('imap.php');

$imapServer = '{imap.gmail.com:993/imap/ssl}INBOX';
$imapUsername = 'rameshauti124@gmail.com';
$imapPassword = '';
$imap = imap_open($imapServer, $imapUsername, $imapPassword);
print("Connection established....");
print("<br>");

$newmailbox = "{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX.new_mail_box";
$res = imap_createmailbox($imap, imap_utf7_encode($newmailbox));

if ($res) {
    print("Mailbox created successfully");

    include('conn.php'); 

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $con->prepare("INSERT INTO `user` (`email`, `password`) VALUES ('$email','$hashedPassword')");
    $stmt->bind_param("ss", $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "Record inserted into the database successfully";
    } else {
        echo "Error inserting record: " . $stmt->error;
    }

    $stmt->close();
    $con->close();
} else {
    print("Error occurred");
}
?>
