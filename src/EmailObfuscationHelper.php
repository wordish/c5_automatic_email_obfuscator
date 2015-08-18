<?php
namespace Concrete\Package\AutomaticEmailObfuscator\Src;

use Core;
use Concrete\Core\Page\Page;
use Symfony\Component\EventDispatcher\Event;

class EmailObfuscationHelper
{
    public function registerViewAssets()
    {
        Core::make('automatic_email_obfuscator/obfuscator')->registerViewAssets();
    }

    /**
     * Handles the event's content
     * obfuscates emails if current page is not admin area
     *
     * @param Event $event
     * @return Event
     */
    public function handle(Event $event)
    {
        $viewText = $event->getArgument('contents');
        $p = Page::getCurrentPage();

        if (!$p->isAdminArea()) {
            $event->setArgument('contents', $this->findAndObfuscate($viewText));
        }

        return $event;
    }

    public function findAndObfuscate($content)
    {
        // Regexps modified from the MIT licenced "Transparent Email Obfuscation" add-on:
        // http://www.concrete5.org/marketplace/addons/transparent-email-obfuscation/
        $content = preg_replace_callback('/(<a[^>]*)(href=")(mailto:)([^"]+)([^>]*>)/',
            function ($matches) {
                return EmailObfuscationHelper::mailtoCallback($matches);
            },
            $content);

        $content = preg_replace_callback('/\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]+\b(?!([^<]+)?>)/i',
            function ($matches) {
                return EmailObfuscationHelper::mailCallback($matches);
            },
            $content);

        return $content;
    }

    public static function mailtoCallback($matches)
    {
        $cb = Core::make('automatic_email_obfuscator/obfuscator')->getCallback('mailto');
        if (method_exists(Core::make('automatic_email_obfuscator/obfuscator'), $cb)) {
            $parts = array($matches[1], $matches[2], $matches[3], $matches[4], $matches[5]);
            $linkCls = "obfuscated_link";
            $hasClass = false;
            if (strpos($parts[0], "class=\"") !== false) {
                $parts[0] = str_replace("class=\"", "class=\"" . $linkCls . " ", $parts[0]);
                $hasClass = true;
            }
            if (strpos($parts[4], "class=\"") !== false) {
                $parts[4] = str_replace("class=\"", "class=\"" . $linkCls . " ", $parts[4]);
                $hasClass = true;
            }
            if (!$hasClass) {
                $parts[0] = $parts[0] . 'class="' . $linkCls . '" ';
            }
            return $parts[0] . $parts[1] . call_user_func(array(
                Core::make('automatic_email_obfuscator/obfuscator'),
                $cb
            ),
                $parts[2] . $parts[3]) . $parts[4];
        }
    }

    public static function mailCallback($matches)
    {
        $cb = Core::make('automatic_email_obfuscator/obfuscator')->getCallback('email');
        if (method_exists(Core::make('automatic_email_obfuscator/obfuscator'), $cb)) {
            return '<span class="obfuscated_link_text">' . call_user_func(array(
                Core::make('automatic_email_obfuscator/obfuscator'),
                $cb
            ),
                $matches[0]) . '</span>';
        }
    }
}