# ⚽ Jovem ADNP

Aplicação para organizar as peladas (futsal/society) da igreja: cadastro de atletas com aprovação,
lista de participantes por código, checagem de presença, sorteio de times, fotos de comprovação,
suspensões, inativação automática por faltas, relatório de frequência e mensagens bíblicas dinâmicas.

- **Painel do organizador** (Filament, desktop): `/organizador`
- **Página pública dos atletas** (Livewire, mobile-first): `/` e `/p/{codigo}`

---

## 🧱 Stack

| Camada | Tecnologia |
|---|---|
| Backend | Laravel 12 (PHP 8.2+) |
| Painel administrativo | Filament 3 |
| Frontend público | Livewire 3 + Blade + Tailwind CSS 3 |
| Banco de dados | MySQL 8 |
| Fotos | spatie/laravel-medialibrary 11 |
| Dev (Docker) | Laravel Sail |

---

## 🚀 Como rodar em desenvolvimento

### Opção A — Laravel Sail (Docker, recomendado para dev)

Pré-requisitos: Docker + Docker Compose.

```bash
# 1) Dependências PHP (primeira vez, usando um container Composer descartável)
docker run --rm -v "$(pwd)":/app -w /app composer:2 install --ignore-platform-reqs

# 2) Variáveis de ambiente
cp .env.sail.example .env

# 3) Sobe os containers (app + MySQL + Mailpit)
./vendor/bin/sail up -d

# 4) App key, migrations, seed e assets
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan storage:link
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

Acesse: <http://localhost/organizador> (painel) e <http://localhost/> (página pública).

### Credenciais do organizador (criadas pelo seed)

```
URL:    /organizador
E-mail: organizador@igreja.local
Senha:  mudar123
```

> **Troque a senha** no primeiro acesso (menu do usuário no canto superior direito) ou crie outro
> usuário e remova este. Para criar um organizador manualmente: `php artisan tinker` →
> `\App\Models\User::create(['name'=>'Fulano','email'=>'fulano@email.com','password'=>bcrypt('senha')]);`

---

## 🧭 Como a aplicação funciona

### Fluxo do atleta (página pública)

1. O atleta pega o **código** da pelada no grupo do WhatsApp.
2. Insere o código em `/` (ou acessa `/p/CODIGO`).
3. O sistema pede os **4 últimos dígitos do telefone** (verificação leve — só neste acesso).
4. O atleta **confirma o próprio nome** na lista e está dentro.
5. Quem ainda não tem cadastro toca em **“Não estou na lista”** e envia uma solicitação, que fica
   **pendente de aprovação** do organizador.

> A verificação por telefone é configurável por pelada/recorrência (`exige_verificacao_telefone`).
> Desligada, a página mostra o elenco aprovado para o atleta se selecionar.

### Fluxo do organizador (no dia do jogo)

- **Checagem de presença** e **sorteio** são ações rápidas, **sem verificação** — feitas no painel.
- Em *Peladas → Gerenciar*, aba **Lista / Presença**: marque quem chegou (toque individual ou em massa).
- Botão **Sortear times**: distribui os **presentes** em times. Pode refazer quantas vezes quiser.
- Botão **Encerrar pelada**: processa a frequência (faltas) e fecha a pelada.

### Sorteio

- Configurável por pelada: **Aleatório** ou **Balanceado por nível** (níveis 1–5).
- Quando há goleiro, **1 goleiro por time** é distribuído primeiro; depois os jogadores de linha.
- O número de times sai do total de presentes e dos jogadores por time; excedentes viram **reservas**.

### Status, suspensão e inativação automática

- Status do atleta: **ativo / suspenso / inativo**.
- **Suspensão** tem registro próprio (motivo, início, fim). Um comando agendado marca/desmarca o status
  “suspenso” durante a vigência. Atleta suspenso **não entra na lista** e **não acumula faltas**.
- **Inativação automática**: ao atingir **5 faltas consecutivas** o atleta vira **inativo**.
  “Ausência” conta tanto para quem se inscreveu e faltou quanto para quem nem entrou na lista.
  Presença **zera** o contador. Reativar é **manual** (e zera as faltas).

### Recorrências

- Cadastre uma recorrência (ex.: “Pelada de quinta”, 20h) e o comando `peladas:gerar-recorrentes`
  cria as próximas peladas automaticamente, já com a lista aberta.

---

## ⏰ Agendamento e fila (importante para a Hostinger)

Todo o automático é disparado pelo scheduler em `routes/console.php`:

- `atletas:sincronizar-status` — aplica/retira suspensões (diário)
- `peladas:processar-encerradas` — encerra peladas vencidas e processa faltas (de hora em hora)
- `peladas:gerar-recorrentes` — cria as próximas peladas (diário)
- `queue:work --stop-when-empty` — processa a fila (conversões de imagem) **sem worker dedicado**

Na hospedagem compartilhada, cadastre **UM único cron** (veja o deploy abaixo) e ele cuida de tudo.

---

## 🌐 Deploy na Hostinger (hospedagem compartilhada)

Sem Docker em produção. O deploy é por **Git + SSH**, com os assets buildados localmente e versionados
(o servidor compartilhado não roda `npm`).

### 1. Preparar o build localmente e versionar os assets

Na sua máquina, antes de enviar:

```bash
npm install && npm run build          # gera public/build (Vite)
git add -f public/build               # versione os assets já compilados
git commit -m "build de assets para produção"
git push
```

> Os assets do próprio Filament são publicados **no servidor** com `php artisan filament:assets`
> (passo 5), então não precisam ir no Git.

### 2. Clonar o projeto via SSH

No hPanel, ative o **acesso SSH** e o **Git**. Conecte-se e clone para uma pasta **fora** da raiz pública,
por exemplo `~/pelada`:

```bash
cd ~
git clone SEU_REPOSITORIO.git pelada
cd pelada
```

### 3. Instalar dependências (sem dev)

A Hostinger tem Composer disponível. Selecione **PHP 8.2+** no hPanel e:

```bash
composer install --no-dev --optimize-autoloader
```

### 4. Ambiente

```bash
cp .env.example .env
nano .env
```

Ajuste:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com.br
APP_KEY=                       # será gerada no próximo passo

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=usuario_pelada     # criado no hPanel → Bancos de Dados MySQL
DB_USERNAME=usuario_pelada
DB_PASSWORD=********
```

```bash
php artisan key:generate
```

### 5. Banco, storage e assets

```bash
php artisan migrate --force
php artisan db:seed --force          # opcional: cria organizador + versículos + demonstração
php artisan storage:link
php artisan filament:assets          # publica os assets do painel
php artisan vendor:publish --tag=medialibrary-config   # opcional
```

### 6. Apontar o domínio para a pasta `public`

No hPanel, em **Sites → Avançado → Document Root** (ou *Gerenciador de Arquivos*), aponte o
domínio/subdomínio para `~/pelada/public`.

Se o painel só permitir servir a partir de `public_html`, a alternativa é criar um **symlink**:

```bash
rm -rf ~/public_html
ln -s ~/pelada/public ~/public_html
```

### 7. Otimizações de produção

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache       # cache dos ícones do Filament
```

### 8. SSL

Ative o **SSL gratuito (Let’s Encrypt)** no hPanel. O `AppServiceProvider` já força HTTPS quando
`APP_ENV=production`.

### 9. Cron único (scheduler + fila)

Em **hPanel → Avançado → Cron Jobs**, adicione **um** job a cada minuto:

```cron
* * * * * cd ~/pelada && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

Esse único cron dispara as tarefas agendadas **e** mantém a fila andando (conversões de imagem etc.).

### 10. Atualizações futuras

```bash
cd ~/pelada
php artisan down
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan icons:cache
php artisan up
```

---

## 🗃️ Sobre os nomes das tabelas

As tabelas de **domínio** estão em **português**: `locais`, `atletas`, `recorrencias`, `peladas`,
`inscricoes`, `times`, `time_atleta`, `suspensoes`, `versiculos`. Mantêm as colunas padrão do Laravel
(`id`, `created_at`, `updated_at`, `deleted_at`).

As tabelas de **framework/pacotes** ficam com os nomes padrão (em inglês) por estabilidade e
compatibilidade com o ecossistema: `users`, `sessions`, `password_reset_tokens`, `cache`, `cache_locks`,
`jobs`, `job_batches`, `failed_jobs` e `media` (spatie/laravel-medialibrary). Renomeá-las traria risco
desnecessário (autenticação, Filament e o pacote de mídia esperam esses nomes).

---

## 📁 Estrutura principal

```
app/
  Enums/                     StatusAtleta, SituacaoCadastro, Modalidade, MetodoSorteio, StatusPelada, FrequenciaRecorrencia
  Models/                    Atleta, Pelada, Inscricao, Time, Recorrencia, Suspensao, Local, Versiculo, User
  Services/
    SorteioService.php       sorteio aleatório/balanceado, goleiros, reservas
    FrequenciaService.php    faltas consecutivas, inativação, relatório
  Console/Commands/          sincronizar-status, processar-encerradas, gerar-recorrentes
  Livewire/AcessoPelada.php  fluxo público (código → telefone → confirmar nome)
  Filament/
    Resources/               Atleta, Pelada, Recorrencia, Local, Suspensao, Versiculo
    Pages/                   RelatorioFrequencia
    Resources/PeladaResource/RelationManagers/  Inscricoes (presença), Times (sorteio)
database/
  migrations/                framework + domínio (pt-BR)
  seeders/                   Database, Versiculo, Demo
resources/views/             layout público + livewire/acesso-pelada + filament/pages
routes/                      web.php (público) + console.php (scheduler)
```

---

## ✅ Checklist pós-instalação

- [ ] Acessei `/organizador` e troquei a senha do organizador.
- [ ] Criei um **Local** e uma **Recorrência** (ou uma pelada avulsa).
- [ ] Aprovei os atletas pendentes na aba **Pendentes**.
- [ ] Abri a lista da pelada e testei a entrada pela página pública com o código.
- [ ] Marquei presença e fiz um **sorteio** de teste.
- [ ] Cadastrei o **cron** único na Hostinger.

Bom jogo! 🙏⚽
