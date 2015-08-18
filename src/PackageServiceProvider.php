<?php
namespace Concrete\Package\AutomaticEmailObfuscator\Src;

use Core;
use Config;
use Concrete\Core\Support\Facade\Events;
use Concrete\Core\Foundation\Service\Provider;

class PackageServiceProvider extends Provider
{
    public function register()
    {
        $this->app->singleton('automatic_email_obfuscator/obfuscator', function () {
            $method = Core::make('helper/text')->camelcase(Config::get('app.obfuscator.method', 'html'));
            $obfuscatorClass = '\Concrete\Package\AutomaticEmailObfuscator\Src\Obfuscator\EmailObfuscator' . $method;
            return new $obfuscatorClass;
        });
    }

    public function registerEvents()
    {
        Events::addListener('on_page_output', function ($event) {
            $helper = new EmailObfuscationHelper();
            $helper->handle($event);
        });
    }

    public function registerAssets()
    {
        Core::make('automatic_email_obfuscator/obfuscator')->registerViewAssets();
    }
}