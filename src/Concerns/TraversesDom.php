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

    /**
     * Traverse the dom and execute the provided callback on all its members.
     *
     * @param callable $callback
     */
    protected function traverseDom(callable $callback)
    {
        foreach ($this->getBodyChildrenElements() as $element) {
            $callback($element);
        }
    }

    /**
     * Retrieve all the dom nodes withing the body tag of the provided document.
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function getBodyChildrenElements() : \Symfony\Component\DomCrawler\Crawler
    {
        return $this->getCrawler()->filter('body')->children();
    }
}
