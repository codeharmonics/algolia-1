<?php

declare(strict_types=1);

namespace Devin\Algolia\Concerns;

use Devin\Algolia\Exceptions\InvalidRootSelectorException;

trait TraversesDom
{
    /**
     * The css-selector of the root element. All the children will be looped over.
     * Defaults to the sensible body tag.
     *
     * @var string
     */
    protected $rootSelector = 'body';

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
     *
     * @throws \Devin\Algolia\Exceptions\InvalidRootSelectorException
     */
    protected function traverseDom(callable $callback)
    {
        try {
            foreach ($this->getBodyChildrenElements() as $element) {
                $callback($element);
            }
        } catch (\InvalidArgumentException $exception) {
            throw new InvalidRootSelectorException($this->rootSelector, $exception);
        }
    }

    /**
     * Retrieve all the dom nodes withing the body tag of the provided document.
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function getBodyChildrenElements() : \Symfony\Component\DomCrawler\Crawler
    {
        return $this->getCrawler()->filter($this->rootSelector)->children();
    }

    /**
     * Set the selector to determine the root of the html which should be parsed.
     *
     * @param string $root
     *
     * @return \Devin\Algolia\Concerns\TraversesDom
     */
    public function setRootSelector(string $root) : self
    {
        $this->rootSelector = $root;

        return $this;
    }
}
