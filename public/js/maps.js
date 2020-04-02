navigator.geolocation.getCurrentPosition(function(position) {
	const lat = position.coords.latitude;
	const lon = position.coords.longitude;
	var macarte = null;
	// Fonction d'initialisation de la carte
	function initMap() {
		// Créer l'objet "macarte" et l'insèrer dans l'élément HTML qui a l'ID "map"
		macarte = L.map('map').setView([lat, lon],13);
		// Leaflet ne récupère pas les cartes (tiles) sur un serveur par défaut. Nous devons lui préciser où nous souhaitons les récupérer. Ici, openstreetmap.fr
		L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
			// Il est toujours bien de laisser le lien vers la source des données
			attribution: 'données © <a href="//osm.org/copyright">OpenStreetMap</a>/ODbL - rendu <a href="//openstreetmap.fr">OSM France</a>',
			minZoom: 1,
			maxZoom: 20
		}).addTo(macarte);
	}
	window.onload = function(){
		// Fonction d'initialisation qui s'exécute lorsque le DOM est chargé
		initMap();
		L.marker([position.coords.latitude, position.coords.longitude]).addTo(macarte).bindPopup("<b>Votre position.").openPopup();
	};

});


// var settings = {
//   "async": true,
//   "crossDomain": true,
//   "url": "https://eu1.locationiq.com/v1/search.php?key=f539d8ca0e50b6&q=7+place+de+la+resistance+vourles+69390&format=json",
//   "method": "GET"
// };
//
// $.ajax(settings).done(function (response) {
//
// 	L.marker([response[0].lat, response[0].lon]).addTo(macarte).bindPopup("<b>test position.").openPopup();
// });
