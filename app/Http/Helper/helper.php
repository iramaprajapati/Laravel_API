<?php
if (!function_exists('dataPrint')) {
    function dataPrint($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}
