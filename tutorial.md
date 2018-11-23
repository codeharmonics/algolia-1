# Parsing HTML with PHP

## Introduction
The aim of this tutorial is to walk you through all the steps necessary to create records you can use with [Algolia](https://www.algolia.com/) search.
We will generate these records by parsing multiple HTML pages containing documentation about Algolia. 
The pages that will be parsed, can be found in the `docs` folder in the root of this repository.

### Algolia
Algolia provides a wide array of services for developers and the platforms they work on. 
These services range from an analytics service, to search as a service.
The indicises you will create in this tutorial, can be used with their _search as a service_.

Algolia makes creating a search a breeze for developers, saving them hours of work and optimalization.
This enables developers to focus their effort on providing the most value to the end user of the platform they are working on.
Algolia will take care of the search, providing a great user experience to the end user with a lightning fast and user-friendly search through the content provided by the developers. 
This content is provided by sending so called _records_ to the Algolia service.

#### records
Algolia makes use of records to provide their _search as a service_.
A record is a schemaless object sent to Algolia by the developer.
Algolia will use the records sent, and create an index. This index, basically, is a searchable collection of records.

Because a record is schemaless, it is extremely easy for developers to generate them and send them to Algolia.
To provide the most value to the end user though, it is important to think about how your users will use the search on your website, and what content the user will be looking for.

## Goals of this tutorial
In this tutorial, we will walk you through all the steps you need to take to parse HTML documentation pages and build records for their contents.
These records will be formatted according to the [hierarchical](https://blog.algolia.com/how-to-build-a-helpful-search-for-technical-documentation-the-laravel-example/#1-create-small-hierarchical-records) structure [this](https://blog.algolia.com/how-to-build-a-helpful-search-for-technical-documentation-the-laravel-example) article proposes.

An example record structure for a paragraph of documentation:
```json
{
    "h1": "Validation", 
    "h2": "Introduction", 
    "link": "validation#introduction", 
    "importance": 1, 
    "_tags": [
        "5.1"
    ], 
    "objectID": "master-validation#introduction-eeafb566c2af34e739e2685efdb45524"
}
```

## Requirements
This tutorial requires you have a basic understanding of PHP, 
and that you know how to use the [Composer](https://getcomposer.org/doc/00-intro.md) dependency manager to autoload the classes you will create.
If you need to learn more about this, [here](https://vegibit.com/composer-autoloading-tutorial/) is an excellent tutorial on how to get started.

### System requirements
Your system must have the following software installed:
* [PHP 7.0 or higher](http://php.net/manual/en/install.php)
* [Composer](https://getcomposer.org/doc/00-intro.md)

### Project
If you're not sure how to set up your project, we've created a [repository](https://github.com/devinbeeuwkes/boilerplate) you can clone to use as a starting point.
It covers the basics for you, like auto-loading your classes and setting up a basic testsuite.

## Building an HTML parser
Before you start coding, let's have a look at the steps that our code will need to go through in order to parse the HTML pages found in the `docs` folder of this repository.

You will need to:

```
* Load the HTML files
* Loop over all elements
* Determine which elements are relevant, and which are not
* Build the structure for our relevant elements
* Return and use our results
```

Let's get started!

### Loading HTML files
In order for PHP to be able to read the HTML files, you need to fetch the content of it.
Luckily, PHP has a built-in `file_get_contents` function which makes this easy for you to do.
You just need to provide the path to the file you want to get the contents of: 

```php
<?php

// Load the file contents
$content = \file_get_contents(__DIR__ . '/docs/1-algolia-and-scout.html');
```

Since you'll create different objects for all four of the pages found in the `docs` folder,
it's best to load them all separately, instead of concatenating all the files into one big variable.
For demonstration purposes, this tutorial will only cover the parsing of the first page.
You have the freedom to decide how you want to handle the other pages.

### Loop over all elements
Now that you have the HTML file contents in a variable, we need to loop over the elements in them.
However, PHP does not know the contents of the file is HTML, it just provides you a string. 
This is where the `symfony/dom-crawler` package comes in; it makes looping over HTML elements a breeze.

To include `symfony/dom-crawler` in your project, run the following command in your terminal:
```bash
$ composer require symfony/dom-crawler "^3.4"
```

You can now instantiate a new `Crawler` class instance, which will help you traverse the dom.
The `Crawler` class needs to know what HTML to traverse, so pass the file contents you loaded before to its constructor.


```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load the file contents
$content = \file_get_contents(__DIR__ . '/docs/1-algolia-and-scout.html');

$crawler = new \Symfony\Component\DomCrawler\Crawler($content);

```

The crawler class is iterable, meaning you can use it in a foreach-loop and traverse through its contents.
However, when you try this, you will see it provides you with just one object, the `html` element and all the content in it.
Since we want to parse only the contents of the `body` tag, you need to filter the `Crawler` object.
You can achieve this by using the `filter` method, passing a CSS selector as its parameter.

> The CSS selector for the `body` tag is 'body'

Because you want all the child elements of the `body` element, you have to chain the `filter` method with the `children` method, like this:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load the file contents
$content = \file_get_contents(__DIR__ . '/docs/1-algolia-and-scout.html');

$crawler = new \Symfony\Component\DomCrawler\Crawler($content);
$bodyElements = $crawler->filter('body')->children();
```

This code will give you an error, because the `Crawler` object requires another `symfony` package to use the CSS selector.
To include this package, run the following command in your terminal:

```shell
$ composer require symfony/css-selector "^3.4"
```

If you now loop over the `$bodyContents` variable, you will see it will give you all the elements that you need to start creating your records.

```php
<?php

foreach($bodyElements as $element) {
    // Your logic goes here
}
```

### Classifying elements of interest
The HTML pages that you will parse only contains elements which are interesting to index, since there are no elements with the purpose of styling, or any menus for example.
The only elements we are interested in, are `h1`, `h2`, `h3`, `h4` and `p` tags, all other tags can be skipped.

### Building your records
Now that you can loop over all available elements, it's time to build records for these elements.
To do this, use the following logic:

```
* h1 elements have their own record
* h2 elements have their own record, with the parent H1 record if it has such a parent
* h3 elements have their own record, with the parent H1 and H2 records if it has such parents
* h4 elements have their own record, with the parent H1, H2 and H3 records if it has such parents
* p elements have their own record, with the parent H1, H2, H3 and H4 records if it has such parents
```


For performance reasons, you only want to loop through the elements once. 
This makes it important to keep an array of all the parent _relevant_ elements that were being looped over.

Sometimes you will need to reset this parents array, for example when you encouter a new `h2` element after you indexed an `h3` element.
To keep track of when you need to reset, you can use a `priorityOrder`, where you list all the elements of interest from most interesting (`h1`) to least interesting (`p`).
If an element is not in the `priorityOrder` array, you should skip the indexing of this element, since this element is not of our interest.

When you index a new element, you check if any of the elements _after_ that tagname in the array occur in your parents array.
If this situation occurs, you have to remove these elements from the parents array.

```php
<?php

$parents = [];
$priorityOrder = ['h1', 'h2', 'h3', 'h4', 'p'];
foreach($bodyContents as $element) {
    // Remove unnecessary parents
    $prioIndex = \array_search($element->tagName, $priorityOrder);
    if ($prioIndex === false) {
        continue;
    }
    
    for ($i = $prioIndex; $i < count($priorityOrder); $i++) {
        unset($parents[$priorityOrder[$i]]);
    }
    
    // Add the current item to the parent array
    $parents[$element->tagName] = $element;
    
}
```

Now, everytime you encouter a new `h2` element, the previous one _and_ all of it's children will be unset for the parents array.
The above code will result in a parents array in the following format:
```php
<?php
    $parents = [
        'h1' => 'Algolia and Laravel Scout',
        'h2' => 'Introducing Laravel Scout',
        // h3 is missing, because it is not in the current page 
        // h4 is missing, because it is not in the current page 
        'p'  => 'With the addition of ...'
    ];
```

### Keeping scores
To provide an importance score of how relevant an entry actually is, please consider the following:
```
* H1 gets a score of 0
* H2 gets a score of 1
* H3 gets a score of 2
* H4 gets a score of 3
* P directly after H1 gets a score of 4
* P directly after H2 gets a score of 5
* P directly after H3 gets a score of 6
* P directly after H4 gets a score of 7
```


In order to calculate the score above, you need to check which element you're dealing with.
Calculating the score for any of the `h*` elements is easy, because they do not depend on one of their parents.
This is different for the `p` elements, however, because its score depends on its closest parent.

Since your `parents` array now contains all the elements of interest, you can easily get the closest parent to any `p` element, and use this parent to calculate the score.
As you can deduct from the score matrix above, the score of a `p` element is the score of its closest parent + 4.

This means your code to calculate the score can look something like this:

```php
<?php

    $scores = [
        'h1' => 0,
        'h2' => 1,
        'h3' => 2,
        'h4' => 3,
    ];
    
    foreach($bodyContents as $element) {
        // Your logic goes here
    
        if (array_key_exists($element->tagName, $scores)) {
            $score = $scores[$element->tagName];
        } else {
            // Assume we have a 'p' tag
            $score = 0;
            foreach($parents as $tag => $parent) {
                if (array_key_exists($tag, $scores)) {
                    // We overwrite score if there is a 'lower' parent
                    // E.g: if a p element has h2 and h1 parents, we use the h2 parent since this is the closest one
                    $score = $scores[$tag];
                }
            }
            $score += 4;
        }
        
    }

```

### Finalizing the attribute list
The nice thing about keeping your parent elements in an array, is that the result of this array is almost the complete record you need to build!
All that is missing now to get the results as described in the beginning of this tutorial, is to map the `parents` array to get only the parent content instead of the whole object provided by the `Crawler` class,
and combine this with the `score`, `link` and `_tags` attributes!

The `_tags` attribute will be used to build up the url. It contains all elements that should be appended before the filename.
In your case, the documentation files you're parsing are in the `docs` folder, so the `_tags` array would contain only one entry: `docs`!
```php
<?php

$tags = ['docs'];
```


The `link` attribute will be the pagename, appended with an `#element_or_closest_parent` anchor if needed.
Since the base of it is the pagename, we can retrieve it by using the `basename` method in PHP, providing the path to the file you are parsing as an attribute.

```php
<?php

    $link = basename(__DIR__ . '/docs/1-algolia-and-scout.html');
```
  
To determine if you should attach an anchor to the link, you need to check if the current element has an anchor (`a`) tag in it's content **with** an `id` property.
You can accomplish this by looping over the children of the element you are parsing.
However, since we want to start with the lowest parent and work our way up, you need to reverse the array.
You can accomplish this by using the `array_reverse` method.

Your code to generate the anchor would look something like this:
```php
<?php

    foreach($bodyContents as $element) {
        $anchor = '';
        
        foreach(array_reverse($parents) as $parent) {
            foreach ($parent->getElementsByTagName('a') as $child) {
                if ($child->getAttribute('id')) {
                    $anchor = '#' . $child->getAttribute('id');
                }   
            }
            
            // We escape the loop as soon as we have the closest anchor
            if (! empty($anchor)) {
                break;
            }
        }
    }

```

At last, you append the anchor to the `link` variable:
```php
<?php
    
        foreach($bodyContents as $element) {
            // Create the link and body variable for this element
            
            $link .= $anchor;
        }
```

### Putting it al together
With all the data available that you need, all that is left is to put it together.
To do this, loop over the parents and get the `textContent` for every element, and place it in an array.
Merge this array with an array containing the `score`, `link` and `_tags` attributes.

```php
<?php

    $records = [];
    foreach($bodyContents as $element) {
            
        $data = [];
        foreach ($parents as $parent => $element) {
            $data[$parent] = $element->textContent;
        }
        
        $records[] = array_merge($data, [
            '_tags'      => $tags,
            'link'       => $link,
            'importance' => $score,
        ]);
    }
    
    return $records;
    
```

## Done!
Congratulations, you've finished building the structure for your records!
The final code will look something like this:
```php
$content = \file_get_contents(__DIR__ . '/docs/1-algolia-and-scout.html');

    $crawler = new \Symfony\Component\DomCrawler\Crawler($content);
    $bodyElements = $crawler->filter('body')->children();
    
    $priorityOrder = ['h1', 'h2', 'h3', 'h4', 'p'];
    $scores = ['h1' => 0, 'h2' => 1, 'h3' => 2, 'h4' => 3];
    $records = [];
    $parents = [];

    foreach($bodyElements as $element) {
        // Remove unnecessary parents
        $prioIndex = \array_search($element->tagName, $priorityOrder, true);
        if ($prioIndex === false) {
            continue;
        }

        for ($i = $prioIndex; $i < count($priorityOrder); $i++) {
            unset($parents[$priorityOrder[$i]]);
        }

        // Add the current item to the parent array
        $parents[$element->tagName] = $element;

        if (array_key_exists($element->tagName, $scores)) {
            $score = $scores[$element->tagName];
        } else {
            // Assume we have a 'p' tag
            $score = 0;
            foreach($parents as $tag => $parent) {
                if (array_key_exists($tag, $scores)) {
                    // We overwrite score if there is a 'lower' parent
                    // E.g: if a p element has h2 and h1 parents, we use the h2 parent since this is the closest one
                    $score = $scores[$tag];
                }
            }
            
            $score += 4;
        }

        $tags = ['docs'];
        $link = basename(__DIR__ . '/docs/1-algolia-and-scout.html');

        $anchor = '';
        foreach(array_reverse($parents) as $parent) {
            foreach ($parent->getElementsByTagName('a') as $child) {
                if ($child->getAttribute('id')) {
                    $anchor = '#' . $child->getAttribute('id');
                }
            }
            // We escape the loop as soon as we have the closest anchor
            if (! empty($anchor)) {
                break;
            }
        }
        $link .= $anchor;

        $data = [];
        foreach ($parents as $parent => $element) {
            $data[$parent] = $element->textContent;
        }

        $records[] = array_merge($data, [
            '_tags'      => $tags,
            'link'       => $link,
            'importance' => $score,
        ]);
    }

    return $records;
```




## Further steps
Creating an HTML parser is a good start for your journey. 
However, the code we wrote is not very flexible or reusable. 
Here are some ideas to put your skills to the test, and possibly further your knowledge in HTML parsing:
```
* Extract all the logic of this parser into classes (see the src folder for inspiration on this)
* Create a custom search using Algolia
* Write unit tests to test the functionallity and make sure refactoring your code doesn't break how it works
* Adjust your parser so that it can read any page, instead of the very basic one used for this tutorial
```

___
Cheers!