<?php
/**
 * @author John Crossley <hello@phpcodemonkey.com>
 * @copyright Copyright (c) 2013 - John Crossley (http://phpcodemonkey.com)
 */
class Validator
{
  /**
   * array(
   *   'username' => 'admin',
   *   'password' => 'pass1'
   * )
   */
  private $values = array();

  /**
   * Default class error messages array.
   * @var array
   */
  private $messages = array(
    'required'    => 'The :attribute field is required',
    'min'         => 'The :attribute should be a minimum of :min characters',
    'max'         => 'The :attribute should be a maximum of :max characters',
    'match'       => 'The :attribute fields do not match',
    'unique'      => 'The :attribute has already been taken',
    'valid_email' => ':email doesn\'t seem to be a valid email',
    'valid_url'   => ':url doesn\'t seem to be a valid URL',
    'banned'      => ':email is using a banned extension. Please use another email address.'
  );

  /**
   * Any custom error messages.
   * @var array
   */
  private $customAttributeMessages = array();

  /**
   * Stores a list of error messages that a
   * particular attribute has associated with it.
   * @var array
   */
  private $errorMessages = array();

  /**
   * Stores if an attribute has an error associated with it.
   * @var array
   */
  private $errors = array();

  /**
   * The make method kicks off all of the validation process using the
   * data provided to it.
   * @param  array  $data     The data to be validated. Typically this is
   * just the $_POST variable.
   * @param  array  $rules    The array of rules with keys matching the $data
   * keys. Eg: ($_POST)$data['username'] - $rules['username'] = array('required')
   * @param  array  $messages Any custom error messages you may want instead of the
   * class defaults. Eg: $messages['required.email'] = 'My custom required email error message'
   * @return NULL
   */
  public function make(array $data, array $rules = array(), array $messages = array())
  {
    // Do we have any custom messages?
    if (!empty($messages)) {
      foreach ($messages as $key => $value) {
        // Explode the key
        $key = explode('.', $key);
        if (count($key) > 1) {
          // What's the attribute Cindy?
          $attribute = $key[0];
          // Whats the rule this message should be applied to?
          $rule = $key[1];
          // Store
          $this->customAttributeMessages[$attribute][$rule] = $value;
        }
      }
      // Add them to the messages for reference or what ever.
      $this->messages = array_replace($this->messages, $messages);
    }

    // Loop through the data we need to validate
    foreach ($data as $attribute => $value) {

      // Set the value here bevause the value
      // may not be backed up during validation
      $_SESSION['FORM_ERRORS'][$attribute]['value'] = $value;

      // Store the value.
      $this->values[$attribute] = $value;

      // Check to see if a rule matches an attribute
      if (array_key_exists($attribute, $rules)) {
        foreach ($rules[$attribute] as $rule) {
          $this->validate($value, $rule, $attribute);
        }
      }

    }
    // Cleanup
    $this->removeAnyPasswords();
  }

  /**
   * Removes any password like attribute value.
   * @return NULL
   */
  private function removeAnyPasswords()
  {
    // pass([\S]+)?
    // Match something like a pass, password etc.
    foreach ($this->values as $attr => $value) {
      if (preg_match('/pass([\S]+)?/', $attr)) {
        unset($this->values[$attr]);
        unset($_SESSION['FORM_ERRORS'][$attr]['value']);
      }
    }
  }

  /**
   * Stores the error information about any given attribute.
   * @param  string $attribute The name of the attribute
   * @param  string $rule      The rule being applied to the attribute
   * @param  array  $data      The array of error information
   * @return NULL
   */
  private function storeErrorInformation($attribute, $rule, $data = array())
  {
    // Set an error for the attribute
    // Eg: $this->errors['username'] = true;
    $this->errors[$attribute] = true;

    $this->errorMessages[$attribute][$rule] = $this->createMessage($this->messages[$rule], $data, $rule, $attribute);

    // Store in a session
    $_SESSION['FORM_ERRORS'][$attribute]['error'] = true;
    $_SESSION['FORM_ERRORS'][$attribute]['message'] = $this->errorMessages[$attribute];
  }

  /**
   * Using the messages and custom messages this method replaces
   * the placeholders of the messages to personalise them.
   * @param  string $message    The untouch message
   * @param  string $attributes The attribute in question
   * @param  string $rule       The rule being applied
   * @param  string $attr       The name of the attribute
   * @return string             The modified error message string
   */
  private function createMessage($message, $attributes, $rule, $attr)
  {
    $tmp = $message;
    foreach ($attributes as $attribute => $value) {
      if (isset($this->customAttributeMessages[$attr][$rule])) {
        $tmp = $this->customAttributeMessages[$attr][$rule];
      }
      $tmp = preg_replace("/$attribute/", $value, $tmp);
    }
    return $tmp;
  }

  /**
   * Carries out the validation using the options configured.
   * @param string $value The current value being validated.
   * @param arraty $rule The rules to be applied to the current value.
   * @param string $attribute The name of the attribute being validated.
   * @return NULL
   */
  private function validate($value, $rule, $attribute)
  {
    $rule = explode(':', $rule);
    // Switch it out man and validate this SUKA!
    switch (strtolower($rule[0])) {
      /**
       * Validation for the required rule.
       */
      case 'required':
        if (empty($value)) {
          $this->storeErrorInformation($attribute, 'required', array(
            ':attribute' => $attribute
          ));
        }
        break;
      /**
       * Validation for the min rule
       */
      case 'min':
        if (strlen($value) <= $rule[1]-1) {
          $this->storeErrorInformation($attribute, 'min', array(
            ':attribute' => $attribute,
            ':min'       => $rule[1]
          ));
        }
        break;
      /**
       * Validation for the max rule
       */
      case 'max':
        if (strlen($value) > $rule[1]) {
          $this->storeErrorInformation($attribute, 'max', array(
            ':attribute' => $attribute,
            ':max'       => $rule[1]
          ));
        }
        break;
      /**
       * Validation for the valid_email rule
       */
      case 'valid_email':
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
          $this->storeErrorInformation($attribute, 'valid_email', array(
            ':email' => $value
          ));
        }
        break;
      /**
       * Validation for the banned rule
       */
      case 'banned':
        // Cheap way but works fine
        $bannedExtensions = explode(' ', $rule[1]);
        $extension = explode('@', $value);
        $extension = $extension[1];
        // Check to see if the clien supplied a banned extension
        if (in_array($extension, $bannedExtensions)) {
          $this->storeErrorInformation($attribute, 'banned', array(
            ':email' => $value
          ));
        }
        break;
      /**
       * Validation for the value_url rule
       */
      case 'valid_url':
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
          $this->storeErrorInformation($attribute, 'valid_url', array(
            ':url' => $value
          ));
        }
        break;
      /**
       * Validation for the unique rule
       */
      case 'unique':
        // Client may not have DB accessbile so rather than throwing
        // a nasty fatal we shall check to see if the DB class is
        // available
        if (class_exists('DB')) {
          // Suppress just for now...
          if (@DB::table($rule[1])->where($attribute, '=', '\''.$value.'\'')->count()->count > 0) {
            $this->storeErrorInformation($attribute, 'unique', array(
              ':attribute' => $attribute
            ));
          }
        } else throw new \Exception('[Error] DB Class has not been found!');
        break;
      /**
       * Validation for the match rule
       */
      case 'match':
        if (!isset($rule[1]))
          break; // I'm gone

        // Field data
        $firstField   = $attribute;
        $secondField  = $rule[1];

        // Store the values
        $firstFieldValue  = $this->values[$firstField];
        $secondFieldValue = $this->values[$secondField];

        if ($firstFieldValue !== $secondFieldValue) {
          $this->storeErrorInformation($attribute, 'match', array(
            ':attribute' => $secondField
          ));
        }
        break;
      /**
       * Add custom validation rules below in a new case statement
       */
      default:
        break;
    }
  }

  public function getAttributeErrorMessages($attribute)
  {
    if (isset($this->errorMessages[$attribute]))
      return $this->errorMessages[$attribute];
    return false;
  }

  /**
   * Checks to see if an attribute has any error messages
   * associated with it.
   * @param string $attribute The name of the attribute
   * @return mixed If the attribute has a message or any
   * number of messages asscociated with it, then the first message
   * in the array is returned.
   * @see Validator::allMessages() to return all error messages.
   */
  public function hasMessage($attribute)
  {
    return $this->hasMessageSession($attribute);
  }

  /**
   * Checks to see if an attribute has any error messages
   * associated with it using sessions.
   * @param string $attribute The name of the attribute
   * @return mixed If the attribute has a message or any
   * number of messages asscociated with it, then the first message
   * in the array is returned.
   * @see Validator::allMessages() to return all error messages.
   */
  public static function hasMessageSession($attribute)
  {
    if (isset($_SESSION['FORM_ERRORS'][$attribute])) {
      $data = $_SESSION['FORM_ERRORS'];
      if (isset($data[$attribute]['message'])) {
        $_SESSION['FORM_ERRORS'][$attribute]['message'] = null;
        // Return the first error message
        return reset($data[$attribute]['message']);
      }
    }
    return false;
  }

  /**
   * Checks to see if a particular attribute has a value
   * associated with it.
   * @param  string $attribute The name of the attribute
   * @return mixed If the attribute is found then it is returned.
   * However, if no attribute is found false is returned.
   */
  public function hasValue($attribute)
  {
    return $this->hasValueSession($attribute); // Maybe?
  }

  /**
   * Checks to see if a particular attribute has a value
   * associated with it. This is again useful for requests
   * where the validation is being performed somewhere else.
   * @param  string  $attribute The name of the attribute
   * @return mixed Returns value if one is found, if not then
   * false is returned.
   */
  public static function hasValueSession($attribute)
  {
    if (isset($_SESSION['FORM_ERRORS'][$attribute])) {
      $data = $_SESSION['FORM_ERRORS'];
      if (isset($data[$attribute]['value'])) {
        $_SESSION['FORM_ERRORS'][$attribute]['value'] = null;
        return $data[$attribute]['value'];
      }
    }
    return false;
  }

  /**
   * Checks to see if a particular attribute has an
   * error associated with it.
   * @param  string  $attribute The attribute to check
   * Eg: 'username', 'password', 'email', etc.
   * @return boolean True if error found, false if not.
   */
  public function hasError($attribute)
  {
    return $this->hasErrorSession($attribute);
  }

  /**
   * Checks to see if a FORM_ERROR session has been set
   * for a particular attribute. This is useful for requests
   * away from the form page.
   * @param  string  $attribute The name of the attribute to check.
   * @return boolean True if the attribute has an error, false if not.
   */
  public static function hasErrorSession($attribute)
  {
    if (isset($_SESSION['FORM_ERRORS'][$attribute])) {
      $data = $_SESSION['FORM_ERRORS']; // tmp
      if (isset($data[$attribute]['error'])) {
        $_SESSION['FORM_ERRORS'][$attribute]['error'] = null; // Kill it
        return true;
      }
    }
    return false;
  }

  /**
   * Test to see if the validator has passed.
   *
   * @example
   * if ($validator->passes()) { ... }
   *
   * @return bool true on success
   */
  public function passes()
  {
    return (empty($this->errors)) ? true : false;
  }

  /**
   * Test to see if the validator has failed.
   *
   * @example
   * if ($validator->fails()) { ... }
   *
   * @return bool true on success
   */
  public function fails()
  {
    return (!empty($this->errors)) ? true : false;
  }
}
