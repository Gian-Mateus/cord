<?php

namespace Cord\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'cord:install';

    protected $description = 'Instala o ecossistema Cord, publicando assets nativos e registrando as importações de CSS/JS no host';

    public function handle(): int
    {
        $this->components->info('Instalando o ecossistema Cord UI...');

        // 1. Publicar assets nativos
        $this->components->task('Publicando assets nativos (cord.css, cord.js, cord-core.js)...', function () {
            $this->callSilent('vendor:publish', [
                '--tag' => 'cord-assets',
                '--force' => true,
            ]);

            return true;
        });

        // 2. Injetar importação do CSS
        $appCssPath = resource_path('css/app.css');
        if (file_exists($appCssPath)) {
            $this->components->task('Injetando importação no resources/css/app.css...', function () use ($appCssPath) {
                $content = file_get_contents($appCssPath);

                if (! str_contains($content, 'cord.css')) {
                    // Tenta injetar logo abaixo do import do Tailwind v4
                    if (str_contains($content, "@import 'tailwindcss';")) {
                        $content = str_replace(
                            "@import 'tailwindcss';",
                            "@import 'tailwindcss';\n@import \"./cord.css\";",
                            $content
                        );
                    } elseif (str_contains($content, '@import "tailwindcss";')) {
                        $content = str_replace(
                            '@import "tailwindcss";',
                            '@import "tailwindcss";\n@import "./cord.css";',
                            $content
                        );
                    } else {
                        // Se não encontrar, coloca no topo do arquivo
                        $content = "@import \"./cord.css\";\n".$content;
                    }

                    file_put_contents($appCssPath, $content);
                }

                return true;
            });
        } else {
            $this->components->warn('Arquivo resources/css/app.css não encontrado no host. Crie-o e adicione @import "./cord.css"; manualmente.');
        }

        // 3. Injetar importação do JS
        $appJsPath = resource_path('js/app.js');
        if (file_exists($appJsPath)) {
            $this->components->task('Injetando importação no resources/js/app.js...', function () use ($appJsPath) {
                $content = file_get_contents($appJsPath);

                if (! str_contains($content, 'cord.js') && ! str_contains($content, 'cord"')) {
                    $content .= "\nimport \"./cord.js\";\n";
                    file_put_contents($appJsPath, $content);
                }

                return true;
            });
        } else {
            $this->components->warn('Arquivo resources/js/app.js não encontrado no host. Crie-o e adicione import "./cord.js"; manualmente.');
        }

        $this->components->info('Instalação do Cord concluída com sucesso!');

        return self::SUCCESS;
    }
}
