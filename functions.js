jQuery(document).ready(function($){

    $('#select_all').on('click', function() {

        // get the checkboxes
        var checkboxes = $('#homepage-categories-wrap').find('.form-table').find('input');

        if( $(this).prop('checked') ) {
            $.each(checkboxes, function() {
                $(this).prop('checked', true);
            });
        } else {
            $.each(checkboxes, function() {
                $(this).prop('checked', false);
            });
        }
    });
});