# Parsing HTML pages with PHP 

## Introduction
@TODO: Algolia link
The aim of this tutorial is to walk you through all the steps necessary to create indices we can use with [Algolia]() search.
We will generate these indices by parsing multiple HTML pages containing documentation about Algolia. 
The pages that will be parsed, can be found in the `docs` folder in the root of this repository.

### Algolia
Algolia provides a wide array of services for developers and the platforms they work on. 
These services range from an analytics service, to search as a service.
In this tutorial, we will create indices to use with their _search as a service_.

Algolia makes creating a search a breeze for developers, saving them hours of work and optimalization.
This enables developers to focus their effort on providing the most value to the end user of the platform they are working on.
Algolia will take care of the search, providing a great user experience to the end user with a lightning fast and user-friendly search through the content provided by the developers. 
This content is provided by sending so called _indices_ to the Algolia Service

#### Indices
Algolia makes use of indices to provide their _search as a service_.
An index is a schemaless object sent to Algolia by the developer.
Because an index is schemaless, it is extremely easy for developers to generate them and send them to Algolia.
To provide the most value to the end user though, it is important to think about how your users will use the search on your website, and what content the user will be looking for. 

### The documentation pages
The HTML pages in the `docs` folder in this repository, contain documentation on a couple of subjects related to using Algolia.
A good search function for the documentation is important, so that the reader can easily find the most relevant content for the terms they are looking for.

@TODO: blog post link
To be able to provide this search functionality, we are going to parse the contents of the documentation pages and create indices for it.
We will generate these indices based on [this blog post]() by the co-founder of Algolia. 
Each index will be given a `priority` score based on its contents. Let's get started!



## Setting up
In this step, we will walk you through the process of setting up your local machine to parse the documentation pages you can find in the `docs` folder. 

### System requirements
Please make sure you have the following software and tools installed on your computer:
- PHP 7 or higher
- [Composer]() @TODO: composer link

### Necessary files
From now on, we will assume you've copied the `docs` folder from this repository to your own project folder.

### Composer
Before we begin, we have to set up auto-loading for our project so the classes we create can be found by PHP.
Composer will handle the auto-loading for us, but before it can do that we need to setup Composer for our project.

In your project root, run the following command:
```shell
$ composer init
```

This will guide you through an interactive setup where you define your projects settings,
and generate a `composer.json` file containing all this information.

#### Dependencies

We need to install some dependencies before we can get to work. 
Run the following commands in your command line:
```shell
$ composer require-dev phpunit/phpunit "^6.5"
$ composer require symfony/dom-crawler "^3.4"
$ composer require symfony/css-selector "^3.4"
```

We will develop our parser according to the [test-driven methodology](). @TODO: tdd uitleg link 
In short, this means we will write unit tests to make sure the code we write (and possibly refactor) will always work as expected.
We will use PHPUnit to run our unit tests.

Our parser needs to be able to loop over all the elements present in the dom.
Symfony wrote an excellent package for this which we will use for this purpose.

#### Autoloading our classes
In order to be able to auto load the classes we will write for out software, 
we need to tell Composer where to find our classes. 
We can do this by adding the following snippet to our `composer.json` file generated

```json
{
  "autoload": {
    "psr-4": {
      "Devin\\Algolia\\": "src/"
    }
  }
}
```

Be sure to run the `$ composer dump-autoload -o` command after adding this snippet to your `composer.json`.

All your classes in the `Devin\Algolia` namespace, will now be loaded from the `src` folder in your project.

### PHPUnit
Before we can run our unit tests, we need to create a test suite for PHPUnit.
To do this, just copy the `phpunit.xml` file in this repository to your project folder, and create a `tests` folder.
This is where we will keep all our tests.

To run unit tests, simply run the following command from your command line:
```shell
$ php vendor/bin/phpunit
```


Your development environment should now be ready to develop, so let's dive in!


## HTML Parser
@TODO: paragraaf uitwerken 
Parsing an HTML document to create a search index requires some steps.

### Writing our first test
Following the TDD principles, we should start with writing our tests.
Since we are creating a parser, let's create a `ParserTest` class in your `tests` folder:

```php
<?php

class ParserTest extends \PHPUnit\Framework\TestCase
{
    public function test_it_runs_tests()
    {
        $this->assertEquals(true, true);
    }
}
```

All the tests we will write have to extend the `\PHPUnit\Framework\TestCase` class.
This class will provide our class with all the testing tools we need, like the `assertEquals` method.
To run your test, run `php vendor/bin/phpunit` in your command line.
It should tell you all your tests have passed!

### Creating our Parser class
We are going to replace the `test_it_runs_tests` method in `ParserTest.php` with a test to create a new instance of our `Parser` class.
To do this, we should think about how we want to instantiate our parser. 
Personally, I like expressive code that explains itself, so the test would look like this:

```php
<?php

use Devin\Algolia\Parser;

class ParserTest extends \PHPUnit\Framework\TestCase
{
    public function test_it_creates_a_new_instance()
    {
         $parser = new Parser();
        
         $this->assertInstanceOf(Parser::class, $parser);
    }
}
```
If we run this test now, it will fail. We have not even created our `Parser` class yet.


### Loading the HTML

### Looping over the elements

### Parsing the elements

### Generating the indices


## Further steps
- Send to algolia
- Expand parser; more than just body