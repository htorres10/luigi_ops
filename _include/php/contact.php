<?php
/*
* Contact Form Class
*/
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

$admin_email = 'contacto@luigidecarlo.com.ar'; // Your Email
$message_min_length = 5; // Min Message Length

class Contact_Form{
	function __construct($details, $email_admin, $message_min_length){		
		$this->name = stripslashes($details['name']);
		$this->email = trim($details['email']);
		$this->subject = 'Tiene un nuevo contacto del website'; // Subject 
		$this->message = stripslashes($details['message']);
	
		$this->email_admin = $email_admin;
		$this->message_min_length = $message_min_length;
		
		$this->response_status = 1;
		$this->response_html = '';
	}


	private function validateEmail(){
		$regex = '/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i';
	
		if($this->email == '') { 
			return false;
		} else {
			$string = preg_replace($regex, '', $this->email);
		}
	
		return empty($string) ? true : false;
	}


	private function validateFields(){
		// Check name
		if(!$this->name)
		{
			$this->response_html .= '<p>Por favor ingrese su nombre</p>';
			$this->response_status = 0;
		}

		// Check email
		if(!$this->email)
		{
			$this->response_html .= '<p>Por favor ingrese una dirección de email</p>';
			$this->response_status = 0;
		}
		
		// Check valid email
		if($this->email && !$this->validateEmail())
		{
			$this->response_html .= '<p>Por favor ingrese una dirección de email válida</p>';
			$this->response_status = 0;
		}
		
		// Check message length
		if(!$this->message || strlen($this->message) < $this->message_min_length)
		{
			$this->response_html .= '<p>Ingrese su  mensaje. Debe contener al menos '.$this->message_min_length.' caracteres</p>';
			$this->response_status = 0;
		}
	}


	private function sendEmail(){
		// Para enviar un correo HTML, debe establecerse la cabecera Content-type
        $mensaje = '<html>
			          <div>
			            <table border="0" cellpadding="10">
			              <thead style="background: #eeeeee;">
			                <tr><th colspan="2">Formulario de contacto web</th></tr>
			              </thead>
			              <tbody>
			                <tr style="background: #f6f6f6;">
			                  <td>Nombre</td>
			                  <td><strong>'.$this->name.'</strong></td>
			                </tr>
			                  <tr style="background: #f6f6f6;">
			                  <td>Email</td>
			                  <td><strong><a href="mailto:EMAIL" target="_blank">'.$this->email.'</a></strong></td>
			                </tr>
			                  <tr style="background: #f6f6f6;">
			                  <td>Consulta</td>
			                  <td><strong>'.$this->message.'</strong></td>
			                </tr>
			              </tbody>
			            </table>
			          </div>
			        </html>';
        $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$cabeceras .= 'From:Consulta formulario web<no_reply@luigidecarlo.com.ar>' . "\r\n";

		$mail = mail($this->email_admin, $this->subject, $mensaje,$cabeceras); //$this->message,
			 /*"From: ".$this->name." <".$this->email.">\r\n"
			."Reply-To: ".$this->email."\r\n"
			."X-Mailer: PHP/" . phpversion());*/
	
		if($mail)
		{
			$this->response_status = 1;
			$this->response_html = '<p>Gracias!</p>';
		}
	}


	function sendRequest(){
		$this->validateFields();
		if($this->response_status)
		{
			$this->sendEmail();
		}

		$response = array();
		$response['status'] = $this->response_status;	
		$response['html'] = $this->response_html;
		
		echo json_encode($response);
	}
}


$contact_form = new Contact_Form($_POST, $admin_email, $message_min_length);
$contact_form->sendRequest();

?>