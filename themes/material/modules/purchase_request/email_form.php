<!DOCTYPE html> 
<html lang = "en"> 

   <head> 
      <meta charset = "utf-8"> 
      <title>CodeIgniter Email Example</title> 
   </head>
	
   <body> 
      <?php 
         echo $this->session->flashdata('email_sent'); 
         echo form_open('/Email_controller/send_mail'); 
         echo $this->email->print_debugger();
      ?> 
		
      <input type = "email" name = "email" required /> 
      <input type = "submit" value = "SEND MAIL"> 
		
      <?php 
         echo form_close(); 
      ?> 
   </body>
	
</html>