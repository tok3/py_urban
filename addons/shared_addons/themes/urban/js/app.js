// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
$(document).foundation();


$(document).ready(function() 
{ 

	
	fixFooter();

	window.onresize = fixFooter;

	function fixFooter()
	{


		var content = document.querySelector('#content');

		var div = document.querySelector('#footer');

		var footerHeight = div.offsetHeight;
		var footerBottom =  div.offsetTop + footerHeight;
		var innerHeight = window.innerHeight;

		var currStylePos = document.getElementById('footer').style.position;
		var gap = innerHeight - footerBottom;

		if(gap > 0)
		{
			var cHeight = innerHeight - footerHeight - content.offsetTop;
			document.getElementById('content').style.height = cHeight+ 'px';

		}
	}

// set pagination classes to achieve foundation styled pagination without tweaking codeigniters pagination function outside this theme. 
$('div.pagination').next().addClass('pagination');
$("div.pagination ul").first().addClass('pagination');

});
