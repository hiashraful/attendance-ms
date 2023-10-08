<?php
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$latitude = (float) $latitude;
$longitude = (float) $longitude;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
    />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
</head>
<body>
    <form action="" method="post">
        <input type="text" name="latitude"  >
        <input type="text" name="longitude" >
        <input type="submit" name="submit" value="submit">
    </form>
</body>
<div id="map" style="width: 600px; height: 450px"></div>

    <script>
      var map = L.map("map").setView([<?php echo $latitude;?>, <?php echo $longitude;?>], 15);
      var marker = L.marker([<?php echo $latitude;?>, <?php echo $longitude;?>]).addTo(map);
      var circle = L.circle([<?php echo $latitude;?>, <?php echo $longitude;?>], {
        color: "green",
        fillColor: "#cccff",
        fillOpacity: 0.2,
        radius: 500,
      }).addTo(map);
      L.marker([<?php echo $latitude;?>, <?php echo $longitude;?>])
        .addTo(map)
        .bindPopup("Ashraful was here.<br> when logged in.")
        .openPopup();
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution:
          '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      }).addTo(map);
    </script>
<script>
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function (position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        document.getElementsByName("latitude")[0].value = latitude;
        document.getElementsByName("longitude")[0].value = longitude;
        });
    } else {
        console.log("Browser doesn't support geolocation!");
    }
</script>
</html>


<?php if ($location['latitude'] == null) {echo 0;} else {echo $location['latitude'];}?>
<?php if ($location['longitude'] == null) {echo 0;} else {echo $location['longitude'];}?>