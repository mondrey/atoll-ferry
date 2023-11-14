jQuery(document).ready(function ($) {
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