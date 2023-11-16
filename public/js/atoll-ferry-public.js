jQuery(document).ready(function ($) {


    function copyUrlToClipboard() {
        // Select the DIV containing the URL
        var urlDiv = $('#share-departure-url'); // Replace 'your-url-div-id' with the actual ID of your DIV
    
        // Create a temporary input element
        var tempInput = $('<input>');
    
        // Set the input's value to the text content of the URL DIV
        tempInput.val(urlDiv.text());
    
        // Append the input to the body
        $('body').append(tempInput);
    
        // Select the input's content
        tempInput.select();
    
        // Copy the selected content to the clipboard
        document.execCommand('copy');
    
        // Remove the temporary input
        tempInput.remove();
    
        // Optionally, provide some visual feedback to the user
        $('.share-button-text').text('Copied').addClass('copied');
        $('#copy-shareurl-button').addClass('copied');
    }
    
    // Example: Call the function on a button click
    $(document).on('click', '#copy-shareurl-button', function () {
        copyUrlToClipboard();
    });

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

        // Parse the URL to get the value of the 'departure' parameter
        const urlParams = new URLSearchParams(window.location.search);
        const departure = urlParams.get('departure');

        // Check if the departure exists and set it in the Select2 dropdown
        if (departure) {
            // Set the selected value directly
            $('#island-select').val(departure).trigger('change');

            // Perform the AJAX request for the selected island
            updateScheduleForSelectedIsland(departure);
        }
    }
// Event listener for select change
$('#island-select').change(function () {
    var selectedIsland = $(this).val();
    console.log(selectedIsland);
    $('.loader-ellipsis-wrap-inner').show();

    // Retrieve the nonce from the server (assuming it's included in the AJAX response)
    $.ajax({
        type: 'POST',
        url: ajax_object.ajax_url,
        data: {
            action: 'get_all_schedules_for_departure_island',
            selectedIsland: selectedIsland,
        },
        success: function (response) {
            // Check if the nonce is present in the response
            if (response.success && response.data.nonce) {
                var nonce = response.data.nonce;

                // Perform the AJAX request for the selected island with the nonce
                updateScheduleForSelectedIsland(selectedIsland, nonce);
            }
        },
    });
});

// Function to update schedule based on the selected island and nonce
function updateScheduleForSelectedIsland(selectedIsland, nonce) {
    $.ajax({
        type: 'POST',
        url: ajax_object.ajax_url,
        data: {
            action: 'get_all_schedules_for_departure_island',
            selectedIsland: selectedIsland,
            nonce: nonce, // Include the nonce in the request
        },
        success: function (response) {
            // Check if the nonce is present in the response
            if (response.success && response.data.html) {
                // Display the response in a container (e.g., div with id 'schedule-output')
                console.log(response);
                $('.loader-ellipsis-wrap-inner').hide();
                $('#schedule-output').html(response.data.html);
                addScrollListenerToContainersWithOverflow(); // Add scroll listener after content is loaded

                // Update the #share-destination-url with the selected value
                var baseUrl = window.location.origin + window.location.pathname; // Get the current base URL
                var select2Value = selectedIsland; // Assuming select2Value is the selected value

                // Construct the destination URL
                var destinationUrl = baseUrl + '?departure=' + select2Value;

                // Update the #share-destination-url DIV
                $('#share-departure-url').text(destinationUrl);
            }
        },
    });
}

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