# PHPSession
===============

The Iriven PHP Session class endeavors to make it easy to use basic session best practices in PHP scripts.

### Features
-----------------

* Protects against fixation attacks by regenerating the ID periodically.
* Prevents session run conditions caused by rapid concurrent connections (such as when Ajax is in use).
* Locks a session to a user agent and ip address to prevent theft.
* Supports users behind proxies by identifying proxy headers in requests.
* Easy to create, manage, and destroy session values.
* HTTPOnly session cookie
* Session fingerprint validation
* supports PHP objects vars storage

### Examples
-----------------

##### Logging in. (login.php)
```php
<?php
    use \Iriven\Plugin\Sessions\PHPSession;
    require 'vendor/autoload.php';

    $errors = array();

    // You'll definitely want to add more validation here and check against a
    // database or something. This is just an example.
    if (! empty($_POST)) {
        if ($_POST['username'] == 'user' && $_POST['password'] == 'pwd') {
            $session = new PHPSession();

            // You can define what you like to be stored.
            $user = array(
                'user_id' => 1,
                'username' => $_POST['username']
            );

            $session->start(120); // Register for 2 hours.
            $session->set('_CurrentUser', $user);
            header('location: index.php');
            exit;
        } else {
            $errors[] = 'Invalid login.';
        }
    }
?>

// Your form here.
```


##### Secure area once authenticated. (index.php/controller/whatever)
```php
<?php
    use \Iriven\Plugin\Sessions\PHPSession;
    require 'vendor/autoload.php';

    $session = new PHPSession();
        // Check to see if the session has expired.
        // If it has, end the session and redirect to login.
        if (!$session->isValid()) {
            $session->close();
            header('location: login.php');
            exit;
        } else {
            // Keep renewing the session as long as they keep taking action.
            $session->regenerate();
        }
?>
```


##### Logging out. (logout.php)
```php
<?php
    use \Iriven\Plugin\Sessions\PHPSession;
    require 'vendor/autoload.php';

    $session = new PHPSession();
    $session->close();
    header('location: login.php');
    exit;
?>
```
