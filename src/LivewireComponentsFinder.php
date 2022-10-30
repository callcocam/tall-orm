<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Cms;

use Exception;
use ReflectionClass;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Livewire\Component;
use Symfony\Component\Finder\SplFileInfo;


class LivewireComponentsFinder
{
    protected $path;
    protected $paths;
    protected $files;
    protected $manifest;
    protected $manifestPath;

    public function __construct(Filesystem $files, $manifestPath, $path)
    {
        $this->files = $files;
        $this->paths = $path;
        $this->manifestPath = $manifestPath;
    }

    
    public function load()
    {
        if (! is_null($this->manifest)) {
            return $this->manifest;
        }

        if (! file_exists($this->manifestPath)) {
            $this->build();
        }

        return  $this->files->getRequire($this->manifestPath);
    }

    public function find($alias)
    {
        $manifest = $this->getManifest();

        return $manifest[$alias] ?? $manifest["{$alias}.index"] ?? null;
    }

    public function getManifest()
    {
        if (! is_null($this->manifest)) {
            return $this->manifest;
        }

        if (! file_exists($this->manifestPath)) {
            $this->build();
        }

        return $this->manifest = $this->files->getRequire($this->manifestPath);
    }

    public function build()
    {
        $manifest = collect();
        if($this->paths){
            foreach($this->paths as $path){
                $this->path = $path;
                $manifest->push($this->getClassNames()
                ->mapWithKeys(function ($class) {
                    if(Str::contains($class::getName(),'livewire')){
                        $name = Str::afterLast($class::getName(), 'livewire.');
                        $name = sprintf("tall::%s", $name);
                    }
                    else{
                        $name = $class::getName();
                    }
                    return [$name => $class];
                })->toArray());
    
            }
        }
        foreach($manifest as $values){
            foreach($values as $name=>$value){
                $this->manifest[$name] = $value;
            }
        }
        $this->write($this->manifest);

        return $this;
    }

    protected function write(array $manifest)
    {
        if (! is_writable(dirname($this->manifestPath))) {
            throw new Exception('The '.dirname($this->manifestPath).' directory must be present and writable.');
        }

        $this->files->put($this->manifestPath, '<?php return '.var_export($manifest, true).';', true);
    }

    public function getClassNames()
    {
        if (! $this->files->exists(data_get($this->path, 'path'))) {
            return collect();
        }
        return collect($this->files->allFiles(data_get($this->path, 'path')))
            ->map(function (SplFileInfo $file) {
                $namespace = str($file->getPathname())
                ->after(data_get($this->path, 'search'))
                ->replace(['/', '.php'], ['\\', ''])->__toString();
              
                return  sprintf("%s%s", data_get($this->path, 'namespace'), $namespace);
            })
            ->filter(function (string $class) {
                return is_subclass_of($class, Component::class) &&
                    ! (new ReflectionClass($class))->isAbstract();
            });
    }
}

