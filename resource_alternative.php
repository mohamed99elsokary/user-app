<?php
$object = [
    "id" => 1,
    "name" => "mohamed",
    "email" => "mohamed99elsokary@gmail.com",
    "mobile" => "+201111155856",
];

function new_resource($data, $required)
{
    $new_object = array();
    foreach ($required as $i) {
        $new_object[$i] = $data[$i];
    }
    print_r($new_object);
}
new_resource($object, ["id", "name", "email", "mobile"]);
