<?php

use Artesaos\SEOTools\Providers\SEOToolsServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    SEOToolsServiceProvider::class
];
