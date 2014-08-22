$( document ).ready(function() {


if(typeof BASE_URL === 'undefined')
{
alert('BASE_URL not available in module ramstrg/js/calendar.js');
}
	var tSlots = {};
	$('.timeSlot').each(function(){

		var uxTime = $(this).attr('data-slot_tstampstart');
		var d = new Date(uxTime * 1000)
		tSlots[uxTime] = d; 



		// wenn classe fuer fehrien gesetzt ist dann nur in den geschaeftszeiten anzeigen 
		if(!$(this).hasClass('businessHours'))
		{
			$(this).removeClass('vacc');
		}
		else
		{
			if(!$(this).hasClass('booked'))
			{
				
				$(this).click(function(){
					var stantort = parseInt($(this).attr('data-standort'));
					location.href= BASE_URL + "ramstrg/book/index/" +uxTime+"/"+stantort;
					//alert(d);
				});
			}
			/*.bind("click touchstart", function(){

			  });
			*/	
		}
		if($(this).hasClass('vacc'))
		{
			$(this).html('Betriebsferien');
		}


	});

	/*

	  var bHours;
	  var bHours = axPost('http://localhost/py_ramstrg/ramstrg/get_business_hours/12','true=1');


	  //date test
	  var date1 = tSlots[1395597600];


	  function check_b_hour(tSlot)
	  {

	  $.each(bHours, function(i,item){

	  if(tSlot.getDay() <= item.day_start && tSlot.getDay() >= item.day_end)
	  {
	  return true;
	  }  
	  console.log(item.day_start +' '+ item.time_start +' '+ item.day_end +' '+ item.time_end);
	  console.log(tSlot.getDay() +' '+ tSlot.getTime('H:i:s') +' '+ tSlot +' '+ tSlot);

	  });


	  }
	  // ende datetest 
	  */


	// --------------------------------------------------------------------
	/**
	 * ajax post function 
	 *
	 * @param 	string	url
	 * @param 	string	post data var1=data&var2=data 
	 * @return 	object	json data
	 */
	function axPost(post_url,post_data)
	{

		var retVal;
		retVal = null;

		$.ajax({
			type: "POST",
			async: false,
			data: post_data,
			url: post_url,   
			dataType: "json",
			success: function(data) {
				return retVal = data;
			},
			error: function() {
				return alert("Error occured");
			}
		});

		return retVal;
	}


});
