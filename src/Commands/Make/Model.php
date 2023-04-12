<?php

namespace WaxFramework\Artisan\Commands\Make;

class Model extends Make {
    protected static $defaultName = 'make:model';

    protected static $defaultDescription = 'Create a new model class';

    public function namespaces():array {
        return [
            explode( '\\', __NAMESPACE__ )[0],
            'App',
            'Models'
        ];
    }

    public function directories():array {
        return [
            'app',
            'Models',
        ];
    }

    public function uses_classes():array {
        return [
            'WaxFramework\App',
            'WaxFramework\Database\Resolver',
            'WaxFramework\Database\Eloquent\Model'
        ];
    }

    public function file_content() {
        return '<?php

namespace NamespaceName;

UsesClasses

class ClassName extends Model {
    public static function get_table_name():string {
        // return your table name here
    }

    public function resolver():Resolver {
        return App::$container->get( Resolver::class );
    }
}';
    }
}