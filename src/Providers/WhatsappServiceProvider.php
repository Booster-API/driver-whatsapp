<?php

namespace BotMan\Drivers\WhatsappWeb\Providers;

use Illuminate\Support\ServiceProvider;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\WhatsappWeb\WhatsappDriver;
use BotMan\Drivers\WhatsappWeb\WhatsappFileDriver;
use BotMan\Drivers\WhatsappWeb\WhatsappAudioDriver;
use BotMan\Drivers\WhatsappWeb\WhatsappPhotoDriver;
use BotMan\Drivers\WhatsappWeb\WhatsappVideoDriver;
use BotMan\Drivers\WhatsappWeb\WhatsappLocationDriver;
use BotMan\Drivers\WhatsappWeb\WhatsappContactDriver;

class WhatsappServiceProvider extends ServiceProvider
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
            __DIR__ . '/../../stubs/whatsapp.php' => config_path('botman/whatsapp.php'),
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../../stubs/whatsapp.php', 'botman.whatsapp');
    }

    /**
     * Load BotMan drivers.
     * @return void
     */
    protected function loadDrivers(): void
    {
        DriverManager::loadDriver(WhatsappDriver::class);
        DriverManager::loadDriver(WhatsappAudioDriver::class);
        DriverManager::loadDriver(WhatsappFileDriver::class);
        DriverManager::loadDriver(WhatsappLocationDriver::class);
        DriverManager::loadDriver(WhatsappContactDriver::class);
        DriverManager::loadDriver(WhatsappPhotoDriver::class);
        DriverManager::loadDriver(WhatsappVideoDriver::class);
    }
}
