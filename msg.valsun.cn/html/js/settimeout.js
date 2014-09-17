$(document).ready(function(){
	setInterval(function(){ 
		$.ajax({
			  type: "GET",
			  url: "keepStatus.php",
			  dataType: "script"
		});
    },600000); 
});                                                                                                                                                         