# Botman WhatsApp Driver
Este é um Driver do Botman para se comunicar com o WhatsApp. O Driver permite criar chatbots para o WhatsApp, enviando e recebendo mensagens, gerenciando conversas e mantendo o estado da conversa com o usuário.

## Requisitos
Antes de usar este Driver, você deve ter:

Uma conta comercial no WhatsApp Business API
- Credenciais para acessar a API do WhatsApp
- PHP 8.0 ou superior
- Botman 2.6 

## Instalação
Para instalar este Driver, basta executar o seguinte comando no terminal:

```bash
composer require booster-api/driver-whatsapp
```

## Configuração
Para usar este Driver, você precisa adicionar as seguintes configurações ao seu arquivo `config/botman/whatsapp-web.php`:

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Whatsapp Web Token
    |--------------------------------------------------------------------------
    |
    | Your Whatsapp Web bot token you received after creating
    | the chatbot through Whatsapp Web.
    |
    */
    'secret' => env('WHATSAPP_WEB_SECRET'),
    'url' => env('WHATSAPP_WEB_URL'),
    'api_key' => env('WHATSAPP_WEB_API_KEY'),
];

```
- `WHATSAPP_WEB_SECRET`: é a chave secreta usada para autenticar as solicitações enviadas para o seu webhook. Esta chave deve ser definida no console de desenvolvimento do WhatsApp.

- `WHATSAPP_WEB_URL`: é a URL do webhook que recebe as solicitações do WhatsApp. Esta URL também deve ser definida no console de desenvolvimento do WhatsApp.

- `WHATSAPP_WEB_API_KEY`: é a chave de API usada para autenticar as solicitações enviadas para o seu webhook. Esta chave deve ser gerada por você e pode ser usada para verificar se a solicitação veio do WhatsApp Web API.

Aqui está um exemplo de como definir essas variáveis de configuração em um arquivo `.env`:

```dotenv
WHATSAPP_WEB_SECRET=your_webhook_secret
WHATSAPP_WEB_URL=https://api.booster-api.com
WHATSAPP_WEB_API_KEY=your_api_key
```

## Uso
Para usar este Driver, basta criar uma nova instância do Botman e definir as rotas de conversação da seguinte maneira:

```php
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;

$config = [
    'secret' => 'WHATSAPP_WEB_SECRET',
    'url' => 'WHATSAPP_WEB_URL',
    'api_key' => 'WHATSAPP_WEB_API_KEY',
];

DriverManager::loadDriver(BotMan\Drivers\WhatsappWeb\WhatsappWebDriver::class);

$botman = BotManFactory::create($config);

$botman->hears('oi', function (BotMan $bot) {
    $bot->reply('Olá! Como posso ajudar?');
});

$botman->listen();
```
Este exemplo define uma rota que responde à mensagem 'oi' com uma mensagem de saudação.

Para mais informações sobre como usar o Botman, consulte a documentação oficial: [https://botman.io/2.0/getting-started](https://botman.io/2.0/getting-started)

## Features
lista de tipos de mensagens que podem ser enviadas usando o Driver do Botman para o WhatsApp:

- [X] Texto: mensagens de texto simples
- [X] Imagens: mensagens com imagens (JPEG ou PNG)
- [X] Arquivos de áudio: mensagens de áudio (MP3 ou AAC)
- [X] Vídeos: mensagens com vídeos (MP4)
- [ ] Localização: mensagens com a localização do remetente
- [ ] Contatos: mensagens com informações de contato (nome e número de telefone)
- [X] Documentos: mensagens com documentos anexados (PDF, DOCX, XLSX)
- [ ] Mensagens de sistema: mensagens que o WhatsApp envia, como notificações de entrega, leitura e outras informações de status.

## Contribuição
Se você quiser contribuir com este Driver, sinta-se à vontade para enviar um pull request ou abrir uma nova issue. Sua ajuda é muito bem-vinda!
