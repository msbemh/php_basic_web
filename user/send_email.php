<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/phpmailer/phpmailer/src/Exception.php';
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';
require '../vendor/autoload.php';

header('Content-Type: text/html; charset=UTF-8');

$result_array = array();

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    

    $email = $_GET['email'];
    $nick_name = $_GET['nick_name'];

    if(empty($nick_name)) $nick_name = '홀리몰리';

    // 랜덤 숫자 4개 생성
    $rand_num = sprintf('%04d', rand(0000,9999));

    /**
     * Cookie key에 특수문자가 있으면 설정이 안되서 특수문자를 제거
     * key : email
     * value : 인증번호(랜덤 4자리)
     * 
     * 회원가입시 60초 안에 인증번호를 넣고 회원가입을 해야한다.
     */
    $email_trans = preg_replace("/[^A-Za-z0-9-]/", "", $email);
    setcookie($email_trans, $rand_num, time() + 60, "/");

    // UTF-8 설정
    $mail->CharSet  = 'UTF-8'; 

    //Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output
    $mail->isSMTP(); //Send using SMTP
    $mail->Host = 'smtp.gmail.com'; //Set the SMTP server to send through
    $mail->SMTPAuth = true; //Enable SMTP authentication
    $mail->Username = 'thdalsehf@gmail.com'; //SMTP username
    $mail->Password = 'ynphcylqpewcehqo'; //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; //Enable implicit TLS encryption
    $mail->Port = 465; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('thdalsehf@gmail.com', '송민호');
    $mail->addAddress("$email", "$nick_name"); //Add a recipient

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz'); //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg'); //Optional name

    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->Subject = '인증번호';
    $mail->Body = "인증번호: $rand_num";
    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    $result_array['result'] = true;
} catch (Exception $e) {
    $result_array['result'] = false;
    $result_array['msg'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

echo json_encode($result_array);

?>