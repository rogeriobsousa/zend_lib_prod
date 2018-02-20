<?php


class Base_Email_EMail{
	
	function enviaEmail(array $params = null){
		error_reporting(E_ALL);
		$mail = new phpmailer_PHPMailer();
		
		$username = (isset($params['username']))  ? $params['username'] : 'rogerio@virtualavionics.com.br';
		$password = (isset($params['password']))  ? $params['password'] : 'aqamnsq';
		$mailServer = (isset($params['mailServer']))  ? $params['mailServer'] : 'smtp.virtualavionics.com.br';
		
		$bodyText = ($params['bodyText'])  ? $params['bodyText'] : 'Email padrao de teste de envio....<BR>Por favor não retornar!';
		
		$from = (isset($params['from']))  ? $params['from'] : 'suporte@virtualavionics.com.br';
		$fromName = (isset($params['fromName']))  ? $params['fromName'] : 'Academicos da Asa Norte';
		$to = (isset($params['to']))  ? $params['to'] : 'rogeriobsousa@gmail.com';
 		$toName = (isset($params['toName']))  ? $params['toName'] : 'Nome';
 		$subject = (isset($params['subject']))  ? utf8_decode($params['subject']) : utf8_decode('SCA - Sistema de Controle de Associados');

 	
 		
 		$mail->IsHTML(true);                                  // set email format to HTML
		// Define os dados do servidor e tipo de conexão
		// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
		$mail->IsSMTP(); // Define que a mensagem ser� SMTP
		$mail->Host = $mailServer; // Endere�o do servidor SMTP
		$mail->SMTPAuth = true; // Usa autentica��o SMTP? (opcional)
		$mail->Username = $username;  // SMTP username
		$mail->Password = $password; // SMTP password
 		
 		$address = $to;
		$mail->From = $from; // Seu e-mail
		$mail->FromName = $fromName; // Seu nome
		$mail->Subject = $subject;
		$mail->Body =$bodyText;
		
 		$mail->AddAddress($address, $toName);
 		if(!$mail->Send()) {
		  echo "Mailer Error: " . $mail->ErrorInfo;
		  return false;
		} else {
			echo "<pre>";
			print_r('aqui3');
			die("Arquivo: ".__FILE__." - Linha: ".__LINE__);  
			return true;
		}
		
	}
	
}