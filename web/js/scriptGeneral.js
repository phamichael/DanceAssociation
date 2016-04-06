function getCookie(name) {
    var cookieValue = null;
    if (document.cookie && document.cookie != '') {
        var cookies = document.cookie.split(';');
        for (var i = 0; i < cookies.length; i++) {
            var cookie = jQuery.trim(cookies[i]);
            // Does this cookie string begin with the name we want?
            if (cookie.substring(0, name.length + 1) == (name + '=')) {
                cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                break;
            }
        }
    }
    return cookieValue;
}
var csrftoken = getCookie('csrftoken');

function csrfSafeMethod(method) {
    return (/^(GET|HEAD|OPTIONS|TRACE)$/.test(method));
}
$.ajaxSetup({
    beforeSend: function(xhr, settings) {
        if (!csrfSafeMethod(settings.type) && !this.crossDomain) {
            xhr.setRequestHeader("X-CSRFToken", csrftoken);
        }
    }
});


// ======================================================================================================
// ======================================================================================================
// ======================================================================================================


// Afficher/Cacher une fiche (de stage,...) :
$(document).ready(function() {
	$("#visualiser_fiche a").click(function(evt){
		evt.preventDefault();
		var div = $("#fiche");
		if(div.is(":visible")){
			div.hide();
			$(this).html("Visualiser la fiche du stage");
		}else{
		   div.show();
		   $(this).html("Cacher la fiche du stage");
		}
    });
});

// Afficher/Cache Confirmation de suppression :

	//affiche
$(document).ready(function() {
	$("#btnSuppr").click(function(evt){
		evt.preventDefault();
		var div = $("#confirSuppr");
		if(!div.is(":visible")){
		   div.show();
		   $(this).hide();
		}
    });
});

//cache
$(document).ready(function() {
	$("#btnAnnulerSuppr").click(function(evt){
		evt.preventDefault();
		var div = $("#confirSuppr");
		if(div.is(":visible")){
		   div.hide();
		   $("#btnSuppr").show();
		}
    });
});
