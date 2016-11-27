<?php
require_once('config.php');
require_once('recaptcha/recaptchalib.php');

$json = array();
$email = isset( $_POST['email'] ) ? $_POST['email'] : '';
$name = isset( $_POST['name'] ) ? $_POST['name'] : '';
$message = isset( $_POST['message'] ) ? $_POST['message'] : '';

if( !$name ) {
	$json['error']['name'] = 'Por favor introduzca su nombre.';
}
if( !$message ) {
	$json['error']['message'] = 'Por favor introduzca su mensaje.';
}
if( !$email || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $email ) ) {
	$json['error']['email'] = 'Por favor introduzca su correo.';
}
// Checking reCaptcha
# the response from reCAPTCHA
$resp = null;
# the error code from reCAPTCHA, if any
$error = null;
$reCaptcha = new ReCaptcha($privatekey);
# was there a reCAPTCHA response?

if ($_POST["g-recaptcha-response"]) {
	$resp = $reCaptcha->verifyResponse(
		$_SERVER["REMOTE_ADDR"],
		$_POST["g-recaptcha-response"]
		);

	if (!$resp->success) {
# set the error code so that we can display it
		$error = $resp->error;
		$json['error']['recaptcha'] = 'Código incorrecto. Por favor intente de nuevo.';
	}
} else {
	$json['error']['recaptcha'] = 'Por favor introduzca el texto de reCaptcha.';
}
// If no errors
if( !isset( $json['error'] ) ) {
// Email text
	$mail_message = "From: " . $name . "\r\n\r\n";
	$mail_message .= "E-mail: " . $email . "\r\n\r\n";
	$mail_message .= "Message:\r\n\r\n" . $message . "";
// Email title
	$mail_headers  = "Content-type: text/plain; charset=utf-8\r\n";
	$mail_headers .= "From: {$mail_sender}\r\n";
// Sending email
	mail( $to_email, $mail_subject, $mail_message, $mail_headers );
	$json['success'] = 'Su mensaje ha sido enviado exitosamente!';
}

echo json_encode( $json );
?>