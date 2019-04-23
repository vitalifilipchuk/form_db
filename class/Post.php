<?php
require_once('DBConnection.php');
require_once('Mailer.php');

class Post implements Mailer {
	private $name;
	private $surname;
	private $email;
	private $message;
	private $image;

	/**
	*Constructor method
	* @param string $name post sender name
	* @param string $surname post sender surname
	* @param string $email post sender email address, used to send a mail with data
	* @param string $message post message
	* @param string[]  $image contains uploaded image data
	*/
	public function __construct($name, $surname, $email, $message, $image) {
		$this->name = $name;
		$this->surname = $surname;
		$this->email = $email;
		$this->message = $message;
		$this->image = $image;
	}
	/**
	*Validating the form data
	*@return string[] $errors   an array with error messages
	*/
	public function validate() {
		$errors = array();
		if ((!is_string($this->name)) || (empty($this->name)) || (strlen($this->name) > 32)) {
			$errors['name'] = "Name should not be empty, be a string and not longer than 32 symbols.";
		}
		if ((!is_string($this->surname)) || (empty($this->surname)) || (strlen($this->surname) > 32)) {
			$errors['surname'] = "Surname should not be empty, be a string and not longer than 32 symbols.";
		}
		if ((!is_string($this->email)) || (empty($this->email)) || (strlen($this->email) > 64) || (!filter_var($this->email, FILTER_VALIDATE_EMAIL))) {
			$errors['email'] = "Email should not be empty, be valid and not longer than 64 symbols.";
		}
		if (!is_string($this->message)) {
			$errors['email'] = "Message should be string.";
		}
		if (!empty($this->image["tmp_name"])) {
			$imageFileType = strtolower(pathinfo($directory . basename($this->image['name']),PATHINFO_EXTENSION));
			if ((getimagesize($this->image["tmp_name"]) === false) || ($this->image["size"] > 10000000) || ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )) {
				$errors['image'] = "Image should have a correct file extension and not be bigger than 9.6Mb.";
			}
		}
		else {
			$errors['image'] = "Image is required.";
		}
		return $errors;
	}
	/**
	*Saving the form data in database and uploading image on server
	*/
	public function save() {
		/* Uploading image section */
		$directory = "images/";
		$uploadFile = $directory . time() . $this->image['name'];
	    if (move_uploaded_file($this->image["tmp_name"], $uploadFile)) {
	        $this->image = $uploadFile;
	    } else {
	        echo "Sorry, there was an error uploading your file.";
	    }
		/* DB operations section */
		$db = DBConnection::getInstance();
		$connection = $db->getConnection();
		//we use prepared statements to prevent SQL injections
		$statement = $connection->prepare("INSERT INTO posts (name, surname, email, message, image_path) VALUES (?, ?, ?, ?, ?)");
		$statement->bind_param("sssss", $st_name, $st_surname, $st_email, $st_message, $st_image_path);
		$st_name = $this->name;
		$st_surname = $this->surname;
		$st_email = $this->email;
		$st_message = $this->message;
		$st_image_path = $this->image;
		if ($statement->execute()) {
			//echo "Success<br>";
		}
		else {
			echo "Failed to save data to database.<br>";
		}
		$statement->close();
		$connection->close();
	}
	/**
	*Sending the data via email with image attachment
	*/
	public function _mail() {
		$to = $this->email;
		$subject = "Your info, " . $this->name . " " . $this->surname;
		$headers = "From: ivanivanovqw007@gmail.com" . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n" . "Content-Type: multipart/mixed; boundary=\"1a2a3a\"";
		$message .= "If you can see this MIME than your client doesn't accept MIME types!\r\n" . "--1a2a3a\r\n";
		$message .= "Content-Type: text/html; charset=\"iso-8859-1\"\r\n"
		."Content-Transfer-Encoding: 7bit\r\n\r\n"
		."<p>Name: ". $this->name ." </p>"
		."<p>Surname: ". $this->surname ." </p>"
		."<p>Email: ". $this->email ." </p>"
		."<p>Comment: ". $this->message ." </p> \r\n"
		."--1a2a3a\r\n";
		$attachment = file_get_contents($this->image);
		$imageFileType = strtolower(pathinfo(basename($this->image),PATHINFO_EXTENSION));
		$message .= "Content-Type: image/". $imageFileType ."; name=\"". $this->image ."\"\r\n"
			."Content-Transfer-Encoding: base64\r\n"
			."Content-disposition: attachment; file=\"". $this->image ."\"\r\n"
			."\r\n"
			.chunk_split(base64_encode($attachment))
			."--1a2a3a--";
		$success = mail($to,$subject,$message,$headers);
	    if (!$success) {
	    	echo "Mail to " . $to . " failed .";
	    } 
	    else {
	    	//echo "Success : Mail was send to " . $to ;
	    }
	}
		
}
?>