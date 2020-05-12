<?php

use App\Mysql;

$db = Mysql::getInstance();
$result = $db->query("SELECT * FROM place");
$info = $result->fetch(PDO::FETCH_ASSOC);
echo json_encode($info);
?>
<script>
    alert("test");
</script>
