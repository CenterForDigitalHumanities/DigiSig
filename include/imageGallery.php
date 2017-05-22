<?php
	
    echo "<div class='image_gallery'>
		<div class='galleryText'></div>
    	<div class='galleryImages'>".gather_random_slow()."</div>
      </div>";
 
    function gather_random_slow(){
        #constants and default values
		include "config/config.php";
        include "include/constants.php"; 
		$sql_slow =  "SELECT * FROM gallery_view ORDER BY RAND() LIMIT 5";
        $result = mysqli_query($link,$sql_slow);
        $returnHtml = "";
        if(isset($result) && $result!==""){
            while ($row = mysqli_fetch_array($result)) {
                				
			#JM revised gallery code -- I am using medium images but it is possible to switch to thumbnails
				$returnHtml = $returnHtml."<div class='imgFake'>";
                $imagemedium = $row['representation_filename'];
				$imagethumb = $row['representation_thumbnail'];
                $seal_id = $row['id_seal'];
				$imagelocation = $row['medium'];
				$imagelocation= str_replace("local",$address."/images/medium/",$imagelocation);
                $seal_connection = $address. "/entity/".$seal_id;
                //$seal_connection = "http://digisig.org/entity/".$seal_id;
                $shelfmark = $row['shelfmark'];
				$repository = $row['repository_fulltitle'];
								
				$image = $imagemedium;												                				
				if (isset($image) && $image!=="") {
                    $returnHtml = $returnHtml. '<a target="_blank" href="' . $seal_connection . '" title="' . $repository . ', ' . $shelfmark . '"><img class="galimg" src="'.$imagelocation. $image . '" /></a></div>';
                }else{
                    $returnHtml = $returnHtml. '<img src="' . $default . 'not_available_thumb.jpg"/></div>';
                }				
            }
        }
        return $returnHtml;
    }
