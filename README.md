# Technical Test
Hi there 👋!

This is what I've made from the [technical assignment](https://github.com/algolia/doc-engineer-assignment) I was asked to make.
If you have any questions or feedback, [please let me know](mailto:devinbeeuwkes7@gmail.com).

## Deliverables
### 1. Indexing
 In order to run the [indexing](https://github.com/algolia/doc-engineer-assignment#1-indexing) assignment on your local machine,
 please make sure your machine has the following software installed:
 - [php version 7 or higher](http://php.net/manual/en/install.php)
 - [Composer](https://getcomposer.org/doc/00-intro.md) 
 
 Open the root of this project in your terminal, and install all composer dependencies:
 ```shell
 $ composer install
 ```
 
 To run the unit test defined for the project, run:
 ```shell
  $ php vendor/bin/phpunit
  ```
 
 Open the `create-records` file in your favorite editor, and replace the values of the `$appId` and `$apiKey` variables to contain your own app ID and API key.
 
 To create the indices for the documentation pages, run:
```shell
 $ php create-records
```
This will create an index called `devin_documentation` for easy lookup in your account.

I chose to create the indices through a command instead of (for example) a webpage to keep it easier, since you are already in the terminal to run the commands listed above.

All the code that is used to generate the index, can be found in the [`src`](src) folder.

### 2. Search front-end
Open the [`assets/search.js`](assets/search.js) file in your favorite editor, and change the `appId` and `apiKey` variables at the top of the file to contain your own app ID and search-only API key. 
 
To see the search implementation using the records created in the first step, just open the [`index.html`](index.html) file in your browser.
In order for it to work, you do need a working internet connection.

### 3. Write a tutorial about step 1
The tutorial can be found in [tutorial.md](tutorial.md).

***
I really enjoyed the assignment, and hope you are as happy with the result as I am!

Cheers,

Devin.
