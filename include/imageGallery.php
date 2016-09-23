<?php

    // Query the database for a random set of images (or design queries for specific sets).  Populate those images into the gallery.  Make sure they are
    // linked so that clicking on them takes you to their seal page. 
    //      -What is a good query for finding an image?
    //      -How do we randomize this?
    //      -How many should we get?
    //      -How big should the images be?
    //      -Should they have a tooltip with more information about them?  If so, what info exactly should be with the image in the gallery?
    //      -John is making an image gallery view table to pull entries from.  This will make it a lot easier to write queries and randomize what it grabbed or set up a schema to grab
    //       certain predifined sets of data. 
    
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
        if(isset($result) && $result!==""){
            while ($row = mysqli_fetch_array($result)) {
                echo "<div class='imgFake'>";
                $image = $row['representation_thumbnail'];
                $value16 = $row['connection'];
                $value17 = $row['thumb'];
                $value8 = $row['representation_filename'];
                $value2 = $row['shelfmark'];
                $value9 = $row['name_first'] . " " . $row['name_last'];
                 if (isset($image)) {
//                    if (1 == $row['fk_access']) {
//                        echo '<a href="' . $value16 . $value8 . '" data-lightbox="example-1" data-title="' . $value2 . '"><img src="' . $value17 . $value18 . '" /></a></div>';
//                    } else if (isset($_SESSION['userID']) && ($_SESSION['fk_access'] == $row['fk_access'] || $_SESSION['fk_repository'] == $row['fk_repository'])) {
//                        echo '<a href="' . $value16 . $value8 . '" data-lightbox="example-1" data-title="' . $value2 . '"><img src="' . $value17 . $value18 . '" /></a></div>';
//                    } else {
//                        echo '<img src="' . $default . 'restricted_thumb.jpg"/></div>';
//                    }
                    echo '<a href="' . $value16 . $value8 . '" data-lightbox="example-1" data-title="' . $value2 . '"><img src="' . $value17 . $value18 . '" /></a></div>';
                }else{
                    echo '<img src="' . $default . 'not_available_thumb.jpg"/></div>';
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
        if(isset($result) && $result!==""){
            while ($row = mysqli_fetch_array($result)) {
                $returnHtml = $returnHtml."<div class='imgFake'>";
                $image = $row['representation_filename'];;
                $value16 = $row['connection'];
                $value17 = $row['thumb'];
                $value8 = 
                $value2 = $row['shelfmark'];
                 if (isset($image) && $image!=="") {
//                    if (1 == $row['fk_access']) {
//                        echo '<a href="' . $value16 . $value8 . '" data-lightbox="example-1" data-title="' . $value2 . '"><img src="' . $value17 . $value18 . '" /></a></div>';
//                    } else if (isset($_SESSION['userID']) && ($_SESSION['fk_access'] == $row['fk_access'] || $_SESSION['fk_repository'] == $row['fk_repository'])) {
//                        echo '<a href="' . $value16 . $value8 . '" data-lightbox="example-1" data-title="' . $value2 . '"><img src="' . $value17 . $value18 . '" /></a></div>';
//                    } else {
//                        echo '<img src="' . $default . 'restricted_thumb.jpg"/></div>';
//                    }
                    $returnHtml = $returnHtml. '<a href="' . $value16 . $value8 . '"  data-title="' . $value2 . '"><img src="' . $image . '" /></a></div>';
                }else{
                    $returnHtml = $returnHtml. '<img src="' . $default . 'not_available_thumb.jpg"/></div>';
                }

            }
        }
        return $returnHtml;
    }
