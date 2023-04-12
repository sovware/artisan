<?php

namespace WaxFramework\Artisan\Commands\Make;

use PHP_CodeSniffer\Tokenizers\PHP;
use WaxFramework\Artisan\Contracts\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\FormatterHelper;

abstract class Make extends Command {
    abstract public function file_content();

    abstract public function directories():array;

    abstract public function namespaces():array;

    public function uses_classes():array {
        return [];
    }

    protected function execute( InputInterface $input, OutputInterface $output ): int {
        $argument_name = strtolower( $this->get_called_class_name() );
        $class         = str_replace( ' ', '', ucwords( $input->getArgument( $argument_name ) ) );

        $class_parts = explode( '/', $class );

        $directories = $this->directories();
        $namespaces  = $this->namespaces();

        if ( count( $class_parts ) === 1 ) {
            $class = $class_parts[0];
        } else {
            $class_parts = array_map(
                function( $item ) {
                    return ucfirst( $item );
                }, $class_parts
            );

            $class = end( $class_parts );
            array_pop( $class_parts );

            array_push( $namespaces, ...$class_parts );
            array_push( $directories, ...$class_parts );
        }

        $class = ucfirst( $class );

        $directory_path = $this->artisan->root_dir . DIRECTORY_SEPARATOR . implode( DIRECTORY_SEPARATOR, $directories );
        $file_path      = $directory_path . DIRECTORY_SEPARATOR . $class . '.php';

            
        if ( ! file_exists( $directory_path ) ) {
            mkdir( $directory_path, 0777, true );
        } elseif ( file_exists( $file_path ) ) {
            echo PHP_EOL;
            $formatterHelper = new FormatterHelper();
            $output->writeln( $formatterHelper->formatBlock( "{$class } {$this->get_called_class_name()} Already Exists!", 'error', true ) );
            return Command::FAILURE;
        }

        $uses_classes     = static::uses_classes();
        $namespace_prefix = explode( '\\', __NAMESPACE__ )[0];
        $uses_classes     = array_map(
            function( $class )use( $namespace_prefix ) {
                return "use {$namespace_prefix}\\{$class};";
            }, $uses_classes
        );

        $file    = fopen( $file_path, "wb" );
        $content = (string) str_replace( ['NamespaceName', 'UsesClasses', 'ClassName'], [implode( '\\', $namespaces ), implode( PHP_EOL, $uses_classes ), $class], $this->file_content() );
        fwrite( $file, $content );
        fclose( $file );
        $output->writeln( "<info>{$class} {$this->get_called_class_name()} Created Successfully!</info>" );

        return Command::SUCCESS;
    }

    protected function configure() {
        $this->addArgument( strtolower( $this->get_called_class_name() ), InputArgument::REQUIRED, "{$this->get_called_class_name()} class name?" );
    }

    protected function get_called_class_name():string {
        $class = explode( '\\', get_called_class() );
        return end( $class );
    }
}