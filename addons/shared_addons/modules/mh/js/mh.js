$( document ).ready(function() {


$('.delBtn').click(function(e) {
        var Check = confirm(unescape("Soll der Eintrag wirklich gel%F6scht werden?"));
        if (Check === false)
            e.preventDefault();
    });

$('#del_date').datetimepicker({
	lang:'de',
	timepicker:false,
format:'d.m.Y',
	formatDate:'d.m.Y',
scrollInput:false
});


$('#del_time').datetimepicker({
	datepicker:false,
	format:'H:i',
step:30
});

});
