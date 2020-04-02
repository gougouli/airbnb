<?php


if($list){
    foreach ($list as $acco)
    $lat = $acco['place']['lat'];
    $long = $acco['place']['lon'];
    ?>
    <script>
    window.setInterval(function(){
        L.marker([ <?= $lat; ?>, <?= $long; ?> ]).addTo(macarte).bindPopup("<b><?= $acco['title']; ?>.").openPopup();
        //alert("<?//= $lat; ?>//, <?//= $long; ?>//");
    }, 2000);
</script>
<?php
}
?>

