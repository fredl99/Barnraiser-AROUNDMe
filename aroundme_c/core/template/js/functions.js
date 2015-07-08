// -----------------------------------------------------------------------
// This file is part of AROUNDMe
// 
// Copyright (C) 2003-2008 Barnraiser
// http://www.barnraiser.org/
// info@barnraiser.org
// 
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; see the file COPYING.txt.  If not, see
// <http://www.gnu.org/licenses/>
// -----------------------------------------------------------------------

//<![CDATA[

//puts browser window at top (out of frames) - stops bug with registering from inside hotmail frame.
if (self != top){
   if (document.images) top.location.replace(document.location.href);
   else top.location.href = document.location.href;
}


/**
 * Some browser detection
 */
var clientPC  = navigator.userAgent.toLowerCase(); // Get client info
var is_gecko  = ((clientPC.indexOf('gecko')!=-1) && (clientPC.indexOf('spoofer')==-1) &&
                (clientPC.indexOf('khtml') == -1) && (clientPC.indexOf('netscape/7.0')==-1));
var is_safari = ((clientPC.indexOf('AppleWebKit')!=-1) && (clientPC.indexOf('spoofer')==-1));
var is_khtml  = (navigator.vendor == 'KDE' || ( document.childNodes && !document.all && !navigator.taintEnabled ));
if (clientPC.indexOf('opera')!=-1) {
    var is_opera = true;
    var is_opera_preseven = (window.opera && !document.childNodes);
    var is_opera_seven = (window.opera && document.childNodes);
}



var myWindow;

function launchPopupWindow(page) {
	if(myWindow && !myWindow.closed) {
			myWindow.close();
	}

	var winWidth = 350;
	var winHeight = 550;
	
	customise = "scrollbars=yes,width="+winWidth+",height="+winHeight+",status=0";
	
	customise = customise + ',left='+20;
	customise = customise + ',top='+100;
	
	myWindow = window.open(page,null,customise);
	myWindow.focus();
}


function objShowHide(id) {

	if (document.getElementById) {
		if (document.getElementById(id).style.display == 'block') {
			document.getElementById(id).style.display = 'none';
		}
		else {
			document.getElementById(id).style.display = 'block';
		}
	}
	else {
		if (document.layers) {
			if (document.id.visibility == 'block') {
				document.id.visibility = 'none';
			}
			else {
				document.id.visibility = 'block';
			}
		}
		else { // IE 4
			if (document.all.id.style.display == 'block') {
				document.all.id.style.display = 'none';
			}
			else {
				document.all.id.style.display = 'block';
			}
		}
	}
}

// AJAX _GET request to script
var http_request = false;

function makeRequest(url, parameters, destination) {

	http_request = false;
	
	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
		// set type accordingly to anticipated content type
		http_request.overrideMimeType('text/xml');
		//http_request.overrideMimeType('text/html');

		}
	}
	else if (window.ActiveXObject) { // IE
		try {
		http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
		try {
			http_request = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (e) {}
		}
	}
	
	if (!http_request) {
		alert('Cannot create XMLHTTP instance');
		return false;
	}
	/*
	http_request.onreadystatechange = destination;
	http_request.open('POST', url + parameters, true); // we using GET - tom
	http_request.send(null);
	*/
	http_request.onreadystatechange = destination;
	http_request.open('POST', url, true); 
	http_request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	http_request.send(parameters);
}


function check_uncheck(id_to_check, id_to_color) {
	
	if (document.getElementById(id_to_check).checked) {
		document.getElementById(id_to_check).checked = false;
		document.getElementById(id_to_color).style.backgroundColor = "#FFFFFF";
	}
	else {
		document.getElementById(id_to_check).checked = true;
		document.getElementById(id_to_color).style.backgroundColor = "#F4F4F4";
	}
}

/* displays input size indicator */
function inputMaxLength(id, maxlength, indicator) {

	currentsize = id.value.length+1;

	if (currentsize > maxlength) {
		currentsize = maxlength
		id.value = id.value.substring(0, maxlength-1);
	}
	document.getElementById(indicator).innerHTML = maxlength-currentsize;
}

// nice titles
function showInterfaceSystemMessage(e, title_id, message_id) {
	var mposx = 0;
	var mposy = 0;
	if (!e) var e = window.event;
	if (e.pageX || e.pageY) {
		mposx = e.pageX;
		mposy = e.pageY;
	}
	else if (e.clientX || e.clientY) 	{
		mposx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
		mposy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
	}
	//we take the inner html from the given ID and push it into the error box
	document.getElementById('interface_system_message_header').innerHTML = document.getElementById(title_id).innerHTML;
	document.getElementById('interface_system_message_body').innerHTML = document.getElementById(message_id).innerHTML;
	document.getElementById('interface_system_message').style.display = 'block';
}

function hideInterfaceSystemMessage(id) {
	document.getElementById('interface_system_message').style.display = 'none';
}

function checkImage(_image) {
	_image.onerror = function() {
		_image.style.display = 'none';
	}
}

function checkImages() { 
	_images = document.getElementsByTagName('img'); 
	for(i=0; i < _images.length; i++) { 
		checkImage(_images[i]); 
	} 
}

//]]>