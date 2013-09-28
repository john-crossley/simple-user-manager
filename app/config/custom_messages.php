<?php
/**
 * custom_messages.php
 *
 * This file allows you to define custom responses
 * without the need to modify any of the core code.
 *
 * Look at the defaul messages to see what {{keys}} are
 * available for you to use. If you have forgot then please
 * consult the original file custom_messages.php.bk
 */

/**
 * process.php
 *
 * Shown when a path is not found
 */
define('PATH_NOT_FOUND',
  "Path was not found, check and try again"
);

/**
 * admin/view.php
 *
 * When the user can now access the selected directories
 */
define('USER_CAN_NOW_ACCESS_DIR',
  "The user can now access the selected files. Remember to secure the file!"
);

/**
 * process.php
 *
 * Shown when the user supplied an invaid path (Protecting files)
 */
define('INVALID_PATH_SUPPLIED',
  "You have supplied a nonexistent path, please enter another one."
);

/**
 * process.php
 *
 * Shown when the user tries to send themselves a message
 */
define('CANNOT_SEND_YOURSELF_A_PERSONAL_MESSAGE',
  "Sorry but you cannot send yourself a personal message."
);

/**
 * messages.php
 */
define('MESSAGE_FEATURE_HAS_BEEN_DISABLED',
  "The messages feature has been disabled, you must re-enable this feature to use it."
);

/**
 * compose_messages.php
 */
define('USER_HAS_PERSONAL_MESSAGES_DISABLED',
  "Sorry, but the user has personal messages disabled."
);

/**
 * admin/view.php
 */
define('DELETE_USER_SUCCESS',
  "The user has been successfully removed from the database!"
);

/**
 * procsss.php
 *
 * Just a generic form success message
 */
define('TEMPLATE_SUCCESSFULLY_UPDATED',
  "The template has been updated successfully!"
);

/**
 * admin/new_user_group.php
 *
 * Shown when a user group has been added successfully
 */
define('USER_GROUP_SUCCESSFULLY_ADDED',
  "User group has been successfully added and can now be applied to members."
);

/**
 * admin/user_groups.php
 *
 * When a user has successfully removed a user group
 */
define('SUCCESSFULLY_REMOVED_USER_GROUP',
  "User groups have been successfully removed and no longer usable!"
);

/**
 * admin/user_groups.php
 *
 * When the user has succesfully updated a user group.
 */
define('SUCCESSFULLY_UPDATE_USER_GROUP',
  "The user group has been successfully updated!"
);

/**
 * view_message.php
 *
 * Called when a message has not been supplied.
 * What do we do show an empty page? I think not!
 */
define('UNABLE_TO_LOCATE_MESSAGE',
  "The message no longer exists, maybe it has been deleted?"
);

/**
 * process.php
 *
 * Shown when a user successfully deletes a message
 */
define('SUCCESSFULLY_DELETED_A_MESSAGE',
  "Success, selected {{messages}} have been removed!"
);

/**
 * admin/index.php
 *
 * Shown when the user tries to access an area they are not allowed.
 */
define('UNABLE_TO_ACCESS_THIS_AREA',
  "Sorry but your account is not able to access this area of the website"
);

/**
 * admin/index.php
 *
 * Shown when the user needs to be logged in to view a page
 */
define('MUST_BE_LOGGED_IN',
  "Sorry but you must be logged in to view this page"
);

/**
 * process.php
 *
 * Just a generic error message when form errors are found.
 */
define('GENERIC_FORM_ERROR_MESSAGE',
  "We have some errors, please review and try again"
);

/**
 * process.php
 *
 * When a user has received a new personal message email.
 */
// define('NEW_PERSONAL_MESSAGE',
//   "{{title}} | You have a new personal message"
// );

/**
 * process.php
 *
 * When a user has successfully sent another user a message
 */
define('SUCCESSFULLY_SENT_A_MESSAGE',
  "Your message has been successfully sent"
);

/**
 * process.php
 *
 * The subject for when a user account has been created from the admin panel
 */
// define('NEW_USER_ACCOUNT_FROM_ADMIN_PANEL',
//   "Your new Account | Welcome to {{system_name}}"
// );

/**
 * process.php
 *
 * Shown when the users profile has been updated from the admin panel
 */
define('USER_PROFILE_HAS_BEEN_UPDATED',
  "{{username}}'s profile has been successfully updated."
);

/**
 * process.php
 *
 * Shown when the user has some generic form validation errors.
 */
define('VALIDATION_ERRORS',
  "We have some validation errors, please check them!"
);

/**
 * process.php
 *
 * Only shown really when suspected tomfoolery
 */
define('UNABLE_TO_LOCATE_USER',
  "Oops, the system was unable to locate the requested member. Did you enter their name correctly?"
);

/**
 * process.php
 *
 * Tells the user that they failed the captcha
 */
define('CAPTCHA_FAILED',
  "Captcha failed! Please enter the correct sum and try again."
);

/**
 * logout.php
 *
 * Tells the user if they have been logged out.
 */
define('LOGGED_OUT',
  "{{username}} has been successfully logged out. See you soon!"
);

/**
 * process.php
 *
 * Shown when the users account is banned.
 */
define('BANNED_ACCOUNT',
  "Sorry {{username}} but this account has been banned!"
);

/**
 * process.php
 *
 * Shown when the user tries to login when their
 * account has not been activated.
 */
define('ACCOUNT_NOT_YET_ACTIVATED',
  "Sorry {{username}} your account is not yet active, check your
  spam folder for an activation email!"
);

/**
 * confirm.php
 *
 * Shown when the user has had a new password generated.
 */
define('NEW_PASSWORD_GENERATED',
  "Success, {{username}} we have generated you a new password!"
);

/**
 * process.php
 *
 * Shown when the user has entered their email address
 * on the login.php page (Forgot password field)
 */
define('FORGOT_EMAIL_SENT',
  "Success, {{username}} check your email for further instructions."
);

/**
 * process.php
 */
define('ACCOUNT_NOT_FOUND_BY_EMAIL',
  "Sorry but no record was found matching {{email}}"
);


/**
 * process.php | Line: 77
 *
 * Shown when the user uses a banned email extension
 */
define('BANNED_EMAIL_EXTENSION',
  "Sorry but you cannot use this email extension: {{extension}}"
);

/**
 * confirm.php | Line: 51
 *
 * When a user tries to verify an account that doesn't match
 * what message should be shown?
 */
define('INCORRECT_VERIFICATION_DETAILS',
  "Verification details do not match. Did you click the right link?"
);

/**
 * When the user has successfully verified their account
 * what message should be shown to them?
 */
define('ACCOUNT_VERIFIED',
  "Success {{username}}, you may now login!"
);

/**
 * Shown when the user forgets their password.
 * The first email they will receive instructing them
 * on how to reset their password.
 */
// define('FORGOT_PASSWORD_SUBJECT',
//   "Action Required | Forgot password Request for {{system_name}}"
// );

/**
 * process.php
 *
 * Shown when the users information has been updated.
 */
define('USER_DETAILS_HAVE_CHANGED',
  "FYI Your account has been updated at {{system_name}}"
);

/**
 * The subject for the email the user will
 * receive when they create a new account.
 */
define('WELCOME_SUBJECT',
  "Action Required | Welcome to {{system_name}}"
);



/**
 * Shown when an email address is in use. On the
 * register.php page.
 */
define('EMAIL_IN_USE',
  "{{email}} is currently in use!"
);

/**
 * Shown when the user has entered an invalid email address
 * On the register.php page
 */
define('INVALID_EMAIL_ADDRESS',
  "{{email}} seems to be invalid."
);

/**
 * Shown when the username is in use.
 */
define('USERNAME_IN_USE',
  "{{username}} is currently in use!"
);

/**
 * Shown when the user chooses a password too short.
 */
define('PASSWORD_TOO_SHORT',
  "Password must be 6 or more chars."
);

/**
 * Shown to the user when they enter invalid account
 * information.
 */
define('INVALID_USERNAME_AND_OR_PASSWORD',
  "Username and password do not match, please try again!"
);

define('RECORD_NOT_FOUND',
  "I'm sorry but {{record}} was not found in the database!"
);

define('USER_LOGGED_IN',
  "Welcome {{username}}, you are now logged in!"
);

define('CSRF_CHECK_FAILURE',
  "CSRF failure. You do not have permission to make this request!"
);

define('NO_WRITE_PERMISSIONS_FOR_CACHE_DIRECTORY',
  "Please ensure your cache directory is writable before you can receive notifications."
);


/**
 *  CUSTOM MESSAGES
 *  You can control what messages are shown by the system
 */

/**
 * process.php
 *
 * Shown when a user has just successfully registered
 */
define('NEW_USER_REGISTERED',
  "Welcome {{username}}! Please check your email for further instructions."
);

/**
 * process.php
 */
define('ERROR_OCCURRED_WHILE_PROCESSING_FORM',
  "Sorry but an error occurred while processing form, please try again."
);

define('LOGIN_FORM_DATA_NOT_SUPPLIED',
  "You must fill out all of the required fields."
);

define('SEARCH_TERM_TOO_SHORT',
  "Please user a longer search term, minimum 3 characters long."
);

define('SEARCH_FOUND_NO_RESULTS',
  "No results have been found using {{term}}. Maybe try another one?"
);

define('USER_PROFILE_NOT_FOUND',
  "Sorry but you requested a profile that doesn't exist."
);

/**
 * profile.php
 *
 * When the user wants to update their profile
 */
define('YOUR_ACCOUNT_HAS_BEEN_UPDATED',
  "Your account has been successfully updated."
);

/**
 * admin/view.php
 *
 * Shown when the system failed to update a user.
 */
define('UNABLE_TO_UPDATE_USER',
  "Sorry, unable to update the user's profile. Try again."
);

define('UNABLE_TO_RETRIEVE_NOTIFICATIONS',
  "Unable to retrieve notifications at the moment. Please try again soon."
);
