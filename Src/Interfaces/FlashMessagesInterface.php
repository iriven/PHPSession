<?php
/**
 * Created by PhpStorm.
 * User: Iriventeam
 * Date: 16/07/2019
 * Time: 10:41
 */

namespace Iriven\Plugin\Sessions\Interfaces;


interface FlashMessagesInterface
{
    public function info($message, $redirectUrl=null, $sticky=false);
    public function success($message, $redirectUrl=null, $sticky=false);
    public function warning($message, $redirectUrl=null, $sticky=false);
    public function error($message, $redirectUrl=null, $sticky=false);
    public function sticky($message, $redirectUrl=null, $type=null);
    public function add($message, $type=null, $redirectUrl=null, $sticky=false) ;
    public function hasMessages($type=null);
    public function display($types=null, $print=true);
    public function setMessageWrapper($MessageWrapper='');
    public function prependToMessage($MessagePrefix='');
    public function appendToMessage($MessageSuffix='');
    public function setCloseBtn($closeBtn='');
    public function setStickyCssClass($stickyCssClass='');
    public function setMsgCssClass($msgCssClass='');
    public function setCssClassMap($msgType, $cssClass=null);
}