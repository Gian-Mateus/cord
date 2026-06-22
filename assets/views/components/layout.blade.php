<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Cord Package Layout' }}</title>

    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Carrega o CSS compilado da aplicação hospedeira (onde o Tailwind v4 está rodando) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen bg-background text-foreground grid grid-cols-[auto_1fr] grid-rows-[auto_1fr_auto]">
   
    <!-- HEADER (Navegação) -->
    <!-- col-span-2 faz ele ocupar a tela toda na horizontal -->
    <header class="col-span-2 bg-surface border-b border-border px-6 py-4 flex items-center justify-between sticky top-0 z-10">
        <h1 class="text-xl font-bold text-primary">Cord Admin</h1>
        <div class="text-sm text-muted">Usuário Logado</div>
    </header>

    <!-- SIDEBAR (Menu Lateral) -->
    <!-- Fica na primeira coluna (auto). O 'w-64' fixa o tamanho em 256px -->
    <aside class="bg-surface border-r border-border w-64 p-4 hidden md:block">
        <nav class="space-y-2">
            <a href="#" class="block px-4 py-2 rounded-(--radius) bg-primary/10 text-primary font-medium">Dashboard</a>
            <a href="#" class="block px-4 py-2 rounded-(--radius) text-muted hover:bg-surface-muted hover:text-foreground transition-colors">Configurações</a>
            <a href="#" class="block px-4 py-2 rounded-(--radius) text-muted hover:bg-surface-muted hover:text-foreground transition-colors">Relatórios</a>
        </nav>
    </aside>

    <!-- MAIN (Conteúdo da Página) -->
    <!-- Fica na segunda coluna (1fr), ocupando todo o espaço restante -->
    <main class="p-6 overflow-y-auto">
        {{ $slot }}
    </main>

    <!-- FOOTER (Rodapé) -->
    <!-- col-span-2 para ocupar tudo. py-2 para ser bem pequeno em altura -->
    <footer class="col-span-2 bg-surface-muted border-t border-border px-6 py-2 text-center text-xs text-muted">
        Cord Admin &copy; {{ date('Y') }} - Desenvolvido com Laravel
    </footer>

</body>
</html>
