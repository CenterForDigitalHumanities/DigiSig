<?php
    //Can we make a "load new set" functionality?
    //pk_class represents certain catagorizations.  Can we make a "load a new set based on catagory X" functionality?
    

    echo "<div class='image_gallery'>
    	<div class='galleryText'>Explore<br>do some stuff and some other things and have a lot of fun doing it OR ELSE!</div>
    	<div class='galleryImages'>".gather_random_slow()."</div>
      </div>";

    //works across all tables, not limited bu tables with unique ids. runs in 13% of the time of gahter_random_slow
    function gather_random_fast(){
        include "config/config.php";
        #constants and default values
        include "include/constants.php";
        #constants and default values
        $offset_result = mysqli_query($link,"SELECT FLOOR(RAND() * COUNT(*)) AS offset FROM gallery_view WHERE fk_access=1");
        $offset_row = mysqli_fetch_object( $offset_result ); 
        $offset = $offset_row->offset;
        $sql_quick = "SELECT * FROM 'gallery_view' LIMIT $offset, 5";
        $result = mysqli_query( $link,$sql_quick);
        $basePath = 'http://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], '/'));
        $basePath .= '/DigiSig/images/';
        if(isset($result) && $result!==""){
            while ($row = mysqli_fetch_array($result)) {
                $returnHtml = $returnHtml."<div class='imgFake'>";
                $image = $row['representation_thumbnail'];
                $value16 = $row['connection'];
                $value17 = $row['thumb'];
                $value8 = $row['representation_filename'];
                $value2 = $row['shelfmark'];
                $value9 = $row['name_first'] . " " . $row['name_last'];
                $medium = $row['medium'];
                if($medium!=="local"){
                    $basePath = "";
                }
                else{
                    $medium = "";
                }
                $seal_id = $row['id_seal'];
                $seal_connection = $basePath."/DigiSig/entity/".$seal_id;
                 if (isset($image)) {
//                    if (1 == $row['fk_access']) {
//                        echo '<a href="' . $value16 . $value8 . '" data-lightbox="example-1" data-title="' . $value2 . '"><img src="' . $value17 . $value18 . '" /></a></div>';
//                    } else if (isset($_SESSION['userID']) && ($_SESSION['fk_access'] == $row['fk_access'] || $_SESSION['fk_repository'] == $row['fk_repository'])) {
//                        echo '<a href="' . $value16 . $value8 . '" data-lightbox="example-1" data-title="' . $value2 . '"><img src="' . $value17 . $value18 . '" /></a></div>';
//                    } else {
//                        echo '<img src="' . $default . 'restricted_thumb.jpg"/></div>';
//                    }
                    $returnHtml = $returnHtml. '<a target="_blank" href="' . $seal_connection . '"  data-title="' . $value2 . '"><img src="' . $basePath.$medium.$image . '" /></a></div>';
                }else{
                    $returnHtml = $returnHtml. '<img src="' . $default . 'not_available_thumb.jpg"/></div>';
                }

            }
        }

    }

    //works across all tables, not limited bu tables with unique ids. consider this taking 100% time to run, it is the slowest way to do it. 
    function gather_random_slow(){
        include "config/config.php";
        #constants and default values
        include "include/constants.php";
        #constants and default values
        $sql_slow =  "SELECT * FROM gallery_view WHERE fk_access=1 AND representation_filename IS NOT NULL ORDER BY RAND() LIMIT 5";
        $result = mysqli_query($link,$sql_slow);
        $returnHtml = "";
        $basePath = 'http://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], '/'));
        $basePath .= '/DigiSig/images/';
        if(isset($result) && $result!==""){
            while ($row = mysqli_fetch_array($result)) {
                $returnHtml = $returnHtml."<div class='imgFake'>";
                $image = $row['representation_filename'];
                $image= str_replace("images/","",$image);
                $seal_id = $row['id_seal'];
                if($medium!=="local"){
                    $basePath = "";
                }
                else{
                    $medium = "";
                }
                $seal_connection = $basePath."/DigiSig/entity/".$seal_id;
                $medium = $row['medium'];
                $value16 = $row['connection'];
                $value17 = $row['thumb'];
                $value2 = $row['shelfmark'];
                 if (isset($image) && $image!=="") {
                    $returnHtml = $returnHtml. '<a target="_blank" href="' . $seal_connection . '"  data-title="' . $value2 . '"><img class="galimg" src="'.$basePath .$medium. $image . '" /></a></div>';
                }else{
                    $returnHtml = $returnHtml. '<img src="' . $default . 'not_available_thumb.jpg"/></div>';
                }

            }
        }
        return $returnHtml;
    }
