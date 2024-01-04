    <?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    require 'Exception.php';
    require 'PHPMailer.php';
    require 'SMTP.php';

    include('conn.php');

    function sendEmail($to, $subject, $body) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'rameshauti124@gmail.com';
            $mail->Password   = '';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 80;

            $mail->setFrom('rameshauti124@gmail.com', 'Your Name');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            echo 'Email sent successfully';
        } catch (Exception $e) {
            echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    function receiveEmails($imap, $mailbox) {
        $emails = imap_search($imap, 'ALL');

        if ($emails) {
            foreach ($emails as $email_number) {
                $message = imap_fetchbody($imap, $email_number, 1.1);
                $sender = imap_headerinfo($imap, $email_number)->from[0]->mailbox . "@" . imap_headerinfo($imap, $email_number)->from[0]->host;
                $subject = imap_utf8(imap_headerinfo($imap, $email_number)->subject);


                $stmt = $con->prepare("INSERT INTO `emails` (`sender`, `subject`, `body`) VALUES ('$sender', '$subject' , 'body')");
                $stmt->bind_param("sss", $sender, $subject, $message);

                if ($stmt->execute()) {
                    echo "Email stored in the database successfully";
                } else {
                    echo "Error storing email in the database: " . $stmt->error;
                }

                $stmt->close();
            }
        } else {
            echo "No emails found";
        }
    }


    function registerUser($con, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $con->prepare("INSERT INTO `users` (`email`, `password`) VALUES ( '$email', '$hashedPassword')");
        $stmt->bind_param("ss", $email, $hashedPassword);

        if ($stmt->execute()) {
            echo "User registered";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $imapServer = '{imap.gmail.com:993/imap/ssl}INBOX';
    $imapUsername = 'rameshauti124@gmail.com';
    $imapPassword = '';

    $imap = imap_open($imapServer, $imapUsername, $imapPassword);

    if ($imap) {
        echo "IMAP Connection established....<br>";

        $newmailbox = "{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX.new_mail_box";
        $res = imap_createmailbox($imap, imap_utf7_encode($newmailbox));

        if ($res) {
            echo "Mailbox created successfully<br>";

            sendEmail('rameshauti124@gmail.com', 'Test Email', 'This is a test email sent using PHPMailer.');

            receiveEmails($imap, $newmailbox);

        } else {
            echo "Error creating mailbox";
        }

        imap_close($imap);
    } else {
        echo "IMAP connection failed";
    }
    ?>
