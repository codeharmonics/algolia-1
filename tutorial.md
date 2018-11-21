# Splitting HTML pages

## Introduction
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

To be able to provide this search functionality, we are going to parse the contents of the documentation pages and create indices for it.
We will generate these indices based on [this blog post]() by the co-founder of Algolia. 
Each index will be given a `priority` score based on its contents.



## Steps
- Setup project
    - Requirements
    - 
- Load the html
- Traverse the dom
    - Parse each element
    - Generate index object