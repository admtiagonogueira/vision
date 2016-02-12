<?php if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {header('Location: ../acesso.php'); exit();} ?>
<?php
include('mailer/class.smtp.php');
include('mailer/class.phpmailer.php');
?>

<?php
function sendEmail($endereco, $assunto, $mensagem){
	$mail = new PHPMailer;
	//$mail->AddReplyTo($AddReplyToAddress, $AddReplyToName);
	//$mail->AltBody = 'Texto plano';
	//$mail->SMTPSecure = $SMTPSecure;
	$mail->isSMTP();
	$mail ->charSet = 'utf-8';
	$mail->Host = '';
	$mail->SMTPAuth = true;
	$mail->Username = '';
	$mail->Password = '';
	$mail->Port = 25;
	$mail->SMTPDebug  = false;
	$mail->From = '';
	$mail->FromName = 'Vision FJU';
	$mail->addAddress($endereco);
	$mail->Subject = $assunto;
	$mail->Body    = $mensagem;
	$mail->WordWrap = 50;
	$mail->isHTML(true);

	if($mail->send()) {
		return true;
	} else {
		return false; //echo 'Mailer Error: ' . $mail->ErrorInfo;
	}	
}
?>