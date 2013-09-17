<?php
/**
 * Email
 *
 * A simple and easy way to send emails. That's it!
 */
class Email {
  /**
   * Who is the email from?
   * @var string
   */
  protected $from = '';

  /**
   * Who is the email going to?
   * @var string
   */
  protected $to = '';

  /**
   * What is the subject of the email?
   * @var string
   */
  protected $subject = '';

  /**
   * What is the email contents?
   * @var string
   */
  protected $message = '';

  /**
   * A list of any errors that may have occurred during
   * the emailing process.
   * @var array
   */
  protected $errors = array();

  /**
   * Did it send?
   * @var boolean
   */
  protected $sent = false;

  /**
   * Validates an email address using PHPs built in
   * validation function........... Simple!
   * @param  string $email The email address to validate
   * @return bool
   */
  public function isValidEmail($email)
  {
    // PHPs built in validatoooorrrr
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) return true;

    // Invalid
    return false;
  }

  /**
   * Gets the list of errors, if any exist.
   * @return mixed Returns an array of errors if any. If not then false.
   */
  public function getErrors()
  {
    if (!empty($this->error))
      return $this->error;

    return false;
  }

  /**
   * Shows the generated content, useful for debugging.
   * @return mixed Compiled message or false
   */
  public function view()
  {
    if (!empty($this->message))
      return $this->message;

    return false;
  }

  /**
   * Builds and formats the name of the sender or recipient.
   * @param  string $email The email of the sender or recipient
   * @param  string $name  The name of the sender or recipient
   * @return string
   */
  protected function buildName($email, $name = '')
  {
    if (strlen($name) > 0)
      return strip_tags($name) . ' <' . $email . '>';
    else
      return $email;
  }

  /**
   * The to function allows the user to add a recipients
   * email address.
   * @param  string $to   The recipients email address
   * @param  string $name The name of the recipient
   * @return this
   */
  public function to($to, $name = null)
  {
    if (!$this->isValidEmail($to)) {
      $this->error[] = 'The recipients email address is invalid: ' . $to;
      return $this;
    }
    $this->to = $this->buildName($to, $name);
    return $this;
  }

  /**
   * The from function, allows the client to add a senders
   * email address.
   * @param  string $from The senders email address
   * @param  string $name The name of the sender
   * @return this
   */
  public function from($from, $name = '')
  {
    if (!$this->isValidEmail($from)) {
      $this->error[] = 'The senders email address is invalid: ' . $from;
      return $this;
    }
    $this->from = $this->buildName($from, $name);
    return $this;
  }

  /**
   * Sets the subject for the email address
   * @param  string $subject The subject of the email
   * @return this
   */
  public function subject($subject)
  {
    $this->subject = strip_tags($subject);
    return $this;
  }

  /**
   * The email templates you would like to use.
   * @param  string $file The location of the file.
   * @param  array $data The data for the template eg. ['username' => $username]
   * @return NULL
   */
  public function template($file, array $data)
  {
    // Assign some data
    $data['subject'] = $this->subject;
    $data['email']   = $this->from;

    // Check to see if the file exists. (Correct path supplied)
    $contents = file_get_contents($file);

    // Loop through the data and replace the placeholders
    foreach ($data as $key => $value)
      $contents = preg_replace("/{{{$key}}}/", $value, $contents);

    // Set the message with the new contents
    $this->message = $contents;

    return $this;
  }

  /**
   * The message of the email
   * @param  string $message The email message
   * @return this
   */
  public function message($message)
  {
    // No cleanup just incase this is an HTML email.
    $this->message = $message;
    return $this;
  }

  /**
   * Clears all the set fields of the email address.
   * @return NULL
   */
  public function clear()
  {
    $this->to = '';
    $this->from = '';
    $this->subject = '';
    $this->message = '';
  }

  /**
   * An expressive way for the client to check
   * if the mail has been sent.
   * @return boolean
   */
  public function sent()
  {
    return $this->sent;
  }

  /**
   * Send the email man Rock and Roll!
   * @return boolean
   */
  public function send()
  {
    // If we have errors then we can't continue.
    if (!empty($this->error)) {
      $this->error[] = 'Unable to send the email address because of errors.';
      $this->sent = false;
      return false;
    }
    // 'Content-type: text/plain;
    $headers = 'MIME-Version: 1.0' . "\r\n" .
      'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
      'From: ' . $this->from . "\r\n" .
      'Reply-To: ' . $this->from . "\r\n" .
      'X-Mailer: PHP/' . phpversion();
      // Right now send the email
    if (mail($this->to, $this->subject, $this->message, $headers)) {
      $this->sent = true;
      return true;
    }
    // Errors!
    $this->error[] = 'Faild to send the email - Check your configuration.';
    return false;
  }

}
