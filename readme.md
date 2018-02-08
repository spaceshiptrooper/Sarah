# Sarah

Sarah is a 2 Factor Authentication library built in `PHP`.

---

### Requirements

---

- PHP 5.4+
- `MySQL` database
- `composer`

---

### Prerequisites

---

#### Install Composer

---

##### NOTE

---

Before we can start using `Sarah`, we must install `composer` in order to grab `Sarah`'s dependencies. `Sarah` is dependent on the `PHPMailer` library to send emails. If you already have an up-to-date version of `PHPMailer`, you can skip this part. However, you will have to move the `PHPMailer` into the `vendor` directory if you have not already done that. `Sarah` looks for `PHPMailer` in the `vendor` directory.

---

##### Linux & Mac

---

1. The first thing we need is `composer` so we shall download it from this website. https://getcomposer.org/download/
2. Next step is to use the codes on that page. If you are unfamiliar with `Linux` and `Mac`, you have to open up your terminal. Once in the terminal, type the lines in order; 1 by 1.

3. We will copy the `composer-setup.php` file from the official `composer` website by using the below code.

```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
```

4. We check to make sure that there was no error in downloading the `composer-setup.php` file from the `composer` website using the below code.

```
php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
```

5. We set up our `composer-setup.php` file. This will download a new file called `composer.phar` which is the file we need to run the actual `composer` commands using the below code.

```
php composer-setup.php
```

6. We then delete the `composer-setup.php` file since we don't need it anymore in the below code.

```
php -r "unlink('composer-setup.php');"
```

7. Here, we are moving the `composer.phar` file to global use. So we can do commands such as `composer require composer.json` instead of `/home/user/Downloads/composer.phar require composer.json`.

```
sudo mv composer.phar /usr/local/bin/composer
```

---

##### Windows

---

`Composer` on `Windows` is actually a little simpler. You just need to download the `Composer-Setup.exe` file located on https://getcomposer.org/doc/00-intro.md#installation-windows. Once you open up that application, you start the installation process. You will have to locate your `php.exe` file and `composer` will start installing. If you want to make a global use like the `Linux` and `Mac` one, you'll have to add the location of the `composer.phar` file in your `PATH` variables.

To do this follow these steps.

1. Open up a file file explorer.
2. Right click on This PC.
3. From the context menu, select the option `Properties`.
4. This should bring up a new window that's in the settings which basically lists your Window's version and what the specs are to it and what not.
5. On the left sidebar, click on the option `Advanced system settings`. This will bring up another new window.
6. From this window, click on the `Advanced` tab.
7. While in the `Advanced` tab, look for the `Edit` button.
8. Click on it, it'll allow you to edit your paths.
9. Add the exact path to your `composer.phar` file into the text box and ending it with a `;`. This semicolon will separate each individual value as an actual path so be cautious what you use the semicolon for. More paths means more semicolons.

---

##### Importing the database

---

`Sarah` uses `PDO` as her database `API`. Assuming you understand what that means, we are using the `MySQL` server. So if you have access to your `MySQL` databases, create a database and copy&paste the below code into your new database. A `GUI` such as `phpMyAdmin` should suffice.

```
CREATE TABLE `two_factor_authentication` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `authkey` text DEFAULT NULL,
  `hash` text DEFAULT NULL,
  `ip` varchar(100) DEFAULT NULL,
  `agent` varchar(255) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `status` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `two_factor_authentication`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `two_factor_authentication`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
```

---

### Installing `Sarah`

---

In the terminal (`Linux`, `Windows`, and `Mac`), type in

```
composer require spaceshiptrooper/sarah
```

This will create a `composer.json` file in the current directory you are in and then download `Sarah`'s files along with `PHPMailer`. This process might take a few minutes because we are requiring both `Sarah`'s files and `PHPMailer` so there's a lot of files that are needed. Just wait and be patient. If you receive an error please submit it [here](https://github.com/spaceshiptrooper/Sarah/issues).

---

Once you have finally downloaded `Sarah`'s files and `PHPMailer`, your directory should look something similar to this.

```
vendor
  |___
      |----- composer
      |
      |----- ircmaxell
      |
      |----- phpmailer
      |
      |----- spaceshiptrooper
      |
      |----- autoload.php
```

The `composer` directory and the cache strings within the `composer` files are generated from the `composer` command. `ircmaxell` is a requirement and downloaded from `https://github.com/ircmaxell/password_compat`. This is for backwards compatibility and support for users who are using `PHP 5.4` and lower. Lowest support for this library is 5.3.7 due to some buggy issues with lower versions than that. If you are using `PHP 5.5` and higher, then you don't have to worry about this library since `PHP 5.5` has this function built-in. So this library will be ignored. The `spaceshiptrooper` directory is where `Sarah` lies. The `autoload.php` file is also generated from `composer`. However, some respositories require the `autoload.php` file while some don't. `ircmaxell`, `phpmailer`, and `Sarah` all don't require `autoload.php`, but it comes with the `composer` command anyways. So that's fine.

---

### How to use `Sarah`

---

The next step is to include `Sarah`'s `bootstrap.php` file in your application. You should only call `Sarah`'s `sendMail()` method during a `POST` request. Sending it via a `GET` request can be redundant and unnecessary.

The below is to instantiate `Sarah`'s class.

```
<?php
session_start(); // Start the session

use \Sarah\Sarah;

// Require Sarah's bootstrap file.
require_once('vendor/spaceshiptrooper/sarah/bootstrap.php');

// Instantiate Sarah's class and pass the required parameters in the constructor.
$sarah = new Sarah([
	'SMTP_HOST' => '', // Your SMTP's host.
	'SMTP_EMAIL' => '', // Your SMTP email.
	'SMTP_PASSWORD' => '', // Your SMTP password.
	'SMTP_PORT' => 465, // Your SMTP's port (usually 465 or 587).
	'SMTP_FROM' => 'Noreply', // You can put this reply-from as anybody you want.
	'SMTP_SECURE' => 'ssl', // Use either tls or ssl. Only use ssl if your SMTP server allows it.
	'SUCCESS_DIE' => false, // true or false. Set this to true only if you want to die the entire page after succession.
	'CALLBACK_URL' => '', // Callback URL should be the URL you want to return the user to upon succession.
	'DATABASE' => [
		'TYPE' => 'mysql', // Don't change this if you aren't using other database types.
		'DB_HOST' => 'localhost', // Your database server.
		'DB_USERNAME' => '', // Your database's username.
		'DB_PASSWORD' => '', // Your database's password.
		'DB_DATABASE' => '', // Your actual database you are working in.
		'COST' => 10, // The cost password_hash needs
	]
]);
```

---

### A little side note

By default, the cost for `password_hash` is 10. However, you can specify as much cost as you want. The more the cost, the more secure the password is, however, the more resource it takes. The less the cost, the less secure the password is. And the less resource it takes. It's really up to you to find the balance between the 2.

---

You can use the below code in your `POST` request.

```
$sarah->sarah->setCharacterAmount(10); // The amount of characters you want in the authentication key.
$sarah->sarah->sendMail([
	'email_subject' => 'Just a test', // The email subject you want to send to the user.
	'user_id' => 1, // The user's ID. This should be from your database if you are using user IDs.
	'first_name' => 'Test', // The user's first name. This should be from your database if you are using first names.
	'email' => 'test@test.com' // The user's email. This should be from your database if you are using email addresses.
]);
```

To verify if the user has already been authenticated, a `$_SESSION` has been set upon authentication. So we can just check to see if that `$_SESSION` cookie has been set, if it has, the user has authenticated successfully.

```
if(isset($_SESSION['CALLBACK_SUCCESS'])) {

	// The user has already authenticated successfully.

} else {

	// Doesn't look like the user has authenticated at all.

}
```

This is where you can basically allow the user to login either through your own `$_SESSION` cookie or through something else.

---

### What is SMTP?

---

Simple Mail Transfer Protocol (SMTP) is the next thing to sending emails. The default `mail()` function that `PHP` has should **not** be relied on. This function **can** or **cannot** work at random times. There is **no** 100% guarantees when using the `mail()` function. What does this mean? It means that `PHP`'s `mail()` function **may** or **may not** send any emails at all to its specified destination. That is why `Sarah`'s choice of sending emails is through an SMTP library called `PHPMailer`.

---

#### What is the difference in usage between SMTP libraries and the mail() function?

---

There is a huge difference. SMTP requires multiple information. You need to provide the SMTP host, the SMTP username (also considered as SMTP email), the SMTP password, the SMTP port, and the SMTP protocol type (tls or ssl). Whereas, the `mail()` function only requires the receiver's email address, a subject line, and a message body.

Now. You may ask, "Why can't we just use the `mail()` function since it's so easy and takes less time to comprehend?" And the simple answer, "You just can't rely on it. There is no guarantee that it will even send any emails to your desired destination."

Even if it sends emails about 7/10 of the time, here's a topic I created since I saw this in my email subscription not too long ago. This relates to the use of the `mail()` function.

https://sitepoint.com/community/t/important-update-managing-email-addresses-for-php-asp-mail-scripts-whois-coms-friendly-reminder/274333

SMTP information isn't really too hard to come across honestly. You can actually use your `Gmail`, `Hotmail`, or `Ymail` for SMTP. Here are a few articles on how to setup SMTP using your `Gmail`, `Hotmail`, and `Ymail`. You can apply these same steps to `Sarah`.

https://wpsitecare.com/gmail-smtp-settings/ <-- `Gmail`

https://lifewire.com/what-are-windows-live-hotmail-smtp-settings-1170861 <-- `Hotmail`

https://help.yahoo.com/kb/SLN4724.html <-- `Ymail`

---

Once you are done setting up `Sarah`, give it a twirl. Hopefully you didn't break it! Enjoy.
