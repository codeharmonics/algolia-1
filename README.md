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
 
 Open the `create_indices` file in your favorite editor, and replace the values of the `$appId` and `$appSecret` variables to contain your own ID and API key.
 
 To create the indices for the documentation pages, run:
```shell
 $ php create-indices
```
This will create an index called `devin_documentation` for easy lookup in your account.

I chose to create the indices through a command instead of (for example) a webpage to keep it easier, since you are already in the terminal to run the commands listed above.

### 2. Search front-end
To see the search implementation using the indices created in the step above, run the following command from the project root:

 ```shell
 $ php serve
 ```
This is a command I added that will automatically (try) to set up a webserver using the build-in webserver from PHP. 
Alternatively, you can [start a webserver](http://php.net/manual/en/features.commandline.webserver.php) yourself using the following command:

```shell
# php -S <yourhost>:<yourport>

$ php -S 127.0.0.1:8000
```
The webserver will be accessible through your browser on the host and port you provided in this command.

### 3. Write a tutorial about step 1
The tutorial can be found [here](tutorial.md).

***
I really enjoyed the assignment, and hope you are as happy with the result as I am!

Cheers,

Devin.
