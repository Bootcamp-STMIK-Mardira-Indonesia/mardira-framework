<?php

namespace App\Core\Commands\Concerns;

trait MakeCommand
{
    protected function make($name, $model = null)
    {
        $stub = file_get_contents($this->getStub());

        $replacements = $this->getReplacements($name, $model);

        $stub = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );

        $fileName = $this->getFileName($name);

        $filePath = $this->getFilePath($fileName);

        file_put_contents($filePath, $stub);
    }

    protected function getFilePath($fileName)
    {
        return $this->getNamespacePath() . '/' . $fileName;
    }

    protected function getNamespacePath()
    {
        $namespace = $this->getNamespace();

        $namespace = str_replace('\\', '/', $namespace);

        return $namespace;
    }

    protected function getReplacements($name, $model)
    {
        return [];
    }

    protected function getNamespace()
    {
        return '';
    }

    protected function getFileName($name)
    {
        return $name . '.php';
    }

    protected function getClassName($name)
    {
        return $name;
    }

    protected function getStub()
    {
        return '';
    }

    public function createFile($stub, $namespace, $fileName, $replacements)
    {
        $stub = file_get_contents($stub);

        $stub = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );

        $filePath = $this->getFilePath($namespace, $fileName);

        file_put_contents($filePath, $stub);
    }
}