<?php

use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class BladeFactory
{
    protected $factory;
    protected $compiler;
    protected $filesystem;
    protected $viewsPath;
    protected $cachePath;
    protected $languagePath;
    protected $componentsPath;
    protected $locale = 'en';
    protected $translations = [];

    protected static $instance;

    public function __construct($viewsPath = null, $cachePath = null, $componentsPath = null, $languagePath = null)
    {
        $this->filesystem = new Filesystem;
        $this->viewsPath = $viewsPath;
        $this->cachePath = $cachePath;
        $this->componentsPath = $componentsPath;
        $this->languagePath = $languagePath;
        self::$instance = $this;
    }

    public function setViewsPath($path)
    {
        $this->viewsPath = $path;
        return $this;
    }

    public function setCachePath($path)
    {
        $this->cachePath = $path;
        return $this;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
        $this->loadTranslations();
        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    protected function loadTranslations()
    {
        $path = $this->languagePath . '/' . $this->locale . '.php';
        if (file_exists($path)) {
            $this->translations = require $path;
        }
    }

    protected function initialize()
    {
        if (!$this->viewsPath || !$this->cachePath) {
            throw new \RuntimeException('Views and cache paths must be set before rendering.');
        }

      
        $container = new Container();
        $eventDispatcher = new Dispatcher($container);

        // $eventDispatcher = new Dispatcher(new Container);

        // Set up the Blade compiler
        $this->compiler = new BladeCompiler($this->filesystem, $this->cachePath);

        $this->compiler->directive('csrf', function () {
            return "<?php insert_token(); ?>";
        });

        $this->compiler->directive('lang', function ($expression) {
            return "<?php echo lang($expression); ?>";
        });

       
        $this->compiler->component('Illuminate\View\AnonymousComponent');

        // Set up the view finder with multiple possible extensions
        $viewFinder = new FileViewFinder($this->filesystem, [$this->viewsPath], ['blade.php', 'php']);

        // Set up the engine resolver
        $engineResolver = new EngineResolver;
        $engineResolver->register('blade', function () {
            return new CompilerEngine($this->compiler);
        });
        $engineResolver->register('php', function () {
            return new Illuminate\View\Engines\PhpEngine($this->filesystem);
        });

        // Set up the factory
        $this->factory = new Factory(
            $engineResolver,
            $viewFinder,
            $eventDispatcher
        );

        $this->factory->addNamespace('components', $this->componentsPath);
        
        $this->factory->share('__translate', [$this, 'translate']);
        
        $container->bind('__translate', function () {
            return [$this, 'translate'];
        });
    }

    public static function getInstance()
{
    return self::$instance;
}

    public function make($view, $data = [], $mergeData = [])
    {
        if (!$this->factory) {
            $this->initialize();
        }
        try {
            //code...
            return $this->factory->make($view, $data, $mergeData);
        } catch (\InvalidArgumentException $e) {
            if (str_contains($e->getMessage(), 'View [') && str_contains($e->getMessage(), '] not found')) {
                // Return the 404 error view instead
                return $this->factory->make('errors.404');
            }
        }
    }

    public function compile($value)
    {
        if (!$this->compiler) {
            $this->initialize();
        }
        return $this->compiler->compileString($value);
    }

    public function directive($name, $handler)
    {
        if (!$this->compiler) {
            $this->initialize();
        }
        $this->compiler->directive($name, $handler);
    }

    public function if($name, $callback)
    {
        if (!$this->compiler) {
            $this->initialize();
        }
        $this->compiler->if($name, $callback);
    }

    public function translate($key, $replace = [])
    {
        $translation = $this->translations[$key] ?? $key;
        
        foreach ($replace as $key => $value) {
            $translation = str_replace(":$key", $value, $translation);
        }
        
        return $translation;
    }
}

// Move this function outside of the class
if (!function_exists('lang')) {
    function lang($key, $replace = [])
    {
        $factory = BladeFactory::getInstance();
        return $factory->translate(strtolower($key), $replace);
    }
}