jQuery(function($) {

    // generate a slug when the user types a title in
    pyro.generate_slug('input[name="name"]', 'input[name="slug"]');

    $('.standortDel,.rampDel, #standortDel').click(function(e) {
        var Check = confirm(unescape("Soll der Eintrag wirklich gel%F6scht werden?"));
        if (Check === false)
            e.preventDefault();
    });


    if (typeof formMode == 'undefined' || formMode == 'create') {
        $(".tabs").tabs({
            disabled: [1, 2]
        });
    }
    // Enable/Disable table action buttons geschäftszeiten
    $('input[name="del_bh[]"], .check-all').live('click', function() {

        if ($('input[name="del_bh[]"]:checked, .check-all:checked').length >= 1) {
            $(".table_action_buttons .btn").prop('disabled', false);
        } else {
            $(".table_action_buttons .btn").prop('disabled', true);
        }
    });

    // Enable/Disable table action buttons ferien
    $('input[name="del_holidays[]"], .check-all').live('click', function() {

        if ($('input[name="del_holidays[]"]:checked, .check-all:checked').length >= 1) {
            $(".table_action_buttons .btn").prop('disabled', false);
        } else {
            $(".table_action_buttons .btn").prop('disabled', true);
        }
    });

    // colorbox für geschäftszeiten 
    $('a#newBH').colorbox({

        srollable: false,
        innerWidth: 800,
        innerHeight: 500,
        onComplete: function() {
            $.colorbox.resize();
            e.preventDefault();

        }
    });

    // colorbox für betriebsferien 
    $('a#newHD').colorbox({

        srollable: false,
        innerWidth: 800,
        innerHeight: 500,
        onComplete: function() {
            $.colorbox.resize();
            e.preventDefault();

        }
    });

    $('.holidays_action_buttons [name="btnAction"]').addClass('disabled');



    $('.dpBH').datepicker({
        onSelect: function() {
            if ($('input[name="date_start"]').val() !== '' && $('input[name="date_end"]').val() !== '') {
                var dateObject = $(this).datepicker('getDate');
                $('.holidays_action_buttons [name="btnAction"]').removeClass('disabled');

            }
        },
        dateFormat: 'yy-mm-dd'
    });

    function setACT() {

    }
    //$('a#newHD').trigger('click');


});
