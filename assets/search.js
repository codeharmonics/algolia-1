$(() => {
    // Initialize the seach
    const search = instantsearch({
        appId: 'YOUR_APP_ID',
        apiKey: 'YOUR_SEARCH_ONLY_API_KEY',
        indexName: 'documentation',
        routing: true
    });

    // Add a custom rendering engine
    search.addWidget({
        render: (opts) => {
            const items = opts.results.hits;
            let resultHTML = items.map(item => '' +
                '<a class="list-group-item list-group-item-action flex-column align-items-start" target="_blank" href="/docs/' + item.link +'">' +
                '<h5 class="mb-1">' +(item._highlightResult.h1 ? item._highlightResult.h1.value  : '') + '</h5>' +
                '<h6 class="mb-1">' +(item._highlightResult.h2 ? item._highlightResult.h2.value  : '') + '</h6>' +
                '<h7 class="mb-1">' +(item._highlightResult.h3 ? item._highlightResult.h3.value  : '') + '</h7>' +
                '<p>' + (item._highlightResult.content ? item._highlightResult.content.value  : '') + '</p>' +
                '</a>');
            $('#results').html(resultHTML);
        },
    });

    // Attach the searchbox widget
    search.addWidget(
        instantsearch.widgets.searchBox({
            container: '#search-box',
            placeholder: 'Search the documentation'
        })
    );

    // Set the distinct property to 1, so we only get 1 result (the most relevant) per page
    search.addWidget(
        instantsearch.widgets.configure({
            distinct: 1,
        })
    );

    // Start the search
    search.start();

    // If the search input is empty, hide the list (much prettier)
    $('.ais-search-box--input').on('input', () => {
        if ( $('.ais-search-box--input').val() === '') {
            $('#results').hide();
            return;
        }

        $('#results').show();
    }).trigger('input');
});