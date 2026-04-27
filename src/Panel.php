<?php

namespace Cord;

use Cord\Contracts\Registrable;
use Cord\Resources\Pages\ResourcePage;

class Panel
{
    protected string $id;
    protected string $path;
    protected array  $middleware  = ['web', 'auth'];
    protected string $layout      = 'cord::layouts.app';
    protected string $discoverPath = '';

    public function __construct(string $id)
    {
        $this->id   = $id;
        $this->path = $id;
    }

    // --- Configuração fluente ---

    public function path(string $path): static
    {
        $this->path = trim($path, '/');
        return $this;
    }

    public function middleware(array $middleware): static
    {
        $this->middleware = $middleware;
        return $this;
    }

    public function layout(string $layout): static
    {
        $this->layout = $layout;
        return $this;
    }

    public function discover(string $path): static
    {
        $this->discoverPath = $path;
        return $this;
    }

    // --- Getters ---

    public function getId(): string        { return $this->id; }
    public function getPath(): string      { return $this->path; }
    public function getMiddleware(): array  { return $this->middleware; }
    public function getLayout(): string    { return $this->layout; }

    // --- Discovery ---

    public function getDiscoveredClasses(): array
    {
        if (! is_dir($this->discoverPath)) {
            return [];
        }

        $classes = [];

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $this->discoverPath,
                \RecursiveDirectoryIterator::SKIP_DOTS
            )
        );

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $class = $this->pathToClass($file->getPathname());

            if (! class_exists($class)) {
                continue;
            }

            // Só classes que sabem se registrar
            if (! is_subclass_of($class, Registrable::class)) {
                continue;
            }

            // ResourcePages não se autorregistram —
            // o Resource pai registra as rotas delas
            if (is_subclass_of($class, ResourcePage::class)) {
                continue;
            }

            $classes[] = $class;
        }

        return $classes;
    }

    protected function pathToClass(string $filePath): string
    {
        $appPath  = realpath(app_path());
        $filePath = realpath($filePath);
        $relative = str_replace($appPath . DIRECTORY_SEPARATOR, '', $filePath);
        $relative = str_replace(DIRECTORY_SEPARATOR, '\\', $relative);
        $relative = str_replace('.php', '', $relative);

        return 'App\\' . $relative;
    }
}