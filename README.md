Gerar sitemap dinamicamente em PHP
======

Gerar dinamicamente o sitemap do seu Blog ou site e enviar para o Google Webmaster automaticamente. Sem esforço ;)

Como usar
------------

Basicamente o arquivo sitemap.php que se encontra nesse repositório já esta funcional, bastando realizar as devidas alterações como informações do banco de dados como usuário e senha.

1. Salve o arquivo em algum diretorio do seu site. Por exemplo, pode-se criar um diretorio chamado scripts, cron ou o nome que desejar.
2. Altere as informações de usuário e senha do banco de dados.
3. Na linha responsável em buscar na tabela `posts` os posts/artigos deve ser alterada conforme sua tabela. Veja que existe uma validação onde só irá retornar posts que estejam com o status igual a publicado: `status = 'PUBLISHED'`

```

`$sql = "SELECT * FROM posts WHERE status = 'PUBLISHED'";`

```

4. Na linha `$datetime = new DateTime($v['updated_at']);` que fica dentro do `foreach` deve ser alterado com o nome da coluna de sua tabela que representa a data que o post foi criado ou modificado.
5. Na linha `$arquivo = fopen('sitemap.xml', 'w');` deve ser informado o caminho relativo de onde o arquivo sitemap será criado.


Envio automático para Google Webmaster
------------

Esse trecho de código é responsável em enviar os dois arquivos criados `sitemap.xml` e `sitemap.xml.gz` para o Google. Veja que a variável `HOME` foi declarada nas primeiras linhas do arquivo, que define a URL do seu site.

```

// Envia para o Google o novo sitemap gerado
$urlSitemap = "http://www.google.com/webmasters/sitemaps/ping?sitemap=" . HOME . "/";
// Arquivos a serem enviados
$Files = ['sitemap.xml', 'sitemap.xml.gz'];
// Envia os dois arquivos sitemap gerados para a URL do Google
foreach ($Files as $file) {
    $url = $urlSitemap . $file;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
}

```


Automatizando
------------

Agora que tudo está funcionando como deveria, vamos fazer mais um passo para deixar que o "sistema" faça tudo pela gente.

Se você usa Linux e tem acesso ao shell poderá criar um registro no crontab para realizar a tarefa em um determinado período.

Exemplo de registro para o crontab onde o script será executado toda segunda-feira às 00:30.

`30 0 * * 1 /var/www/html/seu_site/cron/gerasitemap.php`

Contribua
------------

Contribua para que esse script melhore ainda mais
