<?php
namespace Concrete\Package\AutomaticEmailObfuscator\Src\EmailObfuscator;

abstract class AbstractObfuscator implements ObfuscatorInterface
{

    abstract public function registerViewAssets();

    public function obfuscateMail($email)
    {
        return $email;
    }

    public function obfuscateMailtoLinkHref($href)
    {
        return $href;
    }

}