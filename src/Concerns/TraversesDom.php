<?php

namespace Devin\Algolia\Concerns;

trait TraversesDom
{
    /**
     * The symfony crawler used to traverse the dom.
     *
     * @var \Symfony\Component\DomCrawler\Crawler
     */
    protected $crawler;

    /**
     * Set the crawler to traverse the dom with.
     *
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     *
     * @return \Devin\Algolia\Concerns\TraversesDom
     */
    public function setCrawler(\Symfony\Component\DomCrawler\Crawler $crawler) : self
    {
        $this->crawler = $crawler;

        return $this;
    }

    /**
     * Get the crawler used to traverse the dom.
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function getCrawler(): \Symfony\Component\DomCrawler\Crawler
    {
        return $this->crawler;
    }
}
