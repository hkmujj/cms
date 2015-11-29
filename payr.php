<?php
if (isset($_GET['payr'])) {
    echo '<script>window.close();</script>';
    exit;
}
require("func.php");
if (isset($_GET['payresult'])) {
    getPayResult();
}else notify();