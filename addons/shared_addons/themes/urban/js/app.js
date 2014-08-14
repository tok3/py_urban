// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
$(document).foundation();


$(document).ready(function() 
{ 

	function realignFooter()
	{
		if(sticky_footer != 'off')
		{
			if ((sticky_footer == 'allways') || (sticky_footer == 'on_less_content' && $("body").height() < $(window).height())) {

				var footerWidth = document.getElementById('footer').offsetWidth;

				document.getElementById('footer').style.position = "fixed"; 
				document.getElementById('footer').style.bottom = "0"; 
				$( "<p></p>" ).insertAfter( "#content");
				
				if(footer_width == 'content')
				{
					document.getElementById('footer').style.width = footerWidth + "px"; 
				}
			}
			
			if(sticky_footer == 'on_less_content' && $("body").height() > $(window).height()) 
			{			
				document.getElementById('footer').style.position = "relative"; 

			}

		}
	}

	realignFooter();

	window.onresize = function() {
		realignFooter();

	};

	//footer.realign();

// set pagination classes to achieve foundation styled pagination without tweaking codeigniters pagination function outside this theme. 
$('div.pagination').next().addClass('pagination');
$("div.pagination ul").first().addClass('pagination');

});