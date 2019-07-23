# PHPSession

[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XDCFPNTKUC4TU)
[![Build Status](https://travis-ci.org/iriven/PHPSession.svg?branch=master)](https://travis-ci.org/iriven/PHPSession)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/iriven/PHPSession/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/iriven/PHPSession/?branch=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/iriven/PHPSession/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Code Coverage](https://scrutinizer-ci.com/g/iriven/PHPSession/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/iriven/PHPSession/?branch=master)

The Iriven PHP Session class endeavors to make it easy to use basic session best practices in PHP scripts.


### Setup
-----------------
 Add a `composer.json` file to your project:

```javascript
{
  "require": {
      "Iriven/PHPSession": "v1.0.0"
  }
}
```

Then provided you have [composer](http://getcomposer.org) installed, you can run the following command:

```bash
$ composer.phar install
```

That will fetch the library and its dependencies inside your vendor folder. Then you can add the following to your
.php files in order to use the library (if you don't already have one).

```php
require 'vendor/autoload.php';
```

Then you need to `use` the relevant class, and instantiate the class. For example:


### Getting Started
-----------------
```php
require 'vendor/autoload.php';
use \Iriven\Plugin\Sessions\PHPSession;

$session = new PHPSession();
```

### Features
-----------------

- [x] Protects against fixation attacks by regenerating the ID periodically.
- [x] Prevents session run conditions caused by rapid concurrent connections (such as when Ajax is in use).
- [x] Locks a session to a user agent and ip address to prevent theft.
- [x] Supports users behind proxies by identifying proxy headers in requests.
- [x] Easy to create, manage, and destroy session values.
- [x] supports flash messages
- [x] HTTPOnly session cookie
- [x] Session fingerprint validation
- [x] supports PHP objects vars storage

### Examples
-----------------

##### Logging in. (login.php)
```php
<?php
    use \Iriven\Plugin\Sessions\PHPSession;
    require 'vendor/autoload.php';


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
            $session->start(30); // Register for 30 minutes inactive delay.
            $session->set('_CurrentUser', $user);
            $session->flash()->success('Login OK.');
            header('location: '.$session->referer('index.php'));
            exit;
        } else {
            $session->flash()->error('Invalid login.');
        }
    }
?>

 $session->flash()->display();
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
        if(!$session->isStarted())
        {
            $session->saveReferer($_SERVER['REQUEST-URI']);
            header('location: login.php');
            exit;
        }
        elseif(!$session->isValid()) 
        {
            $session->close();
            header('location: login.php');
            exit;
        } 
        // Keep renewing the session as long as they keep taking action.
        $session->regenerate();
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

### Authors
-----------------

* **Alfred TCHONDJO** - *Project Initiator* - [iriven France](https://www.facebook.com/Tchalf)

#### License
-----------------

This project is licensed under the GNU General Public License V3 - see the [LICENSE](LICENSE) file for details

### Donation
-----------------

If this project help you reduce time to develop, you can give me a cup of coffee :)

[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XDCFPNTKUC4TU)

### Disclaimer
-----------------

If you use this library in your project please add a backlink to this page by this code.

```html

<a href="https://github.com/iriven/PHPSession" target="_blank">This Project Uses Alfred's TCHONDJO PHPSession Library.</a>
```
