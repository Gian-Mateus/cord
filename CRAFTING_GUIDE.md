# Guia de Desenvolvimento de Componentes Cord (Crafting Guide)

Este documento registra as decisões arquiteturais da **Cord** e serve como bússola e roadmap técnico para a construção dos nossos Builders de Componentes (Fields, Columns e Layouts).

---

## 1. Princípios de Design da Cord

Para manter a Cord ágil, sustentável e de altíssimo desempenho, toda a base de código deve seguir rigorosamente três diretrizes fundamentais de design.

### A. Padrão Fluent `build()` Limpo

Ao contrário de outros frameworks que aceitam construtores complexos com múltiplos argumentos posicionais ou arrays mágicos, a Cord adota uma postura de **instanciação pura e configuração fluente**.

* O método estático `build()` serve **estritamente** para instanciar a classe (`return new static()`).
* Construtores são **100% livres de parâmetros**.
* Toda e qualquer configuração de estado, propriedades ou comportamentos do componente é definida de forma fluente através de encadeamento de métodos.

#### Exemplos de Implementação

```php
// ❌ COMO NÃO FAZER (Construtor poluído e argumentos posicionais complexos)
$field = new TextInput('email', 'Endereço de E-mail', true);

// ❌ COMO NÃO FAZER (Array de configuração mágica sem auto-complete)
$field = TextInput::make([
    'name' => 'email',
    'label' => 'Endereço de E-mail',
    'required' => true,
]);

//  COMO FAZER NA CORD (Instanciação limpa e Fluent API)
$field = TextInput::build()
    ->bind('email')
    ->label('Endereço de E-mail')
    ->required();
```

Essa abordagem garante legibilidade máxima, facilidade de refatoração, suporte nativo avançado para IDEs (auto-complete completo) e flexibilidade para estender componentes sem quebrar assinaturas de construtores.

---

### B. Abordagem Anatômica "Estilo Shadcn" (blatUI)

A Cord não isola, compila ou encapsula CSS e JavaScript em pacotes pré-processados ou builders pesados como o Filament faz. Em vez disso, ela opera sob um modelo de **consumo e publicação anatômica de componentes Blade anônimos**, inspirando-se na filosofia do Shadcn UI.

* **Componentes Blade Anônimos**: Todos os templates HTML residem na pasta `assets/views/components/` como arquivos Blade padrão.
* **Estilização Global Nativa**: Os componentes utilizam classes utilitárias que se integram diretamente ao **Tailwind CSS v4** e **Livewire v4** globais do projeto hospedeiro.
* **Sem Compilação Interna**: Não há processos de build de CSS/JS internos ao pacote da Cord. O CSS e JS necessários são fornecidos como assets limpos que o compilador de assets principal do projeto (Vite/Tailwind) processa e estiliza nativamente.

---

### C. Estrutura Enxuta da Raiz

A raiz do pacote Cord é dividida de forma limpa entre a camada de representação visual (front-end) e a camada lógica de negócios do PHP.

```
cord/
├── assets/                  # Todo o ecossistema de Front-end (blatUI)
│   ├── css/                 # Arquivos CSS fundamentais (Tailwind v4 integrations)
│   ├── js/                  # Scripts e diretivas personalizadas do Alpine.js
│   ├── views/
│   │   ├── components/      # Componentes Blade anônimos atômicos e de layouts
│   │   └── layouts/         # Layouts de página e estruturas de painel
│   └── ...
└── src/                     # Lógica back-end do PHP e arquitetura da Cord
    ├── Components/          # Classes PHP de mapeamento dos builders
    ├── Providers/           # Service Providers para registrar caminhos de views e assets
    ├── Resources/           # Lógica puramente voltada a CRUDs, Models e Regras de Negócio
    └── ...
```

* **assets/**: É onde reside o visual do framework. O host publica ou consome diretamente esses arquivos Blade anônimos.
* **src/Resources/**: Contém a inteligência do CRUD. Não dita a estética dos componentes de forma acoplada, mas sim a lógica de carregamento, mutação de dados e regras de negócio.

---

## 2. Hierarquia Universal dos Builders

Para criar interfaces dinâmicas, organizamos nossos componentes de dentro para fora em uma hierarquia de responsabilidades claras. Cada nível envolve e adiciona inteligência ou estrutura ao nível anterior:

```
┌────────────────────────────────────────────────────────┐
│ 1. Telas (Pages / Resources)                            │
│    Mapeia rotas, gerencia estado global e une Form/Table│
│   ┌──────────────────────────────────────────────────┐  │
│   │ 2. Orquestradores (Form / Table)                 │  │
│   │    Gerenciam regras de validação e ciclos de vida│  │
│   │   ┌────────────────────────────────────────────┐ │  │
│   │   │ 3. Layouts & Containers (Flex / Grid)      │ │  │
│   │   │    Estruturam e alinham o espaço visual     │ │  │
│   │   │   ┌──────────────────────────────────────┐ │ │  │
│   │   │   │ 4. Componentes Atômicos (Fields/Cols)│ │ │  │
│   │   │   │    Entrada/saída de dados individuais│ │ │  │
│   │   │   └──────────────────────────────────────┘ │ │  │
│   │   └────────────────────────────────────────────┘ │  │
│   └──────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────┘
```

### Detalhamento da Linha de Desenvolvimento

1. **Componentes Atômicos (Fields & Columns)**:
   * Menores elementos de interface de dados.
   * *Exemplos*: `TextInput`, `Select`, `Toggle`, `DateColumn`, `BadgeColumn`.
   * *Responsabilidade*: Mapeamento de um campo específico no banco de dados, sua validação individual e formatação visual básica.
2. **Layouts & Containers**:
   * Estruturas semânticas ou de posicionamento que agrupam Componentes Atômicos.
   * *Exemplos*: `Flex`, `Grid`, `Section`, `Card`, `Tabs`.
   * *Responsabilidade*: Alinhamento de campos, agrupamento visual e responsividade, recebendo filhos através do método `->schema([...])`.
3. **Orquestradores (Form & Table)**:
   * Agregadores lógicos de Layouts e Componentes Atômicos.
   * *Exemplos*: `Cord\Forms\Form`, `Cord\Tables\Table`.
   * *Responsabilidade*: Monitorar ciclos de vida do Livewire, carregar registros (models), unificar e disparar regras de validação agregadas.
4. **Telas (Pages & Resources)**:
   * Controladores visuais de nível superior mapeados para rotas do Laravel.
   * *Exemplos*: `ListRecords`, `CreateRecord`, `EditRecord`.
   * *Responsabilidade*: Definir layout da página inteira, gerenciar ações globais (botões de topo, modais de confirmação) e persistir dados.

---

## 3. Plano de Execução Imediato (Fase Piloto)

Para validar a integridade da arquitetura fluente e a ponte de dados Blade-PHP, a Fase Piloto está dividida em 3 etapas consecutivas.

### Passo 1: Infraestrutura Base

Precisamos consolidar as classes abstratas no namespace `Cord\Support\Abstracts` (ou `Cord\Components`) de forma que elas apontem nativamente para os templates Blade em `assets/views/components/`.

```php
namespace Cord\Support\Abstracts;

use Cord\Support\Concerns\HasFluentApi;
use Cord\Support\Concerns\HasAttributes;
use Cord\Support\Concerns\EvaluatesClosures;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

abstract class Component implements Htmlable
{
    use HasFluentApi;      // Fornece o método estático build() e helpers fluentes
    use HasAttributes;     // Encapsula manipulação de atributos HTML
    use EvaluatesClosures;  // Permite parâmetros definidos via Closures dinamizadas

    protected string $view;

    abstract public function getView(): string;

    public function viewData(): array
    {
        return [
            'component'  => $this,
            'attributes' => $this->getAttributeBag(),
        ];
    }

    public function render(): View
    {
        return view($this->getView(), $this->viewData());
    }

    public function toHtml(): string
    {
        return $this->render()->render();
    }
}
```

```php
namespace Cord\Support\Abstracts;

abstract class Field extends Component
{
    protected string $bind;
    protected ?string $label = null;
    protected mixed $defaultValue = null;

    public function bind(string $bind): static
    {
        $this->bind = $bind;
        
        // Auto-label: Se o label não estiver definido, gera a partir do nome
        if (empty($this->label)) {
            $this->label = str($bind)->replace('_', ' ')->title()->toString();
        }

        return $this;
    }

    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function default(mixed $value): static
    {
        $this->defaultValue = $value;
        return $this;
    }

    public function getBind(): string
    {
        return $this->bind;
    }

    public function getLabel(): string
    {
        return $this->label ?? '';
    }

    public function getDefaultValue(): mixed
    {
        return $this->evaluate($this->defaultValue);
    }
}
```

---

### Passo 2: Componentes Piloto (`TextInput` & `Flex`)

Implementação de duas classes concretas para validar e estabilizar o mecanismo de renderização:

#### 1. `TextInput` (Componente Atômico)
Lida com a lógica de inputs de texto, manipula atributos de forma fluente e define o template HTML padrão.

```php
namespace Cord\Components\Forms;

use Cord\Support\Abstracts\Field;

class TextInput extends Field
{
    protected string $view = 'cord::components.forms.text-input';
    protected string $type = 'text';

    public static function build(): static
    {
        return new static();
    }

    public function email(): static
    {
        $this->type = 'email';
        return $this;
    }

    public function password(): static
    {
        $this->type = 'password';
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
```

*Template correspondente (`assets/views/components/forms/text-input.blade.php`)*:
```html
<div class="field-container">
    <label for="{{ $component->getName() }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
        {{ $component->getLabel() }}
    </label>
    <div class="mt-1">
        <input 
            type="{{ $component->getType() }}" 
            id="{{ $component->getName() }}"
            name="{{ $component->getName() }}"
            {{ $attributes->merge([
                'class' => 'block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-slate-900 dark:border-slate-700'
            ]) }}
        />
    </div>
</div>
```

#### 2. `Flex` (Componente de Layout)
Lida com posicionamento flexível contendo sub-componentes injetados dinamicamente via `schema()`.

```php
namespace Cord\Components\Layouts;

use Cord\Support\Abstracts\Component;

class Flex extends Component
{
    protected string $view = 'cord::components.layouts.flex';
    protected array $schema = [];
    protected string $direction = 'row'; // row, col

    public static function build(): static
    {
        return new static();
    }

    public function schema(array $components): static
    {
        $this->schema = $components;
        return $this;
    }

    public function direction(string $direction): static
    {
        $this->direction = $direction;
        return $this;
    }

    public function col(): static
    {
        return $this->direction('col');
    }

    public function getDirectionClass(): string
    {
        return $this->direction === 'col' ? 'flex-col' : 'flex-row';
    }

    public function getSchema(): array
    {
        return $this->schema;
    }
}
```

*Template correspondente (`assets/views/components/layouts/flex.blade.php`)*:
```html
<div class="flex {{ $component->getDirectionClass() }} gap-4">
    @foreach($component->getSchema() as $child)
        {!! $child->render() !!}
    @endforeach
</div>
```

---

### Passo 3: Motor de Validação Híbrida

Para evitar *round-trips* desnecessários (solicitações HTTP de validação em tempo real para o Livewire/Servidor), a Cord implementa um sistema de validação híbrida.

1. **Parser Back-end**: O `Field` recebe regras do Laravel e as analisa/converte em regras de validação JSON compreensíveis no front-end.
2. **Diretiva Alpine.js Local**: O input renderizado consome esse JSON e o injeta em uma diretiva Alpine.js (por exemplo, `x-validate` ou similar do blatUI), executando validações imediatas de formato, campos obrigatórios e tamanhos mínimos localmente na máquina do cliente antes do submit final do Livewire.

#### Protótipo do Motor de Validação no PHP

```php
namespace Cord\Support\Concerns;

trait HasValidation
{
    protected array $rules = [];

    public function required(): static
    {
        $this->rules[] = 'required';
        return $this;
    }

    public function minLength(int $length): static
    {
        $this->rules[] = "min:{$length}";
        return $this;
    }

    public function emailRule(): static
    {
        $this->rules[] = 'email';
        return $this;
    }

    /**
     * Traduz regras do Laravel para JSON mapeado para o front-end.
     */
    public function getClientSideValidationJson(): string
    {
        $clientRules = [];

        foreach ($this->rules as $rule) {
            if ($rule === 'required') {
                $clientRules['required'] = true;
            }
            if (str_starts_with($rule, 'min:')) {
                $clientRules['minLength'] = (int) str_replace('min:', '', $rule);
            }
            if ($rule === 'email') {
                $clientRules['email'] = true;
            }
        }

        return json_encode($clientRules);
    }
}
```

#### Integração no Componente Blade

No HTML do input, as regras são passadas diretamente para a diretiva client-side:

```html
<input 
    type="{{ $component->getType() }}" 
    id="{{ $component->getName() }}"
    x-data="{ rules: {{ $component->getClientSideValidationJson() }} }"
    x-validate="rules"
    {{ $attributes }}
/>
```

Isso garante uma experiência instantânea para o usuário final (UX Premium) mantendo as mesmas definições de regras de validação declaradas no código do servidor PHP.
