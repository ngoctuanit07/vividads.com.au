<?php 

$to = "sweetashfaq@gmail.com";
$subject = "Test mail from Vivivd Ads ";
$from = "sales@vividads.com.au";
$message = "Hello! This is a simple email message. Test from Ashfaq Ahmed";

// let's say you want to email a comma-separated-values
//$tofile = "col1,col2,col3\n";
//$tofile .= "val1,val1,val1\n";


//$filename  = "filename.csv";
$random_hash = md5(date('r', time())); 
$attachment = chunk_split(base64_encode($tofile)); 

$headers = "From: " . $from . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"\r\n"; 
$headers .= "X-Priority: 3\r\n";
$headers .= "X-MSMail-Priority: Normal\r\n";    
$headers .= "X-Mailer: PHP/" . phpversion();

$body = "--PHP-mixed-".$random_hash."\r\n";
$body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
$body .= "Content-Transfer-Encoding: 8bit\r\n";
$body .= $message;
$body .= "\r\n\r\n";
$body .= "\r\n--PHP-mixed-" . $random_hash . "\r\n\r\n";

$body .= "--PHP-mixed-".$random_hash."\r\n";
$body .= "Content-Transfer-Encoding: base64\r\n";
//$body .= "Content-Type: text/comma-separated-values; name=\"" . $filename . "\" charset=\"UTF-8\"\r\n"; 
$body .= "Content-Disposition: attachment; filename=\"" . $filename . "\"\r\n\r\n";
$body .= $attachment;
$body .= "\r\n--PHP-mixed-".$random_hash."--";

if (mail($to, $subject, $body, $headers)){
	
		echo 'Email has been sent';
		exit;
	}else{
		echo 'Oops Email has not been sent';
		exit;
		}

?>

