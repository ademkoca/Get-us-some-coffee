<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="bootstrap-4.0.0-alpha.6-dist/js/bootstrap.min.js"></script>
<script src="bootstrap-4.0.0-alpha.6-dist/js/site.js"></script>
<link href="bootstrap-4.0.0-alpha.6-dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="bootstrap-4.0.0-alpha.6-dist/css/style.css" type="text/css">
<link rel="stylesheet" href="bootstrap-4.0.0-alpha.6-dist/font-awesome-4.7.0/css/font-awesome.min.css">
</head>
<body onload="getLocation()">
<p id="demo"></p>
<button type="button" onclick="findCoffee()">Search nearby coffee shops!</button>

<script>
var x = document.getElementById("demo");

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else { 
        x.innerHTML = "Geolocation is not supported by this browser.";
    }
}

function showPosition(position) {
    x.innerHTML = "Latitude: " + position.coords.latitude + 
    "<br>Longitude: " + position.coords.longitude;
}

function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            x.innerHTML = "User denied the request for Geolocation."
            break;
        case error.POSITION_UNAVAILABLE:
            x.innerHTML = "Location information is unavailable."
            break;
        case error.TIMEOUT:
            x.innerHTML = "The request to get user location timed out."
            break;
        case error.UNKNOWN_ERROR:
            x.innerHTML = "An unknown error occurred."
            break;
    }
}
function findCoffee(){
	
}

$(function() {
	var lat = "";
    var lng = "";
	var appendeddatahtml = "";
	var arguments = "";
	var str = "";
	var newstr = "";
	var phone = "";
	var rating = "";
	var icon = "";
	var address = "";
	
	$("#query").click(function(){
		$(this).val("");
	});
	
	$("#query").blur(function(){
		if ($(this).val() == "") {
			$(this).val("Example: Happy Hour");
		}
		
		if ($(this).val() != "Example: Happy Hour") {
			$(this).addClass("focus");
		} else {
			$(this).removeClass("focus");
		}
	});
	
	$("#searchform").submit(function(event){
		event.preventDefault();
		if (!lat) {
			navigator.geolocation.getCurrentPosition(getLocation);
		} else {
			getVenues();
		}
	});
	
	function getLocation(location) {
	    lat = location.coords.latitude;
	    lng = location.coords.longitude;
		getVenues();
	}
	
	function getVenues() {
		$.ajax({
	  		type: "GET",
	  		url: "https://api.foursquare.com/v2/venues/explore?ll="+lat+","+lng+"&client_id=YOUR_ID_GOES_HERE&client_secret=YOUR_SECRET_GOES_HERE&v=20130619&query="+$("#query").val()+"",
	  		success: function(data) {
				$("#venues").show();
				var dataobj = data.response.groups[0].items;
				$("#venues").html("");
				
				// Rebuild the map using data.
				var myOptions = {
					zoom:11,
					center: new google.maps.LatLng(lat,lng-.2),
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					panControl: false
				},
				map = new google.maps.Map(document.getElementById('map'), myOptions);
				
				// Build markers and elements for venues returned.
				$.each( dataobj, function() {
					if (this.venue.categories[0]) {
						str = this.venue.categories[0].icon.prefix;
						newstr = str.substring(0, str.length - 1);
						icon = newstr+this.venue.categories[0].icon.suffix;
					} else {
						icon = "";
					}
					
					if (this.venue.contact.formattedPhone) {
						phone = "Phone:"+this.venue.contact.formattedPhone;
					} else {
						phone = "";
					}
					
					if (this.venue.location.address) {
						address = '<p class="subinfo">'+this.venue.location.address+'<br>';
					} else {
						address = "";
					}
					
					if (this.venue.rating) {
						rating = '<span class="rating">'+this.venue.rating+'</span>';
					}
					
					appendeddatahtml = '<div class="venue"><h2><span>'+this.venue.name+'<img class="icon" src="'+icon+'"> '+rating+'</span></h2>'+address+phone+'</p><p><strong>Total Checkins:</strong> '+this.venue.stats.checkinsCount+'</p></div>';
					$("#venues").append(appendeddatahtml);
					
					// Build markers
					var markerImage = {
					url: '../images/pin2.png',
					scaledSize: new google.maps.Size(24, 24),
					origin: new google.maps.Point(0,0),
					anchor: new google.maps.Point(24/2, 24)
					},
					markerOptions = {
					map: map,
					position: new google.maps.LatLng(this.venue.location.lat, this.venue.location.lng),
					title: this.venue.name,
					animation: google.maps.Animation.DROP,
					icon: markerImage,
					optimized: false
					},
					marker = new google.maps.Marker(markerOptions)
					
				});
			}
		});
	}
	
	function mapbuild() {
		$("#venues").hide();
		var myOptions = {
		zoom:5,
		center: new google.maps.LatLng(38.962612,-99.080879),
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		panControl: false
		},
		map = new google.maps.Map(document.getElementById('map'), myOptions);
	}
	
	mapbuild();
});
</script>

</body>
</html>
