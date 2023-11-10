jQuery(document).ready(function ($) {
    // Trigger AJAX request on select change
    $('#island-select').change(function () {
        var selectedIsland = $(this).val();

        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'get_schedule_for_departure_island',
                selectedIsland: selectedIsland,
            },
            success: function (response) {
                // Display the response in a container (e.g., div with id 'schedule-output')
                $('#schedule-output').html(response);
            },
        });
    });
});