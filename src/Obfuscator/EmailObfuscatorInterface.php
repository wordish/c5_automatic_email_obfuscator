<?php
namespace Concrete\Package\AutomaticEmailObfuscator\Src\Obfuscator;

interface EmailObfuscatorInterface
{
    public function getCallback($key);
    public function setCallback($key, $callback);
}