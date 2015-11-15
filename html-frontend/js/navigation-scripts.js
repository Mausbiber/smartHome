// JavaScript Document

$("#menu-toggle").click(function(e) {
	e.preventDefault();
	$("#wrapper").toggleClass("toggled");
});

window.setInterval("zeitanzeige()",1000);

function zeitanzeige()
{
	d = new Date ();
	h = (d.getHours () < 10 ? '0' + d.getHours () : d.getHours ());
	m = (d.getMinutes () < 10 ? '0' + d.getMinutes () : d.getMinutes ());
	s = (d.getSeconds () < 10 ? '0' + d.getSeconds () : d.getSeconds ());
	var monate = new Array ("Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
	/*var monate = new Array ("Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez");*/
	document.getElementById("anzeige_uhrzeit").innerHTML = h + ':' + m + ' Uhr';
	document.getElementById("anzeige_datum").innerHTML = d.getDate () + '. ' + monate[d.getMonth ()] + ' ' + d.getFullYear ();
}
