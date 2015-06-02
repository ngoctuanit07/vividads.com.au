<?php 
		///testing email pop3 and IMAP4
		
		$user = 'ashfaq@tablethrows.com.au';
		$pass = 'abc123456';
		$host = '119.81.29.138';
		 
		
		// Connect to the pop3 email inbox belonging to $user
		// If you need SSL remove the novalidate-cert section
		$con = imap_open("{10.66.179.2:143/novalidate-cert}INBOX}",'ashfaq@tablethrows.com.au', 'abc123456') or print_r(imap_errors());
		 
		
		
		$MC = imap_check($con); 
		print_r($MC);
		exit;
		// Get the number of emails in inbox
		$range = "1:".$MC->Nmsgs; 
		
		// Retrieve the email details of all emails from inbox
		$response = imap_fetch_overview($con,$range); 
		
		// displays basic email info such as from, to, date, subject etc...
		foreach ($response as $msg) {
		
				echo '<pre>';
				var_dump($msg);
				echo '</pre><br>-----------------------------------------------------<br>';
		}
?>