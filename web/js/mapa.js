//Obtenemos la latitud y la longitud
var latitud = document.getElementById('latitud').innerHTML;
var longitud = document.getElementById('longitud').innerHTML;
var pines =[];
var opciones =  {
	zoom:17,
	center: new google.maps.LatLng(latitud, longitud),
	mapTypeId: google.maps.MapTypeId.ROADMAP
};
//Pintamos el mapa
map = new google.maps.Map(document.getElementById('gmap_canvas'), opciones);
//Pin del mapa
var pin = new google.maps.Marker({
	position: new google.maps.LatLng(latitud, longitud),
	map: map,
	title: ""
});
pines.push(pin);
