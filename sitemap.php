<?php

// Dados de conexao com o banco
define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'user_db');
define('MYSQL_PASSWORD', 'pass_db');
define('MYSQL_DB_NAME', 'name_db');
define('HOME', 'http://example.com');

try
{
	// Realiza a conexao com o banco - PDO
    $PDO = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DB_NAME, MYSQL_USER, MYSQL_PASSWORD);
    $PDO->exec("set names utf8");

    // Realiza a consulta no banco de todas as postagens ativas
    $sql = "SELECT * FROM posts WHERE status = 'PUBLISHED'";
	$result = $PDO->query($sql);
	$rows = $result->fetchAll(PDO::FETCH_ASSOC);

	// Data e hora atual
	$datetime = new DateTime(date('Y-m-d H:i:s'));
	// A linha abaixo me retornará uma data no seguinte formato: 2017-11-22T00:06:23-02:00
	$date = $datetime->format(DateTime::ATOM); // ISO8601	

	// Gera o arquivo XML do sitemap
	$xml = '<?xml version="1.0" encoding="UTF-8"?>
	<urlset
	    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
	    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
	        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

	    <url>
	        <loc>'.HOME.'</loc>
	        <lastmod>'.$date.'</lastmod>
	        <changefreq>weekly</changefreq>
	        <priority>1.00</priority>
	    </url>';

		foreach($rows as $v){
		    $datetime = new DateTime($v['updated_at']);
		    $date = $datetime->format(DateTime::ATOM);
		    $xml .='
			    <url>
			        <loc>'.HOME.'/'.$v['slug'].'</loc>
			        <lastmod>'.$date.'</lastmod>
			        <changefreq>weekly</changefreq>
			        <priority>0.85</priority>
			    </url>    ';
		}

	$xml .= '
	</urlset>';

	// Abre o arquivo ou tenta cria-lo se ele não exixtir
	$arquivo = fopen('sitemap.xml', 'w');
	if (fwrite($arquivo, $xml)) {
	    echo "Arquivo sitemap.xml criado com sucesso";
	} else {
		echo "Não foi possível criar o arquivo. Verifique as permissões do diretório.";
	}
	fclose($arquivo);

	// Compactar arquivo sitemap para GZIP
	$data = implode("", file("sitemap.xml"));
	$gzdata = gzencode($data, 9);
	$fp = fopen("sitemap.xml.gz", "w");
	fwrite($fp, $gzdata);
	fclose($fp);
	 
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

}
catch (PDOException $e)
{
    echo 'Erro ao conectar com o banco: ' . $e->getMessage();
}
