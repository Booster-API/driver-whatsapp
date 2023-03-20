<?php

namespace BotMan\Drivers\WhatsappWeb\Providers;

use Illuminate\Support\ServiceProvider;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\WhatsappWeb\WhatsappWebDriver;
use BotMan\Drivers\WhatsappWeb\WhatsappWebFileDriver;
use BotMan\Drivers\WhatsappWeb\WhatsappWebAudioDriver;
use BotMan\Drivers\WhatsappWeb\WhatsappWebPhotoDriver;
use BotMan\Drivers\WhatsappWeb\WhatsappWebVideoDriver;
use BotMan\Drivers\WhatsappWeb\WhatsappWebLocationDriver;
use BotMan\Drivers\WhatsappWeb\WhatsappWebContactDriver;

class WhatsappWebServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadDrivers();

        $this->publishes([
            __DIR__ . '/../../stubs/whatsapp-web.php' => config_path('botman/whatsapp-web.php'),
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../../stubs/whatsapp-web.php', 'botman.whatsapp-web');

        $this->loadRoutesFrom(__DIR__ . '/../../routes/botman.php');
    }

    /**
     * Load BotMan drivers.
     * @return void
     */
    protected function loadDrivers(): void
    {
        DriverManager::loadDriver(WhatsappWebDriver::class);
        DriverManager::loadDriver(WhatsappWebAudioDriver::class);
        DriverManager::loadDriver(WhatsappWebFileDriver::class);
        DriverManager::loadDriver(WhatsappWebLocationDriver::class);
        DriverManager::loadDriver(WhatsappWebContactDriver::class);
        DriverManager::loadDriver(WhatsappWebPhotoDriver::class);
        DriverManager::loadDriver(WhatsappWebVideoDriver::class);
    }
}
