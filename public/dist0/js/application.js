$(function(){

    var renderBookingReferences = function(_center_id){

        var _booking_id = $('input[name=selected_booking_id]').length > 0 ? $('input[name=selected_booking_id]').val() : 0;

        $.ajax({
            url: $('.centers-dropdown').data('url'),
            type: 'GET',
            data: {'center_id': _center_id, booking_id: _booking_id},
            success: function(response){
                $(response).insertAfter('.centers-dropdown')
            }
        });

    };

    if($('.centers-dropdown select').val() !== '') renderBookingReferences($('.centers-dropdown select').val());

    $('.centers-dropdown select').change(function(e){

        e.stopPropagation();

        var _this = $(this);
        if(_this.val() == '') {_this.parent().eq(0).next().remove(); return true;}

        renderBookingReferences(_this.val());
    });

});