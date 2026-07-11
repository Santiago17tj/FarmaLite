<?php 	

require_once 'core.php';

$sql = "SELECT brand_id, brand_name, brand_active, brand_status FROM brands WHERE brand_status = 1";
$result = $connect->query($sql);
$data = $result->fetchAll(PDO::FETCH_BOTH);

$output = array('data' => array());

if(count($data) > 0) { 

 $activeBrands = ""; 

 foreach($data as $row) {
 	$brandId = $row[0];
 	if($row[2] == 1) {
 		$activeBrands = "<label class='label label-success'>Available</label>";
 	} else {
 		$activeBrands = "<label class='label label-danger'>Not Available</label>";
 	}

 	$button = '
	<div class="btn-group">
	  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    Action <span class="caret"></span>
	  </button>
	  <ul class="dropdown-menu">
	    <li><a type="button" data-toggle="modal" data-target="#editBrandModel" onclick="editBrands('.$brandId.')"> <i class="glyphicon glyphicon-edit"></i> Edit</a></li>
	    <li><a type="button" data-toggle="modal" data-target="#removeMemberModal" onclick="removeBrands('.$brandId.')"> <i class="glyphicon glyphicon-trash"></i> Remove</a></li>       
	  </ul>
	</div>';

 	$output['data'][] = array( 		
 		$row[1], 		
 		$activeBrands,
 		$button
 		); 	
 } // /foreach 

} // if count
// header removed

echo json_encode($output);
?>
