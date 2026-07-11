<?php

require_once "core.php";

$sql = "SELECT * FROM users";
$result = $connect->query($sql);
$data = $result->fetchAll(PDO::FETCH_BOTH);

$output = ["data" => []];
if (count($data) > 0) {
    $active = "";

    foreach ($data as $row) {
        $userid = $row[0];
        $username = $row[1];

        $button =
            '
	<div class="btn-group">
	  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    Action <span class="caret"></span>
	  </button>
	  <ul class="dropdown-menu">
	    <li><a type="button" data-toggle="modal" id="editUserModalBtn" data-target="#editUserModal" onclick="editUser(' .
            $userid .
            ')\"> <i class="glyphicon glyphicon-edit"></i> Edit</a></li>
	    <li><a type="button" data-toggle="modal" data-target="#removeUserModal" id="removeUserModalBtn" onclick="removeUser(' .
            $userid .
            ')\"> <i class="glyphicon glyphicon-trash"></i> Remove</a></li>
	  </ul>
	</div>';

        $output["data"][] = [
            $username,
            $button,
        ];
    } // /foreach
} // if count

echo json_encode($output);
