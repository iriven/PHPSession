<?php
namespace Iriven\Plugin\Sessions;
use Iriven\Plugin\Sessions\Flash\FlashMessages;
use Iriven\Plugin\Sessions\Interfaces\SessionInterface;

class PHPSession implements SessionInterface {
    /**
     * @var
     */
    private $sessionFlash;
    /**
     * @var string
     */
    private $sessionLock;

    /**
     * Session constructor.
     * @param bool $autoStart
     * @param int $idle
     */
    public function __construct(int $idle=60, bool $autoStart=false)
    {
        if(!$this->isStarted())
        {
            ini_set( 'session.use_strict_mode', true);
            ini_set( 'session.use_cookies', true);
            ini_set( 'session.cookie_httponly', true);
            ini_set( 'session.use_only_cookies', true);
            ini_set( 'session.cache_limiter', '');
            ini_set( 'session.cache_expire', false);
            ini_set( 'session.lazy_write', true);
        }
        $this->sessionLock = $this->generateSessionKey();
        if($autoStart) $this->start($idle);
        if(rand(1, 100) <= 10) $this->regenerate();
    }

    /**
     * Start session if session has not started
     *
     * @param int $idle
     * @return $this
     */
    public function start(int $idle=60)
    {
        try {
            if(!$this->isStarted())
            {
                $idle = intval($idle);
                $idle >= 10 and $idle <= 60 ?: $idle = 60;
                if (!session_start())
                    throw new \RuntimeException('Failed to start the session');
                $this->set('_SessionId', session_id());
                $this->set('_SessionIdle', $idle);
                $this->set('_SessionTimeout', $this->setTimeout());

                if (!$this->has('_PadLock'))
                    $this->set('_PadLock', sha1($this->sessionLock));
            }
        }
        catch (\Throwable $e)
        {
            echo $e->getMessage();
        }
        return $this;
    }

    /**
     * count of the session vars
     *
     * @return int
     */
    public function count()
    {
        return count($this->all());
    }

    /**
     * checking session status
     *
     * @return bool
     */
    private function isStarted()
    {
        if ( php_sapi_name() !== 'cli' ) 
        {
            if ( version_compare(phpversion(), '5.4.0', '>=') ) 
                return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
            return session_id() === '' ? FALSE : TRUE;
        }
        return FALSE;
    }

    /**
     * Retrieve the global session variable.
     *
     * @return array
     */
    public function all()
    {
        if(!$this->isStarted()) return [];
        return array_map('unserialize', $_SESSION[$this->sessionLock]);
    }

    /**
     * Add value to a session
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        if($this->isStarted())
        {
            if (!empty( $value))
                $_SESSION[$this->sessionLock][$key] = serialize($value);
        }
        return $this;
    }

    /**
     * Get item from session.
     *
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        if(!$this->isStarted()) return null;
        return $this->has($key) ? unserialize($_SESSION[$this->sessionLock][$key]) : $default;
    }

    /**
     * Checks to see if a session item exists.
     *
     * @param $key
     * @return bool
     */

    public function has($key)
    {
        if (!$this->isStarted()) return false;
        return isset($_SESSION[$this->sessionLock][$key]);
    }

    /**
     * Extract session item, delete session item and finally return the item
     *
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function pull($key, $default = null)
    {
        if ($this->has($key)){
            $value = $this->get($key);
            $this->remove($key);
            return $value ;
        }
        if(!$this->isStarted()) return null;
        return $default;
    }
    /**
     * Unset a session var
     *
     * @param $key
     * @return $this
     */
    public function remove($key)
    {
        if ($this->has($key))
            unset($_SESSION[$this->sessionLock][$key]);
        return $this;
    }

    /**
     * Generate a session token based on remote user data
     *
     * @return string
     */
    private function generateSessionKey()
    {
        $key = 'ù%)µ!Oa#?{z£=&2q[Q*}~|¤';
        $ip = isset($_SERVER['HTTP_CLIENT_IP']) ?
            $_SERVER['HTTP_CLIENT_IP'] : isset($_SERVER['HTTP_X_FORWARDE‌​D_FOR']) ?
            $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $userKey=hash_hmac('sha256', $ip, $key . $ip. $key. $_SERVER['HTTP_USER_AGENT'].(ip2long($ip) & ip2long('255.255.0.0')), true);
        return  sha1(serialize($userKey . $ip . $_SERVER['HTTP_USER_AGENT'] . $key));
    }

    /**
     * Return the referer URL if present, otherwise the `$AlternateUrl`.
     * After that, reset the referer attribute of the session.
     *
     * Example: in a 'sign in' action, I could have:
     *
     * <code>
     * redirect(Sessions::referer('/')
     * </code>
     *
     * in order to redirect to the referer if there is one, otherwise to `/`.
     *
     * @param string $AlternateUrl
     * @return string
     */
    public function referer($AlternateUrl = '/')
    {
        return $this->pull('_PreviousUrl', $AlternateUrl);
    }

    /**
     * Flash messages manager
     *
     * @return FlashMessages
     */
    public function flash()
    {
        if(!$this->sessionFlash)
        $this->sessionFlash= new FlashMessages($this, md5($this->sessionLock));
        return $this->sessionFlash;
    }

    /**
     * Set the referer to a given string. The referer will be used to redirect
     * requests after logging a user in.
     *
     * @param $value
     * @return $this
     */
    public function saveReferer($value)
    {
        $this->set('_PreviousUrl', $value);
        return $this;
    }

    /**
     * Gets the id for the current session.
     *
     * @return mixed|null
     */
    public function getSessionId()
    {
        return $this->get('_SessionId');
    }

    /**
     * Checks to see if the session is over based on the amount of time given.
     *
     * @return bool
     */
    public function isValid()
    {
        if(!$this->isStarted()) return false;
        if ($this->get('_SessionTimeout') <= $this->CurrentTimestamp())
            return false;
        return  sha1($this->sessionLock) === $this->get('_PadLock');
    }

    /**
     * Renews the session when the given time is not up and there is activity on the site.
     *
     * @return $this
     */
    public function regenerate()
    {
        if ($this->isStarted())
        {
            $this->set('_SessionTimeout', $this->setTimeout());
            if (!headers_sent())
            {
                session_regenerate_id(false);
                $newSession = session_id();
                session_write_close();
                $this->set( '_SessionId', session_id($newSession));
                session_start();
            }
        }
        return $this;
    }

    /**
     * Returns the current time.
     *
     * @return int timestamp
     */
    private function CurrentTimestamp()
    {
        $currentHour = date('H');
        $currentMin = date('i');
        $currentSec = date('s');
        $currentMon = date('m');
        $currentDay = date('d');
        $currentYear = date('y');
        return mktime($currentHour, $currentMin, $currentSec, $currentMon, $currentDay, $currentYear);
    }
    /**
     * Generates new time.
     *
     * @return int timestamp
     */
    private function setTimeout()
    {
        $currentHour = date('H');
        $currentMin = date('i');
        $currentSec = date('s');
        $currentMon = date('m');
        $currentDay = date('d');
        $currentYear = date('y');
        $idle = $this->get('_SessionIdle');
        return mktime($currentHour, ($currentMin + $idle), $currentSec, $currentMon, $currentDay, $currentYear);
    }

    /**
     * Destroys the session.
     */
    public function close()
    {
        if($this->isStarted())
        {
            if ( isset( $_COOKIE[session_name()] ) )
                setcookie( session_name(), '', time()-3600, '/' );
            session_start();
            session_destroy();
        } 
        $_SESSION = [];
    }

    /**
     * magic method Set key/value in session.
     *
     * @param string $key
     * @param $value
     */
    public function __set($key, $value){$this->set($key, $value);}

    /**
     * magic method to retrieve datas.
     *
     * @param string $key
     * @return mixed|null
     */
    public function __get($key){return $this->get($key);}

}
