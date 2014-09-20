<?php

if(isset($_POST["room"]) && !empty($_POST["room"])){
    $room = $_POST["room"];
    echo file_get_contents("rooms/".$room.".txt");
}