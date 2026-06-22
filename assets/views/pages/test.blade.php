<x-cord::layout title="Bem-vindo ao Cord">
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold mb-2 text-gray-900">Página de Teste do Cord</h2>
        <p class="text-gray-600 text-lg">Este é um ambiente de teste rápido para as variáveis do Tailwind CSS.</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="p-6 bg-primary text-white rounded-xl shadow-md transform transition hover:scale-105">
            <h3 class="text-xl font-bold mb-2">Cor Primária</h3>
            <p>Este cartão está usando a classe bg-primary.</p>
            <p class="mt-4 text-sm opacity-80">Por padrão, será azul se a variável --cord-primary não estiver definida na raiz do projeto (fallback).</p>
        </div>

        <div class="p-6 bg-secondary text-white rounded-xl shadow-md transform transition hover:scale-105">
            <h3 class="text-xl font-bold mb-2">Cor Secundária</h3>
            <p>Este cartão está usando a classe bg-secondary.</p>
            <p class="mt-4 text-sm opacity-80">Por padrão, será verde esmeralda (fallback).</p>
        </div>
    </div>
    
    <div class="mt-8 border-t pt-6">
        <h3 class="font-bold text-gray-800 mb-4">Como testar:</h3>
        <ul class="list-disc pl-5 text-gray-600 space-y-2">
            <li>Abra o arquivo <code>src/resources/views/components/layout.blade.php</code></li>
            <li>Descomente o bloco <code>:root</code> dentro da tag <code>&lt;style&gt;</code> para forçar uma nova cor nas variáveis CSS e veja a mágica acontecer!</li>
        </ul>
    </div>
</x-cord::layout>
