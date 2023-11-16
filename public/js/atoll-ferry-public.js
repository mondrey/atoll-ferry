jQuery(document).ready(function ($) {


// Function to add scroll event listener to containers with overflow using jQuery
function addScrollListenerToContainersWithOverflow() {
    // Get all containers with the specified class
    var containers = $('.interconnected-route');

    // Iterate through each container
    containers.each(function() {
        // Get the corresponding scroll indicator for each container
        var scrollIndicator = $(this).find('.scroll-indicator');
        // Get the scroll container
        var scrollContainer = $(this).find('.interconnected-route-path-wrap');

        // Add a scroll event listener to each container's scroll container
        scrollContainer.on('scroll', function() {
            // Check if overflow-x is present
            if (scrollContainer[0].scrollWidth > scrollContainer.innerWidth()) {
                scrollIndicator.show(); // Show the indicator
                scrollContainer.removeClass('scrollable-route');
            } else {
                scrollIndicator.hide(); // Hide the indicator
            }
        });

        // Check for overflow initially
        if (scrollContainer[0].scrollWidth > scrollContainer.innerWidth()) {
            scrollIndicator.show(); // Show the indicator
            scrollContainer.addClass('scrollable-route');
            setTimeout(function() {
                // Remove the 'scrollable-route' class after 10 seconds
                scrollContainer.removeClass('scrollable-route');
            }, 10000); // 10000 milliseconds = 10 seconds
        } else {
            scrollIndicator.hide(); // Hide the indicator
        }
    });
}

    // Trigger AJAX request on select change
    if ($.fn.select2 && $('#island-select').length) {
        $('#island-select').select2({
            width: '200px'
        });
    }

    $('#island-select').change(function () {
        var selectedIsland = $(this).val();
        console.log( selectedIsland );
        $('.loader-ellipsis-wrap-inner').show();
        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'get_all_schedules_for_departure_island',
                selectedIsland: selectedIsland,
            },
            success: function (response) {
                // Display the response in a container (e.g., div with id 'schedule-output')
                console.log( response );
                $('.loader-ellipsis-wrap-inner').hide();
                $('#schedule-output').html(response);
                addScrollListenerToContainersWithOverflow(); // Add scroll listener after content is loaded
            },
        });
    });

    $(document).on('click', '.island-filters', function () {

        $('.island-filters').removeClass('chosen-filter');
        $(this).addClass('chosen-filter');
        var filter_island = $(this).data('island');

        $(".interconnected-route").hide();

        $(".interconnected-route").each(function(i) {
            if ( $(this).hasClass( 'filter-' + filter_island) ) {
                $(this).show();
            }
        });


        $(".tag-island").each(function() {
            $(this).removeClass('island-highlight');
        });

        // $( ".interconnected-route" ).hide();
        console.log(filter_island);
        $('.tag-' + filter_island).addClass('island-highlight');
    });

    $('#search-btn').on('click', function() {
        var departureIsland = $('#island-select').val();
        var destinationIsland = $('#island-destination').val();

        // AJAX request to retrieve interconnected ferry schedules
        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url, // WordPress AJAX handler URL
            data: {
                action: 'get_interconnected_ferry_schedules',
                departureIsland: departureIsland,
                destinationIsland: destinationIsland
            },
            success: function(response) {
                $('#schedule-output').html(response);
            }
        });
    });
});