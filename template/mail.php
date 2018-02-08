<?php
//////////////////////////////////////////////////////////////////////////////////
//																				//
//		Do whatever you want with this mailing template.						//
//		You can make your own as well.											//
//																				//
//		Your template has to adhere the email template guidelines though.		//
//		You can check out this website for more information.					//
//		https://templates.mailchimp.com/getting-started/html-email-basics/		//
//																				//
//////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////
//																				//
//		This is also a basic email template.									//
//		Nothing really fancy going on here.										//
//		You will have to make your own if you don't like this default one.		//
//																				//
//////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////
//																				//
//		REQUIRED VARIABLES IN THIS MAILING TEMPLATE ARE AS IS					//
//		---------------------------------------------------------				//
//		$subject																//
//		$first_name																//
//		$this->twofa_key														//
//		$link																	//
//																				//
//////////////////////////////////////////////////////////////////////////////////
?>
<!doctype html>
<html>
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php print($subject); ?></title>
<style>
html, body {
	word-wrap: break-word;
}

.bold {
	font-weight: bold;
	text-transform: uppercase;
}

.key, .link {
	color: #AAAAAA;
	padding-left: 10px;
}
</style>
</head>

<body>
<h1><?php print($subject); ?></h1>
<p>Hello <?php print($first_name); ?>!</p>
<p>You have just requested a 2 Factor Authentication key. Click on the link below to go to the authentication page. From there, you can submit the key and you will be able to access your account.</p>
<p>Also, remember that this authentication key will expire in 1 hour of this email.</p>
<p>If you do not verify your account using the below link and authentication key within the hour, you will have to resend a new two factor authentication key and redo the process all over again.</p>
<hr>
<p><span class="bold">Key</span><span class="key"><?php print($this->twofa_key); ?></span></p>
<p><span class="bold">Link</span><span class="link"><a href="<?php print($link); ?>"><?php print($link); ?></span></p>
</body>
</html>