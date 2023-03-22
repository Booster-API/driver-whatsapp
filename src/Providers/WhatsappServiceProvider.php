<?php

namespace BoosterAPI\Whatsapp\Driver\Providers;

use Illuminate\Support\ServiceProvider;
use BotMan\BotMan\Drivers\DriverManager;
use BoosterAPI\Whatsapp\Driver\WhatsappDriver;
use BoosterAPI\Whatsapp\Driver\WhatsappFileDriver;
use BoosterAPI\Whatsapp\Driver\WhatsappAudioDriver;
use BoosterAPI\Whatsapp\Driver\WhatsappPhotoDriver;
use BoosterAPI\Whatsapp\Driver\WhatsappVideoDriver;
use BoosterAPI\Whatsapp\Driver\WhatsappLocationDriver;
use BoosterAPI\Whatsapp\Driver\WhatsappContactDriver;

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
