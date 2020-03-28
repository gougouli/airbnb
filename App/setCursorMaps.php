<?php


if($list){
    foreach ($list as $acco)
    $lat = $acco['infoplace']['lat'];
    $long = $acco['infoplace']['lon'];
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

