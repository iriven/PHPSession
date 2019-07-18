<?php
namespace Iriven\Plugin\Sessions\Flash;
use Iriven\Plugin\Sessions\Interfaces\FlashMessagesInterface;
use  Iriven\Plugin\Sessions\Session;

class FlashMessages implements  FlashMessagesInterface {
    /**
     * Message types and shortcuts
     */
    const INFO    = 'i';
    const SUCCESS = 's';
    const WARNING = 'w';
    const ERROR   = 'e';

    /**
     * Default message type
     */
    const defaultType = self::INFO;

    /**
     * @var array
     */
    private $msgTypes = [
        self::ERROR   => 'error',
        self::WARNING => 'warning', 
        self::SUCCESS => 'success', 
        self::INFO    => 'info', 
    ];

    /**
     * Each message gets wrapped in this
     * @var string
     */
    private $MessageWrapper = '<div class="%s">%s</div>';

    /**
     * Prepend to each message (inside of the wrapper)
     * @var string
     */
    private $MessagePrefix = '';

    /**
     * Append to each message (inside of the wrapper)
     * @var string
     */
    private $MessageSuffix  = '';

    /**
     * HTML for the close button
     *
     * @var string
     */
    private $closeBtn  = '<button type="button" class="close" 
                                data-dismiss="alert" 
                                aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>';

    /**
     * Sticky Msg CSS Classes
     * @var string
     */
    private $stickyCssClass = 'sticky';

    /**
     * Msg CSS Classes
     * @var string
     */
    private $msgCssClass = 'alert dismissable';

    /**
     * Message types and order
     * @var array
     */
    private $cssClassMap = [ 
        self::INFO    => 'alert-info',
        self::SUCCESS => 'alert-success',
        self::WARNING => 'alert-warning',
        self::ERROR   => 'alert-danger',
    ];
    /**
     * @var Session
     */
    private $iSession;

    /**
     * @var string
     */
    private $flashLock = '_FlashMsg';
    /**
     * @var null
     */
    private $redirectUrl = null;

    /**
     * FlashMessages constructor.
     *
     * @param Session $session
     * @param string|null $lock
     */
    public function __construct(Session $session, $lock = null)
    {
        $this->iSession = $session;
        $this->flashLock = !empty($lock)? sha1($lock) : sha1($this->flashLock);
        if(!$this->iSession->has($this->flashLock)) $this->iSession->set($this->flashLock,[]);
    }

    /**
     * Add an info message
     * 
     * @param  string  $message      The message text
     * @param  string  $redirectUrl  Where to redirect once the message is added
     * @param  boolean $sticky       Sticky the message (hides the close button)
     * @return object
     * 
     */
    public function info($message, $redirectUrl=null, $sticky=false)
    {
        return $this->add($message, self::INFO, $redirectUrl, $sticky);
    }

    /**
     * Add a success message
     * 
     * @param  string  $message      The message text
     * @param  string  $redirectUrl  Where to redirect once the message is added
     * @param  boolean $sticky       Sticky the message (hides the close button)
     * @return object
     * 
     */
    public function success($message, $redirectUrl=null, $sticky=false)
    {
        return $this->add($message, self::SUCCESS, $redirectUrl, $sticky);
    }

    /**
     * Add a warning message
     * 
     * @param  string  $message      The message text
     * @param  string  $redirectUrl  Where to redirect once the message is added
     * @param  boolean $sticky       Sticky the message (hides the close button)
     * @return object
     * 
     */
    public function warning($message, $redirectUrl=null, $sticky=false)
    {
        return $this->add($message, self::WARNING, $redirectUrl, $sticky);
    }

    /**
     * Add an error message
     * 
     * @param  string  $message      The message text
     * @param  string  $redirectUrl  Where to redirect once the message is added
     * @param  boolean $sticky       Sticky the message (hides the close button)
     * @return object
     * 
     */
    public function error($message, $redirectUrl=null, $sticky=false)
    {
        return $this->add($message, self::ERROR, $redirectUrl, $sticky);
    }

    /**
     * Add a sticky message
     * 
     * @param  string  $message      The message text
     * @param  string  $redirectUrl  Where to redirect once the message is added
     * @param  string  $type         The $msgType
     * @return object
     * 
     */
    public function sticky($message, $redirectUrl=null, $type=self::defaultType)
    {   
        return $this->add($message, $type, $redirectUrl, true);
    }
    /**
     * Redirect the user if a URL was given
     * 
     * @return object 
     * 
     */
    private function doRedirect()
    {   
        if ($this->redirectUrl) {
            header('Location: ' . $this->redirectUrl);
            exit();
        }
        return $this;
    }

    /**
     * Add a flash message to the session data
     * 
     * @param  string  $message      The message text
     * @param  string  $type         The $msgType
     * @param  string  $redirectUrl  Where to redirect once the message is added
     * @param  boolean $sticky       Whether or not the message is stickied 
     * @return object
     * 
     */
    public function add($message, $type=self::defaultType, $redirectUrl=null, $sticky=false) 
    {
        if (isset($message[0])){
            if (strlen(trim($type)) > 1) $type = strtolower($type[0]);
            if (!array_key_exists($type, $this->msgTypes)) $type = self::defaultType;
            $flashMessages = $this->iSession->get($this->flashLock,[]);
            if (!array_key_exists( $type, $flashMessages )) $flashMessages[$type] = [];
            $flashMessages[$type][] = ['sticky' => $sticky, 'message' => $message];
            $this->iSession->set($this->flashLock, $flashMessages);
        }
        if (!is_null($redirectUrl)) $this->redirectUrl = $redirectUrl;
        $this->doRedirect();
        return $this;
    }

      /**
     * Clear the messages from the session data
     * 
     * @param  mixed  $types  (array) Clear all of the message types in array
     *                        (string)  Only clear the one given message type
     * @return object 
     * 
     */
    private function clear($types=[]) 
    { 
        if (!$types) $this->iSession->remove($this->flashLock);
        else{
            if (!is_array($types)) $types = [$types];
            $flashMessages = $this->iSession->get($this->flashLock,[]);
            foreach ($types as $type) 
                unset($flashMessages[$type]);
            if($flashMessages) $this->iSession->set($this->flashLock, $flashMessages);
            else $this->iSession->remove($this->flashLock);
        }
        return $this;
    }
    /**
     * See if there are any queued message
     * 
     * @param  string  $type  The $msgType
     * @return boolean
     * 
     */
    public function hasMessages($type=null) {
        $flashMessages = $this->iSession->get($this->flashLock,[]);
        if ($type) 
        {
            $type = strtolower(trim($type[0]));
            return isset($flashMessages[$type]); 
        } 
        foreach (array_keys($this->msgTypes) as $type) 
        {
            if (isset($flashMessages[$type])) return true; 
        }
        return false;
    }
    /**
     * Format a message
     * 
     * @param  array  $msgDataArray   Array of message data
     * @param  string $type           The $msgType
     * @return string                 The formatted message
     * 
     */
    private function formatMessage($msgDataArray, $type)
    {
        $type = isset($this->msgTypes[$type]) ? $type : self::defaultType;
        $cssClass = $this->msgCssClass . ' ' . $this->cssClassMap[$type];
        $MessagePrefix = $this->MessagePrefix;
        // If sticky then append the sticky CSS class
        if ($msgDataArray['sticky']) {
            $cssClass .= ' ' . $this->stickyCssClass;
        // If it's not sticky then add the close button
        } else {
            $MessagePrefix = $this->closeBtn . $MessagePrefix;
        }
        // Wrap the message if necessary
        $formattedMessage = $MessagePrefix . $msgDataArray['message'] . $this->MessageSuffix; 
        return sprintf(
            $this->MessageWrapper.PHP_EOL, 
            $cssClass, 
            $formattedMessage
        );
    }

        /**
     * Display the flash messages
     * 
     * @param  mixed   $types   (null)  print all of the message types
     *                          (array)  print the given message types
     *                          (string)   print a single message type
     * @param  boolean $print   Whether to print the data or return it
     * @return string
     * 
     */
    public function display($types=null, $print=true) 
    {
        $output = '';
        if (!$this->iSession->has($this->flashLock)) return $output;
        if (!$types) 
            $types = array_keys($this->msgTypes);
        if (!is_array($types)) $types = [$types];
        $types = array_map(function($v){
            if (strlen(trim($v)) > 1) $v = strtolower(trim($v[0]));
            return $v;
        },$types);
        $flashMessages = $this->iSession->get($this->flashLock,[]);
        foreach ($types as $type) {
            if (!isset($flashMessages[$type]) || empty($flashMessages[$type]) ) continue;
            foreach( $flashMessages[$type] as $msgData ) {
                $output .= $this->formatMessage($msgData, $type);
            }
            $this->clear($type);            
        }
        if ($print) 
            print($output); 
        return $output; 
    }

/**
     * Set the HTML that each message is wrapped in
     * 
     * @param string $MessageWrapper The HTML that each message is wrapped in. 
     *                           Note: Two placeholders (%s) are expected.  
     *                           The first is the $msgCssClass,
     *                           The second is the message text.
     * @return object
     * 
     */
    public function setMessageWrapper($MessageWrapper='')
    {
        $this->MessageWrapper = $MessageWrapper;
        return $this;
    }

    /**
     * Prepend string to the message (inside of the message wrapper)
     * 
     * @param string $MessagePrefix string to prepend to the message
     * @return object
     * 
     */
    public function prependToMessage($MessagePrefix='')
    {
        $this->MessagePrefix = $MessagePrefix;
        return $this;
    }

    /**
     * Append string to the message (inside of the message wrapper)
     * 
     * @param string $MessageSuffix string to append to the message
     * @return object
     * 
     */
    public function appendToMessage($MessageSuffix='')
    {
        $this->MessageSuffix = $MessageSuffix;
        return $this;
    }

    /**
     * Set the HTML for the close button
     * 
     * @param string  $closeBtn  HTML to use for the close button
     * @return object
     * 
     */
    public function setCloseBtn($closeBtn='')
    {
        $this->closeBtn = $closeBtn;
        return $this;
    }

    /**
     * Set the CSS class for sticky notes
     * 
     * @param string  $stickyCssClass  the CSS class to use for sticky messages
     * @return object
     * 
     */
    public function setStickyCssClass($stickyCssClass='')
    {
        $this->stickyCssClass = $stickyCssClass;
        return $this;
    }

    /**
     * Set the CSS class for messages
     * 
     * @param string $msgCssClass The CSS class to use for messages
     * 
     * @return object 
     * 
     */
    public function setMsgCssClass($msgCssClass='')
    {
        $this->msgCssClass = $msgCssClass;
        return $this;
    }

    /**
     * Set the CSS classes for message types
     *
     * @param mixed  $msgType    (string) The message type 
     *                           (array) key/value pairs for the class map
     * @param mixed  $cssClass   (string) the CSS class to use
     *                           (null) not used when $msgType is an array
     * @return object 
     * 
     */
    public function setCssClassMap($msgType, $cssClass=null) 
    {

        if (!is_array($msgType) ) {
            // Make sure there's a CSS class set
            if (is_null($cssClass)) return $this;
            $msgType = [$msgType => $cssClass];
        }

        foreach ($msgType as $type => $cssClass) {
            $this->cssClassMap[$type] = $cssClass;
        }

        return $this;
    }
}