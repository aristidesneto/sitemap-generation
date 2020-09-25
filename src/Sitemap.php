<?php
declare(strict_types=1);

namespace Aristides\Sitemap;

class Sitemap
{
//    const ALWAYS = 'always';
//    const HOURLY = 'hourly';
//    const DAILY = 'daily';
//    const WEEKLY = 'weekly';
//    const MONTHLY = 'monthly';
//    const YEARLY = 'yearly';
//    const NEVER = 'never';

    private $writer;

    private $url;
    private $lastmod;
    private $changefreq;
    private $priority;

    public function __construct()
    {
        $this->writer = new \XMLWriter();
    }

    public function add($url, $lastmod = null, $changefreq = null, $priority = null)
    {
        $this->url = $url;
        $this->lastmod = $lastmod ?? time();
        $this->changefreq = $changefreq ?? 'daily';
        $this->priority = $priority ?? 0.9;
    }

    private function startDocument()
    {
        $this->writer->openURI('sitemap.xml');
        $this->writer->startDocument('1.0', 'UTF-8');
        $this->writer->setIndent(true);

        $this->writer->startElement("urlset");
        $this->writer->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    }

    private function endDocument()
    {
        $this->writer->endDocument();
        $this->writer->flush();
    }

    public function write()
    {
        $this->startDocument();

        $this->writer->startElement("url");
        $this->writer->writeElement('loc', $this->url);
        $this->writer->writeElement('lastmod', date('c', $this->lastmod));
        $this->writer->writeElement('changefreq', $this->changefreq);
        $this->writer->writeElement('priority', number_format($this->priority, 1, '.', ','));
        $this->writer->endElement();

        $this->endDocument();
    }


}