<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\VoltServiceProvider::class,
    ...app()->environment('local') 
        ? [App\Providers\TelescopeServiceProvider::class] 
        : [],
];
