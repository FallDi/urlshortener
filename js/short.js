"use strict";

function shortMe() {
	var inputUrl = document.getElementsByName('url')[0].value;
	
	var xmlhttp;
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else {
		alert('ajax is unsupported');
        return;
	}

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == XMLHttpRequest.DONE) {
			if(xmlhttp.status == 200) {
				document.getElementById('spanResult').innerHTML = "<a href='" + xmlhttp.responseText + "' target='_blank'>" + xmlhttp.responseText + "</a>";
			}
			else {
				alert('Something else other than 200 was returned :"(((')
			}
		}
	};

	xmlhttp.open('POST', 'short.php', true);
	xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xmlhttp.send('url=' + inputUrl);
}