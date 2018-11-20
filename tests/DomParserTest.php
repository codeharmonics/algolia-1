<?php

use Devin\Algolia\Parser;

class DomParserTest extends \PHPUnit\Framework\TestCase
{
    public function test_it_creates_an_instance()
    {
        $parser = Parser::forFile(__DIR__ . '/test.html');

        $this->assertInstanceOf(Parser::class, $parser);
    }

    public function test_it_throws_an_exception_for_invalid_path()
    {
        $this->expectException(\Devin\Algolia\Exceptions\InvalidPathException::class);

        Parser::forFile('pathtoinvalidfile.txt');
    }

    public function test_it_parses_h1_tags_correctly()
    {
        $expected = [
            ['h1' => 'Install', 'importance' => 0],
        ];

        $result = Parser::forFile(__DIR__ . '/test.html', 'foo')->parse()->getIndexableObjects();

        $this->assertArraySubset($expected, $result);
    }

    public function test_it_parses_h2_tags_correctly()
    {
        $expected = [
            ['h1' => 'Install', 'importance' => 0],
            ['h1' => 'Install', 'h2' => 'Install the algolia/scout package', 'link' => 'foo#Install_the_algoliascout_package_2', 'importance' => 1],
        ];

        $result = Parser::forFile(__DIR__ . '/test.html', 'foo')->parse()->getIndexableObjects();

        $this->assertArraySubset($expected, $result);
    }

    public function test_it_parses_paragraph_tags_correctly()
    {
        $expected = [
            ['h1' => 'Install', 'importance' => 0],
            ['h1' => 'Install', 'h2' => 'Install the algolia/scout package', 'link' => 'foo#Install_the_algoliascout_package_2', 'importance' => 1],
            ['h1' => 'Install', 'h2' => 'Install the algolia/scout package', 'link' => 'foo#Install_the_algoliascout_package_2', 'importance' => 5, 'content' => 'First, install Scout and Algolia’s API client via composer'],
        ];

        $result = Parser::forFile(__DIR__ . '/test.html', 'foo')->parse()->getIndexableObjects();

        $this->assertArraySubset($expected, $result);
    }

    public function test_it_parses_adjacent_h2_tags_correctly()
    {
        $expected = [
            ['h1' => 'Install', 'importance' => 0],
            ['h1' => 'Install', 'h2' => 'Install the algolia/scout package', 'link' => 'foo#Install_the_algoliascout_package_2', 'importance' => 1],
            ['h1' => 'Install', 'h2' => 'Install the algolia/scout package', 'link' => 'foo#Install_the_algoliascout_package_2', 'importance' => 5, 'content' => 'First, install Scout and Algolia’s API client via composer'],
            ['h1' => 'Install', 'h2' => 'Enabling scout', 'link' => 'foo#Enabling_scout_11', 'importance' => 1],
        ];

        $result = Parser::forFile(__DIR__ . '/test.html', 'foo')->parse()->getIndexableObjects();

        $this->assertArraySubset($expected, $result);
    }
}
