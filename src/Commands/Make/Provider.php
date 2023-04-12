<?php

namespace WaxFramework\Artisan\Commands\Make;

class Provider extends Make {
    protected static $defaultName = 'make:provider';

    protected static $defaultDescription = 'Create a new service provider class';

    public function namespaces():array {
        return [
            explode( '\\', __NAMESPACE__ )[0],
            'App',
            'Providers'
        ];
    }

    public function directories():array {
        return [
            'app',
            'Providers',
        ];
    }
    
    public function uses_classes():array {
        return [
            'WaxFramework\Contracts\Provider'
        ];
    }

    public function file_content() {
        return '<?php

namespace NamespaceName;

UsesClasses

class ClassName implements Provider {
    public function boot() {
        //
    }
}';
    }
}