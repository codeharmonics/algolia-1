<?php

declare(strict_types=1);

namespace Devin\Algolia;

use Devin\Algolia\Concerns\TraversesDom;
use Devin\Algolia\Contracts\ConvertsItemToAngoliaIndex;
use Devin\Algolia\Exceptions\InvalidPathException;
use Devin\Algolia\Exceptions\UnindexableException;
use Symfony\Component\DomCrawler\Crawler;

class Parser
{
    use TraversesDom;

    /**
     * Determine whether the indices have been created.
     *
     * @var bool
     */
    protected $indicesCreated = false;

    /**
     * The index objects that are suitable for use in Algolia.
     *
     * @var array
     */
    protected $indices = [];

    /**
     * The stored parent objects to add when creating the searchable objects.
     *
     * @var array
     */
    protected $parents = [];

    /**
     * The absolute path to the file to parse.
     *
     * @var string
     */
    protected $path;

    /**
     * A mapping of the heading-tags and their respective scores.
     *
     * @var array
     */
    protected $scores = [
        'h1' => 0,
        'h2' => 1,
        'h3' => 2,
        'h4' => 3,
    ];

    /**
     * A list of all the tags of which the content should be indexed.
     *
     * @var array
     */
    protected $indexableTags = [
        'h1', 'h2', 'h3', 'h4', 'p',
    ];

    /**
     * Create a new instance.
     *
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     */
    protected function __construct(Crawler $crawler)
    {
        $this->setCrawler($crawler);
    }

    /**
     * Create a new parser instance for the provided path.
     *
     * @param string $path
     * @param string $baseUri
     *
     * @return \Devin\Algolia\Parser
     * @throws \Devin\Algolia\Exceptions\InvalidPathException
     */
    public static function forFile(string $path, string $baseUri = '') : self
    {
        if (! \file_exists($path)) {
            throw new InvalidPathException($path);
        }

        return new self(
            new Crawler(file_get_contents($path), $baseUri)
        );
    }

    /**
     * Parse the DOM contents to indexable objects, ready for use with Algolia Search.
     *
     * @return \Devin\Algolia\Parser
     * @throws \Devin\Algolia\Exceptions\InvalidRootSelectorException
     */
    public function createIndices() : self
    {
        $this->traverseDom(function (\DOMElement $element) {
            try {
                $this->addIndexableObject(
                    $this->extractIndexableObject($element)
                );
            } catch (UnindexableException $exception) {
                // Don't index this element.
            }
        });

        $this->indicesCreated = true;

        return $this;
    }

    /**
     * Convert the provided element to an indexable object.
     *
     * @param \DOMElement $element
     *
     * @return array
     * @throws \Devin\Algolia\Exceptions\UnindexableException
     */
    protected function extractIndexableObject(\DOMElement $element) : array
    {
        if (! $this->shouldBeIndexed($element)) {
            throw new UnindexableException(sprintf('Element %s is not indexable', $element->tagName));
        }

        $this->addToParentsArrayIfNeeded($element);

        $elementName = $this->getElementAttributeName($element);

        return array_merge($this->formatParentsArray(), [
            $elementName    => $element->textContent,
            'link'          => $this->generateLinkForElement($element, $this->parents, ''),
            'page'          => basename($this->getCrawler()->getUri()),
            'importance'    => $this->calculateImportanceScore($element),
            '_tags'         => $this->getTags()
        ]);
    }

    /**
     * Add the element to the parents array when this is needed.
     *
     * @param \DOMElement $element
     *
     * @return $this
     */
    protected function addToParentsArrayIfNeeded(\DOMElement $element) : self
    {
        if (! $this->isHeading($element)) {
            return $this;
        }

        $this->parents[$element->tagName] = $element;
        $this->rebaseParentsIfNeeded($element);

        return $this;
    }

    /**
     * Remove lower parents from the parents array. For example: when the parents array contains h1, h2 and h3 keys,
     * and a new h1 key should be added, we should remove the h2 and h3 keys
     *
     * @param \DOMElement $element
     *
     * @return \Devin\Algolia\Parser
     */
    protected function rebaseParentsIfNeeded(\DOMElement $element) : self
    {
        $priority = ['h1', 'h2', 'h3', 'h4'];

        if (false !== $key = \array_search($element->tagName, $priority, true)) {
            $lastIndex = \count($priority);
            for ($i = $key + 1; $i < $lastIndex; $i++) {
                unset($this->parents[$priority[$i]]);
            }
        }

        return $this;
    }

    /**
     * Determine if the provided element should be indexed or not. The 'indexableTags' property
     * tell us which tags to allow.
     *
     * @param \DOMElement $element
     *
     * @return bool
     */
    protected function shouldBeIndexed(\DOMElement $element) : bool
    {
        return \in_array($element->tagName, $this->indexableTags, true);
    }

    /**
     * Get the attribute name that should be used in the index object for the provided element.
     * Only p-tags have a different attribute name, all other tags remain the same.
     *
     * @param \DOMElement $element
     *
     * @return string
     */
    protected function getElementAttributeName(\DOMElement $element): string
    {
        return ($element->nodeName === 'p') ? 'content' : $element->nodeName;
    }

    /**
     * Format the parents array to a format usable for index objects.
     *
     * @return array
     */
    protected function formatParentsArray() : array
    {
        $result = [];

        foreach ($this->parents as $parent) {
            $result[$parent->tagName] = $parent->textContent;
        }

        return $result;
    }

    /**
     * Generate the link belonging to this element. Try to find the closest parent element id for texts,
     * and append this to the url.
     *
     * @param \DOMElement $element
     * @param array       $parents
     * @param string      $append
     *
     * @return string
     */
    protected function generateLinkForElement(\DOMElement $element, array $parents, string $append = '') : string
    {
        if ($this->isSubHeading($element) && $anchorTag = $this->getNestedAnchorTag($element)) {
            $append = '#' . $anchorTag->getAttribute('id');
        }

        if (empty($parents) || $this->isMainHeading($element)) {
            return basename($this->getCrawler()->getUri()) . $append;
        }

        return $this->generateLinkForElement(array_pop($parents), $parents, $append);
    }

    /**
     * Calculate the importance score of the provided element. The distribution is as followed:
     * h1 - 0       text after h1 - 4
     * h2 - 1       text after h2 - 5
     * h3 - 2       text after h3 - 6
     * h4 - 3       text after h4 - 7
     *
     * @param \DOMElement $element
     *
     * @return int
     */
    protected function calculateImportanceScore(\DOMElement $element) : int
    {
        if ($this->isHeading($element)) {
            return $this->scores[$element->tagName] ?: 0;
        }

        $parent = end($this->parents);
        reset($this->parents);

        return $parent ? 4 + $this->calculateImportanceScore($parent) : 1;
    }

    /**
     * Try to get an anchor element that is a child of the provided element. If not available,
     * return a sensible default.
     *
     * @param \DOMElement $element
     * @param null|mixed  $default
     *
     * @return \DOMElement|null|mixed
     */
    protected function getNestedAnchorTag(\DOMElement $element, $default = null)
    {
        foreach ($element->childNodes as $childNode) {
            if ($childNode->tagName === 'a') {
                return $childNode;
            }
        }

        return $default;
    }

    /**
     * Get the tags variable used to build up the url.
     *
     * @return array
     */
    protected function getTags() : array
    {
        return [
            str_replace(
                basename($this->getCrawler()->getUri()),
                '',
                $this->getCrawler()->getUri()
            ),
        ];
    }

    /**
     * Determine if the provided element is a heading tag.
     *
     * @param \DOMElement $element
     *
     * @return bool
     */
    protected function isHeading(\DOMElement $element) : bool
    {
        return \in_array($element->tagName, [
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
        ], true);
    }

    /**
     * Determine if the provided element is a heading-tag, but not an h1 tag.
     *
     * @param \DOMElement $element
     *
     * @return bool
     */
    protected function isSubHeading(\DOMElement $element) : bool
    {
        return \in_array($element->tagName, [
            'h2', 'h3', 'h4', 'h5', 'h6'
        ], true);
    }

    /**
     * Determine if the provided element is a h1-tag
     *
     * @param \DOMElement $element
     *
     * @return bool
     */
    protected function isMainHeading(\DOMElement $element) : bool
    {
        return $element->tagName === 'h1';
    }

    /**
     * Add an object to the array of extracted index objects.
     *
     * @param array $object
     */
    protected function addIndexableObject(array $object)
    {
        $this->indices[] = $object;
    }

    /**
     * Get an array containing all the indexable objects extracted from the HTML provided to this class.
     *
     * @return array
     */
    public function getIndices() : array
    {
        if (! $this->indicesCreated) {
            $this->createIndices();
        }

        return $this->indices;
    }
}
