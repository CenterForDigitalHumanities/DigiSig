<?php $basePath = 'http://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], '/')); ?>
<html>

	<head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <!--<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>-->  
        <!--<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">-->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.8.1/js/lightbox-plus-jquery.min.js"></script>
		<link rel="stylesheet" href="<?php echo $basePath; ?>/digisig/css/digisigSkin.css" />                
                
	</head>
	<body>

		<?php // Version 2: May 2017

        #functions

        #connection details
        include "config/config.php";
        #all activity happens through index.php.  Analytics tracking should catch all the traffic we want it to through the include on this page.
        include "include/analyticstracking.php";
        #constants and default values
        include "include/constants.php";


session_start();
//user log in part
    if(isset($_POST['user_email']) && isset($_POST['password'])){
        $email = $_POST['user_email'];
        $pwd = $_POST['password'];
        $login = "select * from user_digisig where user_email = '".$email."' and password='".$pwd."'";

        $queryresults = mysqli_query($link, $login);
        $count = mysqli_num_rows($queryresults);
        if($count > 0){
            $row = mysqli_fetch_array($queryresults);
            $_SESSION['userID'] = $row['pk_user'];
            $_SESSION['user_email'] = $row['user_email'];
            $_SESSION['fk_access'] = $row['fk_access'];
            $_SESSION['fk_repository'] = $row['fk_repository'];
        }
        else{
            echo 'User email or password error, cannot log in. ';
        }
    }
    
include "header.php"; 
echo "<div class='content_wrap'>";
include "include/page.php";

//user login

        //my functions
        include "include/function.php";
        //functions copied from other people
        include "include/function_parsepath.php";

        $exact = "";
        if (isset($_POST['submit'])) {

            $page = "/" . strtolower($_POST['submit']);
            $address = "/digisig";
            $domain = $field = $index = $term = $exact = "";
            $domain = 'http://' . $_SERVER['HTTP_HOST'];

            if (isset($_POST['field'])) {
                $field = "/" . strtolower($_POST['field']);
            }

            if (isset($_POST['index'])) {
                $index = "/" . strtolower($_POST['index']);
            }

            if (isset($_POST['term'])) {
                $term = "/" . ($_POST['term']);
            }

            if (isset($_POST['exact'])) {
                $exact = "/e";
            }
            $url = ($domain . $address . $page . $field . $index . $term . $exact);
            echo '<script type="text/javascript">
               window.location.href = "'.$url.'";
            </script>';
            die();
            // reload the page with the new header

        }

        // reset the post array to clear any lingering data
        $_POST = array();

        /* If the page has NOT received instructions via 'post'
         * check to see if header contains search instructions
         */

        $path_info = parse_path();
        $new_url = $_SERVER['REQUEST_URI'];
        echo '<script>window.history.pushState("Object", "Title", "'.$new_url.'");</script>';

        if ($path_info['call_parts'][0] == "search") {
            $field = ($path_info['call_parts'][1]);
            $index = ($path_info['call_parts'][2]);
            $term = ($path_info['call_parts'][3]);
            if (count($path_info['call_parts']) > 4) {
                $exact = ($path_info['call_parts'][4]);
            }
            $title = "RESULTS";
        }

        if ($path_info['call_parts'][0] == "entity") {
            $id = ($path_info['call_parts'][1]);
            //find the last digit in the id number because it indicates the type of entity
            $entity = substr($id, -1);
            $title = $id;
        }

        if ($path_info['call_parts'][0] == "gallery") {
            $DIR = "bm_mcm1890";
            if($path_info['call_parts'][1]){
                $DIR = ($path_info['call_parts'][1]);
            };
            // TODO: Maybe make a reference array for the DIR or pull from the query if
            // available so that the $id can be passed around instead.
            $title = $DIR;
            $dirArray = [
                "bm_mcm1890",
                "bm_mcm2062",
                "bm_mcm2064",
                "bm_mcm2212",
                "bm_mcm4973_back",
                "bm_mcm4973_front",
                "bm_mcm5222",
                "bm_mcm5241",
                "bm_mcm5330_back",
                "bm_mcm5330_front",
                "mol_2016_33",
                "mol_84_434",
                "sbh_1019_back",
                "sbh_1019_front",
                "sbh_1090_s2_back",
                "sbh_1090_s2_front",
                "sbh_1094",
                "sbh_1143_back",
                "sbh_1143_front",
                "sbh_1263",
                "sbh_1290",
                "sbh_1337",
                "sbh_1550",
                "sbh_1_s1",
                "sbh_1_s2",
                "sbh_881_back",
                "sbh_881_front",
                "tna_883a",
                "tna_883b",
                "tna_e26_1a_58_back",
                "tna_e26_1a_58_front",
                "tna_e40_11002_s1_back",
                "tna_e40_11002_s1_front",
                "tna_e40_11002_s2",
                "tna_e40_6839",
                "tna_e40_6884_back",
                "tna_e40_6884_front",
                "tna_e42_146_s1",
                "tna_e42_146_s2",
                "tna_e42_543_back",
                "tna_e42_543_front",
                "tna_sc13_h88_back",
                "tna_sc13_h88_front",
                "tna_sc13_i34_back",
                "tna_sc13_i34_front",
                "tna_sc13_i38_back",
                "tna_sc13_i38_front",
                "tna_sc13_k36_back",
                "tna_sc13_k36_front",
                "tna_sc13_q1_back",
                "tna_sc13_q1_front"
            ];
        }

        //Dataset statistics

        $query = "SELECT count(DISTINCT id_seal) as sealcount FROM sealdescription_view";
        $queryresults = mysqli_query($link, $query);
        $row = mysqli_fetch_assoc($queryresults);
        $sealcount = $row['sealcount'];

        $query = "Select COUNT(DISTINCT representation_filename) as imagecount from shelfmark_view";
        $queryresults = mysqli_query($link, $query);
        $row = mysqli_fetch_assoc($queryresults);
        $imagecount = $row['imagecount'];

        /* this file loads the header which is consistent on on all pages
         * It has these parts:
         * 1) Banner / Title
         * 2) Navigation bar
         * 3) Introduction text
         * 4) Basic Search bar
         */

        

        // load the optional extra parts of the page depending on the header

            switch($path_info['call_parts'][0]) {

            case 'search' :
                echo '<div class="pageWrap">';
                //test to see if the search string has more than 1 character
                if (strlen($term) > 0) {
                    $term = str_replace("_", "/", $term);
                    // if someone searches 'all fields' run the query for all possible searches
                    // otherwise, just run the query on the specified field
                    if ($field == "all_fields") {
                        $query12 = "SELECT field_url FROM field";
                        $query12result = mysqli_query($link, $query12);
                        while ($row = mysqli_fetch_array($query12result)) {
                            $searchfield = $row['field_url'];
                            queryResult($searchfield, $index, $term, $address, $exact, 0, $num_result_per_page);
                        }
                    } else {
                        queryResult($field, $index, $term, $address, $exact, 0, $num_result_per_page);
                    }
                }
                echo "</div>"; //close page wrap
                break;

            case 'entity' :
                echo '<div class="pageWrap">';
                # show information about a specific entity

                // first test that we have an entity number and proceed if yes
                if ($id > 0) {
                    # 1) determine what view to query using the entity number
                    $query6 = "SELECT * FROM entity WHERE entity_code = $entity";
                    $query6result = mysqli_query($link, $query6);
                    $row = mysqli_fetch_object($query6result);
                    $count = mysqli_num_rows($query6result);
                    if (isset($row) && $row != null) {
                        $column = $row -> entity_column;
                        $view = $row -> entity_view;

                        # 2) formulate and return the basic search string
                        $query8 = "SELECT * FROM $view WHERE $column = $id";
                        $query8result = mysqli_query($link, $query8);

                        //start rowcounter for table output
                        $rowcount = 1;

                        #the format for each version of the output depends on the nature of the data

                        //for shelfmarks
                        If ($entity == 0) {
                            $row = mysqli_fetch_array($query8result);
                            $value1 = $row['repository_fulltitle'];
                            $value2 = $row['shelfmark'];
                            $value10 = $row['repository_startdate'];
                            $value11 = $row['repository_enddate'];
                            $value12 = $row['repository_location'];
                            $value13 = $row['repository_description'];
                            $value14 = $row['connection'];
                            $value15 = $row['ui_event_repository'];
                            if(isset($value11) && $value11!=="" && isset($value10) && $value10!==""){
                                $outputDate1 = date_create($value10);
                                $outputDate2 = date_create($value11);
                            }

                            

                                    
                            
                            //echo "ITEM";
                            echo '<div class="seal sealPiece sealHeader">
                                <span class="sealLabel hdr">ITEM <div class="icon_item"></div><div class="icon_info"></div></span>
                                <span class="sealLabel">Digisig ID: </span><span id="digisigID">' .$id.'</span>
                                <span clss="sealLabel">Permalink: </span><span id="permalink">http://digisig.org/entity/'. $id .'</span>
                                <input class="digiBtn" type="button" value="Copy Link" onclick="linkToClipboard();" />
                            </div>
                            ';

                            //echo "<br><br>" . $value1 . ": " . $value2;
                            //all the other values listed under shelfmark are optional
                            
                            if($count < 5){
                                echo '<div class="theCards_body">';
                                echo '<div class="card_single">';
                                echo '<div class="cardInfo"><span class="cardInfoKey">Title: </span><span class="cardInfoVal">'.$value1.':'.$value2.'</span></div>';
                                
                                if(isset($value11) && $value11!=="" && isset($value10) && $value10!==""){
                                    echo '<div class="cardInfo"><span class="cardInfoKey">Dated: </span> <span class="cardInfoVal">' . date_format($outputDate1, 'Y') . ' to ' . date_format($outputDate2, 'Y').'</span></div>';
                                }
                                if(isset($value13) && $value10!==""){
                                    echo '<div class="cardInfo"><span class="cardInfoKey">Description: </span> <span class="cardInfoVal">'.$value13.'</span></div>';
                                }
                                if(isset($value12) && $value12!==""){
                                    echo '<div class="cardInfo"><span class="cardInfoKey">Location: </span> <span class="cardInfoVal">'.$value12.'</span></div>';
                                }
                                if(isset($value14) && $value14!=="" && isset($value15) && $value15!==""){
                                    echo '<div class="cardInfo"><span class="cardInfoKey">External Link: </span> <span class="cardInfoVal"><a href="'.$value14.$value15.'" target="_blank">'.$value14.$value15.'</a></span></div>';
                                }
                                echo '</div></div>';
                            }
                            else{
                                if(isset($value11) && $value11!=="" && isset($value10) && $value10!==""){
                                    $dateTD = '<td>' . date_format($outputDate1, 'Y') . ' to ' . date_format($outputDate2, 'Y').'</td>';
                                }
                                else{
                                    $dateTD = '<td></td>';
                                }
                                echo '<div class="tableWrap"><table class="metaTable"><thead><th>Dated</th><th>Description</th><th>Location</th><th>External Link</th></thead>'
                                . '<tbody><tr>'
                                . $dateTD.'<td>'.$value13.'</td><td>'.$value12.'</td><td><a href="'.$value14.$value15.'" target="_blank">'.$value14.$value15.'</a></td></tr></tbody></table></div>';
                            }                           

                            //show table of associated impressions
                            $query12 = "SELECT * FROM shelfmark_view WHERE id_item = $id ORDER BY position_latin";
                            $query12result = mysqli_query($link, $query12);
                            $count3 = mysqli_num_rows($query12result);
                            // table detailing which seal impressions are associated with this item
                            echo "<div class='data_wrapper'>";
                            $addAsCard = "<input type='checkbox' onchange='cardMe($(this), false, false);' />";
                            echo "<div class='separator_2'>Impressions/Casts/Matrices <div class='icon_impression'></div><div class='icon_info'></div></div>";
                            if($count3 < 5){
                                $addAsCard = "";
                                echo "<div class='theCards_body indent'>";
                            }
                            else{
                                echo '<div class="tableWrap"><table class="metaTable indent2">'
                                . '<thead><th>&#x2714;</th><th>#</th><th>Nature</th><th>Number</th><th>Position</th><th>Shape</th><th>Seal Link</th><th>Thumbnail</th></thead>'
                                . ''; //'<tr><td></td><td>nature</td><td>number</td><td>position</td><td>shape</td></tr>'
                            }
                            while ($row = mysqli_fetch_array($query12result)) {
                                $value3 = $row['nature'];
                                $value4 = "";
                                if (isset($row['number']) && $row['number'] != null) {
                                    $value4 = $row['number'];
                                }
                                $value5 = $row['position_latin'];
                                $value6 = "";
                                if (isset($row['shape']) && $row['shape'] != null) {
                                    $value6 = $row['shape'];
                                }
                                $value7 = $row['id_seal'];
                                $value8 = $row['representation_filename'];
                                $value9 = $row['name_first'] . " " . $row['name_last'];
                                $value16 = $row['connection'];
                                $value17 = $row['thumb'];
                                $value18 = $row['representation_thumbnail'];
                                $value19 = $row['medium'];
                                //test to see if the connection string indicates that it is in the local image store
                                if($value19=="local" || $value19==null || $value19==""){
                                    $value19 = "../images/medium/";
                                }
                                if ($value17 == "local" || $value17==null || $value17=="") {
                                    $value17 = $small;
                                    //$value14 = $medium;
                                }
                                if($count3 < 5){
                                    echo '<div class="card"><label><input type="checkbox" onchange="cardMe($(this), false, true);"/> Add To Folder </label>';
                                    echo '<div class="cardNum">#'.$addAsCard . $rowcount . '</div>';
                                    if(isset($value3) && $value3!==""){
                                        echo '<div class="cardInfo"><span class="cardInfoKey">Nature: </span> <span class="cardInfoVal">'.$value3. '</span></div>';
                                    }
                                    if(isset($value4) && $value4!==""){
                                         echo '<div class="cardInfo"><span class="cardInfoKey">Number: </span> <span class="cardInfoVal">'.$value4. '</span></div>';
                                    }
                                    if(isset($value5) && $value5!==""){
                                        echo '<div class="cardInfo"><span class="cardInfoKey">Position: </span> <span class="cardInfoVal">'.$value5. '</span></div>';
                                    }
                                    if(isset($value6) && $value6!==""){
                                        echo '<div class="cardInfo"><span class="cardInfoKey">Shape: </span> <span class="cardInfoVal">'.$value6. '</span></div>';
                                    }
                                    //BH FIXME why is entity in the URL instead of medium? 4/27/17
                                    
                                    if (isset($value18)) {
                                        if (1 == $row['fk_access']) {
                                            echo '<div class="cardInfo"><span class="cardInfoKey"></span>'
                                            . '<span class="cardInfoVal "><a href="' . $value19 . $value8 . '" data-lightbox="example-1" data-title="' . $value2 . '<br>photo: ' . $value9 . '"><img class="limitImgSize" src="' . $value19 . $value18 . '" /></a></span></div>';
                                        } else if (isset($_SESSION['userID']) && ($_SESSION['fk_access'] == $row['fk_access'] || $_SESSION['fk_repository'] == $row['fk_repository'])) {
                                            echo '<div class="cardInfo"><span class="cardInfoKey"></span>'
                                            . '<span class="cardInfoVal"><a href="' . $value19 . $value8 . '" data-lightbox="example-1" data-title="' . $value2 . '<br>photo: ' . $value9 . '"><img class="limitImgSize" src="' . $value19 . $value18 . '" /></a></span></div>';
                                        } else {
                                            echo '<div class="cardInfo"><span class="cardInfoKey"></span>'
                                            . '<span class="cardInfoVal"><img src="' . $default . 'restricted_thumb.jpg"/></span></div>';
                                        }
                                    }else{
                                        echo '<div class="cardInfo"><span class="cardInfoKey"></span>'
                                            . '<span class="cardInfoVal"><img src="' . $default . 'no_image_thumb.jpg"/></span></div>';
                                    }
                                    echo '<div class="cardInfo"><span class="cardInfoKey">Seal Link: </span><span class="cardInfoVal"><a href="' . $address . '/entity/' . $value7 . '">view seal entry</a></span></div>';
                                    echo "</div>";
                                }
                                else{
                                    echo '<tr><td>'.$addAsCard .'</td><td>'. $rowcount . '</td>';
                                    echo '<td>' . $value3 . '</td>';
                                    echo '<td>' . $value4 . '</td>';
                                    echo '<td>' . $value5 . '</td>';
                                    echo '<td>' . $value6 . '</td>';
                                    echo '<td><a href="' . $address . '/entity/' . $value7 . '">view seal entry</a></td>';
                                    If (isset($value18)) {
                                        if (1 == $row['fk_access']) {
                                            echo '<td><a href="' . $value19 . $value8 . '" data-lightbox="example-1" data-title="' . $value2 . '<br>photo: ' . $value9 . '"><img class="limitImgSize" src="' . $value19 . $value18 . '" /></a></td></tr>';
                                        } else if (isset($_SESSION['userID']) && ($_SESSION['fk_access'] == $row['fk_access'] || $_SESSION['fk_repository'] == $row['fk_repository'])) {
                                            echo '<td><a href="' . $value19 . $value8 . '" data-lightbox="example-1" data-title="' . $value2 . '<br>photo: ' . $value9 . '"><img class="limitImgSize" src="' . $value19 . $value18 . '" /></a></td></tr>';
                                        } else {
                                            echo '<td><img src="' . $default . 'restricted_thumb.jpg"/></td></tr>';
                                        }
                                    }else{
                                        echo '<td><img src="' . $default . 'no_image_thumb.jpg"/></td></tr>';
                                    }
                                }

                                $rowcount++;
                            }
                            if($count3 < 5){
                                echo '</div></div>';
                            }
                            else{
                                echo '</tbody></table></div></div>';
                            }
                            
                        }

                        //for seal descriptions
                        If ($entity == 3) {
                            $row = mysqli_fetch_array($query8result);
                            $count = mysqli_num_rows($query8result);
                            //assign variables
                            //$value1 = $row['a_index'];
                            $value1 = $row['collection_fulltitle'];
                            $value2 = $row['collection_volume'];
                            $value3 = $row['catalogue_pagenumber'];
                            $value4 = $row['sealdescription_identifier'];
                            $value5 = $row['realizer'];
                            $value6 = $row['motif_obverse'];
                            if (isset($row['motif_reverse'])) {
                                $value6 = "obverse: " . $value6 . "<br>reverse: " . $row['motif_reverse'];
                            }

                            $value7 = $row['legend_obverse'];
                            if (isset($row['legend_reverse'])) {
                                $value6 = "obverse: " . $value6 . "<br>reverse: " . $row['legend_reverse'];
                            }

                            $value8 = $row['shape'];
                            $value9 = $row['sealsize_vertical'];
                            $value10 = $row['sealsize_horizontal'];
                            $value11 = $row['id_seal'];
                            $value12 = $row['representation_filename'];
                            $value13 = $row['ui_catalogue'];
                            $value14 = $row['connection'];
                            $value15 = $row['sealdescription'];
                            //formulate header
                             echo '<div class="seal sealPiece sealHeader">
                                <span class="sealLabel hdr">Description <div class="icon_descr"></div></span>
                                <span class="sealLabel">Digisig ID: </span><span id="digisigID">' .$id.'</span>
                                <span clss="sealLabel">Permalink: </span><span id="permalink">http://digisig.org/entity/'. $id .'</span>
                                <input class="digiBtn" type="button" value="Copy Link" onclick="linkToClipboard();" />
                             </div>                        
                            ';
                                $cardArea = "<div class='theCards_body'><div class='card_single'>";
                                $cardArea .= '<div class="cardInfo"><span class="cardInfoKey">Title: </span><span id="title" class="cardInfoVal">'.$value1.' : '.$value4.'</span></div>';
                                $tableHeader = "<thead>";
                                $tableBody = "<tbody><tr>";
                                $tableHeader .= "<th>Title</th>";
                                $tableBody .= "<td>".$value1." : ".$value4."</td>";

                            // title
                            //echo $value1 . ":" . $value4;
                            //$tableBody .= "<td>".$value1.":".$value4."</td>";
                            if (isset($value2)) {
                                $tableHeader .= "<th>Volume</th>";
                                $tableBody .= "<td>".$value2."</td>";
                                $cardArea .= '<div class="cardInfo"><span class="cardInfoKey">Volume: </span> <span class="cardInfoVal">'.$value2.'</span></div>';
                                //echo ", vol." . $value2;
                            }
                            if (isset($value3)) {
                                if (strpos($value3, '-') !== false) {
                                    $tableHeader .= "<th>Pages</th>";
                                    $tableBody .= "<td>".$value3."</td>";
                                    $cardArea .= '<div class="cardInfo"><span class="cardInfoKey">Pages: </span> <span class="cardInfoVal">'.$value3.'</span></div>';
                                    //echo ", p." . $value3;
                                } else {
                                    $tableHeader .= "<th>Page</th>";
                                    $tableBody .= "<td>".$value3."</td>";
                                    $cardArea .= '<div class="cardInfo"><span class="cardInfoKey">Page: </span> <span class="cardInfoVal">'.$value3.'</span></div>';
                                    //echo ", pp." . $value3;
                                }
                            }
            
                            //output entry -- only output variables with values

                            if (empty($value12)) {
                            
                            if (isset($value5)) {
                                $tableHeader .= "<th>Name</th>";
                                $tableBody .= "<td>".$value5."</td>";
                                $cardArea .= '<div class="cardInfo"><span class="cardInfoKey">Name: </span> <span class="cardInfoVal">'.$value5.'</span></div>';
                                //echo '<br><br> Name:' . $value5 . '<br>';
                            }

                            if (isset($value15)) {
                                $tableHeader .= "<th>Description</th>";
                                $tableBody .= "<td>".$value15."</td>";
                                $cardArea .= '<div class="cardInfo"><span class="cardInfoKey">Description: </span> <span class="cardInfoVal">'.$value15.'</span></div>';
                                //echo '<br><br> Name:' . $value15 . '<br>';
                            }
                            
                            if (isset($value6)) {
                                $tableHeader .= "<th>Motif</th>";
                                $tableBody .= "<td>".$value6."</td>";
                                $cardArea .= '<div class="cardInfo"><span class="cardInfoKey">Motif: </span> <span class="cardInfoVal">'.$value6.'</span></div>';
                                //echo '<br> Motif:' . $value6 . '<br>';
                            }

                            if (isset($value7)) {
                                $tableHeader .= "<th>Legend</th>";
                                $tableBody .= "<td>".$value7."</td>";
                                $cardArea .= '<div class="cardInfo"><span class="cardInfoKey">Legend: </span> <span class="cardInfoVal">'.$value7.'</span></div>';
                                //echo '<br> Legend:' . $value7 . '<br>';
                            }

                            if (isset($value8)) {
                                $tableHeader .= "<th>Shape</th>";
                                $tableBody .= "<td>".$value8."</td>";
                                $cardArea .= '<div class="cardInfo"><span class="cardInfoKey">Shape: </span> <span class="cardInfoVal">'.$value8.'</span></div>';
                                //echo '<br> Shape:' . $value8 . '<br>';
                            }

                            if (isset($value9)) {
                                $tableHeader .= "<th>Height</th>";
                                $tableBody .= "<td>".$value9."</td>";
                                $cardArea .= '<div class="cardInfo"><span class="cardInfoKey">Height: </span> <span class="cardInfoVal">'.$value9.'</span></div>';
                                //echo '<br> Size Vertical:' . $value9 . '<br>';
                            }

                            if (isset($value10)) {
                                $tableHeader .= "<th>Width</th>";
                                $tableBody .= "<td>".$value10."</td>";
                                $cardArea .= '<div class="cardInfo"><span class="cardInfoKey">Width: </span> <span class="cardInfoVal">'.$value10.'</span></div>';
                                //echo '<br> Size Horizontal:' . $value10 . '<br>';
                            }
/*
                            if (isset($value13)) {
                                $tableHeader .= "<th>External Link</th>";
                                $tableBody .= "<td><a href='" . $value14 . $value13 . "' target='_blank'>" . $value14 . $value13 . "</a></td>";
                                $cardArea .= '<div class="cardInfo"><span class="cardInfoKey">External Link: </span> <span class="cardInfoVal"><a href="' . $value14 . $value13 . '" target="_blank">' . $value14 . $value13 . '</a></span></div>';
                                //echo '<a href="' . $value14 . $value13 . '" target="_blank">external link</a>';
								}
*/
								}
                            //prepare the photograph -- if it is available
                            if (isset($value12)) {
                                $tableHeader .= "<th>Image</th>";
                                
                                if (1 == $row['fk_access']) {
                                    $tableBody .= '<td><img class="sealThumbnail" src="' . $description . $value12 . '"/>'
                                    . '<input class="digiBtn viewImgBtn" type="button" value="View Image" onclick="viewFullImage($(this));"/></td>';
                                    $cardArea .= '<div class="cardInfo"><span class="cardInfoKey">Image: </span> <span class="cardInfoVal">'
                                            . '<img class="sealThumbnail" src="' . $description . $value12 . '"/><input class="digiBtn viewImgBtn" type="button" value="View Image" onclick="viewFullImage($(this));"/></span></div>';
                                   
                                    //echo '<a href="' . $description . $value12 . '" data-lightbox="example-1" data-title=""><img src="' . $description . $value12 . '" height=200></img></a><br>';
                                } else if (isset($_SESSION['userID']) && ($_SESSION['fk_access'] == $row['fk_access'] || $_SESSION['fk_repository'] == $row['fk_repository'])) {
                                    $tableBody.= '<td><img class="sealThumbnail" src="' . $description . $value12 . '" height="200"/>'
                                    . '<input class="digiBtn viewImgBtn" type="button" value="View Image" onclick="viewFullImage($(this));"/></td>';
                                    $cardArea .= '<div class="cardInfo"><span class="cardInfoKey">Image: </span> <span class="cardInfoVal"><img class="sealThumbnail" src="' . $description . $value12 . '" height="200"/>'
                                    . '<input class="digiBtn viewImgBtn" type="button" value="View Image" onclick="viewFullImage($(this));"/></span></div>';
                                   // echo '<a href="' . $description . $value12 . '" data-lightbox="example-1" data-title=""><img src="' . $description . $value12 . '" height=200></img></a><br>';
                                } else {
                                    $tableBody .= '<td><img class="sealThumbnail" src="' . $default . 'restricted_thumb.jpg" height=50/>'
                                    . '<input class="digiBtn viewImgBtn" type="button" value="View Image" onclick="viewFullImage($(this));"/></td>';
                                    $cardArea .= '<div class="cardInfo"><span class="cardInfoKey">Width: </span> <span class="cardInfoVal"><img class="sealThumbnail" src="' . $default . 'restricted_thumb.jpg" height=50/>'
                                    . '<input class="digiBtn viewImgBtn" type="button" value="View Image" onclick="viewFullImage($(this));"/></span></div>';
                                    //echo '<td><a href="' . $default . 'restricted.jpg"><img src="' . $default . 'restricted_thumb.jpg" height=50></img></a></td></tr>';
                                }
                            }
							//JM I moved these lines down here to force display of link regardless of whether or not there is an photo available
							if (isset($value13)) {
                                $tableHeader .= "<th>External Link</th>";
                                $tableBody .= "<td><a href='" . $value14 . $value13 . "' target='_blank'>" . $value14 . $value13 . "</a></td>";
                                $cardArea .= '<div class="cardInfo"><span class="cardInfoKey">External Link: </span> <span class="cardInfoVal"><a href="' . $value14 . $value13 . '" target="_blank">' . $value14 . $value13 . '</a></span></div>';
                                //echo '<a href="' . $value14 . $value13 . '" target="_blank">external link</a>';
                            }
                            //link to seal page
                            $tableHeader .= "<th>Seal Link</th></thead>";
                            $tableBody .= "<td><a href='". $address ."/entity/". $value11."'>view seal entry</a></td></tr></tbody>";
                            //$cardArea .= '<div class="cardInfo"><span class="cardInfoKey">Seal Link: </span> <span class="cardInfoVal"><a href="'. $address ."/entity/". $value11.'">view seal entry</a></span></div>';
                            if($count < 5){
                                echo $cardArea."</div><span class='gotoEntry'><a href='". $address .'/entity/'. $value11."'>view seal entry</a></span></div>";
                            }
                            else{
                                echo "<div class='tableWrap'><table>".$tableHeader.$tableBody."</table></div>";
                            }
                            
                            //echo '<br><a href=' . $address . '/entity/' . $value11 . '>view seal entry</a><br>';

                            //check for other seal descriptions

                            if(isset($value11) && '' != $value11){
                                $query12 = "SELECT * FROM sealdescription_view WHERE id_seal = $value11";
                                $query12result = mysqli_query($link, $query12);
    
                                $count = mysqli_num_rows($query12result);
                                if ($count > 1) {
                                    echo "<div class='data_wrapper'>";
                                    echo "<div class='separator_2'>Other Descriptions <div class='icon_descr'></div><div class='icon_info'></div></div>";
                                    $duplicate = $id;
                                    sealdescription($query12result, $address, $duplicate);
                                }
                            }
                        }

                        //for a seal
                        If ($entity == 1) {

                            echo '<div class="sealPiece sealHeader seal">
                            <span class="sealLabel hdr">SEAL <div class="icon_seal"></div> <div class="icon_info"></div></span>
                            <span class="sealLabel">Digisig ID: </span><span id="digisigID">' .$id.'</span>
                            <span clss="sealLabel">Permalink: </span><span id="permalink">http://digisig.org/entity/'. $id .'</span>
                            <input class="digiBtn" type="button" value="Copy Link" onclick="linkToClipboard();" />
                            </div>';
            //perhaps this could be a card?
                            $shapeDims = '<div id="sealDims" class="sealPiece nobot">';
                            

                            // note that a seal can have two faces but I am going to assume that the double side ones are the same
                            $row = mysqli_fetch_array($query8result);
                            $value3 = $row['shape'];
                            $value4 = $row['face_vertical'];
                            $value5 = $row['face_horizontal'];
                            if(isset($value3) && $value3!==""){
                                $shapeDims .= "<span class='sealLabel'>Shape: </span><span id='shape'>$value3";
                                //$shapeDims .= "<span class='sealLabel'>Shape: </span><span id='shape'>$value3</span>";
                            }
                            if(isset($value4) && $value4!==""){
                                {
                                $shapeDims .= ", $value4";
                                //    $shapeDims .= "<span class='sealLabel'>Height: </span><span id='height'>$value4</span>";
                                }
                                if(isset($value5) && $value5!==""){
                                    $shapeDims .= " x $value5";
                                    //$shapeDims .= "<span class='sealLabel'>Width: </span><span id='width'>$value5</span>";
                                }
                                $shapeDims .= " mm";
                            }
                            $shapeDims.= "</span> <div class='icon_shape'></div></div>";
                            //$shapeDims.= "</div>";
                            echo $shapeDims;
                            $id_seal = $row['id_seal'];
                            // call seal description function to make list of associated seal descriptions

                            $query12 = "SELECT * FROM sealdescription_view WHERE id_seal = $id";
                            $query12result = mysqli_query($link, $query12);
                            $count1 = mysqli_num_rows($query12result);
                            $duplicate = $id;
                            
                            if ($count1 > 0) {
                                echo "<div class='data_wrapper white'>";
                                echo "<div class='separator_2' style='background-color:white;'>Descriptions <div class='icon_descr'></div><div class='icon_info'></div></div>";
                                $duplicate = $id;
                                sealdescription($query12result, $address, $duplicate);
                            }

                            // list of associated seal impressions
                            $query10 = "SELECT * FROM shelfmark_view WHERE id_seal = $id";
                            $query10result = mysqli_query($link, $query10);
                            $count2 = mysqli_num_rows($query10result);
                            echo "<div class='data_wrapper'>";
                            echo '<div class="separator_2">Impression/Matrix/Cast <div class="icon_impression"></div><div class="icon_info"></div></div>';
                            
                            $rowcount = 1;
                            $addAsCard = "<input type='checkbox' onchange='cardMe($(this), false, false);' />";
                            if($count2 < 5){
                                $addAsCard = "";
                                echo '<div class="theCards_body indent3">';
                            }
                            else{
                                echo '<div class="tableWrap"><table class="metaTable indent2"><thead><th>&#x2714;</th><th>#</th><th>Nature</th><th>Position</th><th>Dated</th><th>Thumbnail</th><th>Component of item</th></thead>'
                                . '<tbody>';
                            }
                            while ($row = mysqli_fetch_array($query10result)) {

                                $value1 = $row['nature'];
                                $value2 = "";
                                if (isset($row['number']) && $row['number'] != null) {
                                    $value2 = $row['number'];
                                }
                                $value3 = $row['position_latin'];
                                $value4 = "";
                                if (isset($row['shape']) && $row['shape'] != null) {
                                    $value4 = $row['shape'];
                                }
                                $value5 = $row['shelfmark'];
                                $value6 = $row['id_item'];
                                $value7 = $row['representation_filename'];
                                $value8 = $row['name_first'] . " " . $row['name_last'];
                                $value9 = $row['repository_startdate'];
                                $value10 = $row['repository_enddate'];
                                $value12 = $row['thumb'];
                                $value13 = $row['representation_thumbnail'];
                                $value14 = $row['medium'];
                                //test to see if the connection string indicates that it is in the local image store
                                if($value14=="local" || $value14==null || $value14==""){
                                    $value14 = "../images/medium/";
                                }
                                if ($value12 == "local" || $value12==null || $value12=="") {
                                    $value12 = $small;
                                    //$value14 = $medium;
                                }
                                if($count2 < 5){
                                    echo '<div class="card"> <label><input type="checkbox" onchange="cardMe($(this), false, true);"/> Add To Folder </label>';
                                    echo '<div class="cardNum">#'.$addAsCard . $rowcount . '</div>';
                                    echo '<div class="icon_card"></div>';
                                    if(isset($value1) && $value1!==""){
                                        echo '<div class="cardInfo"><span class="cardInfoKey">Nature: </span> <span class="cardInfoVal">'.$value1.'</span></div>';
                                    }
                                    if(isset($value2) && $value2!==""){
                                        echo '<div class="cardInfo"><span class="cardInfoKey">Number: </span> <span class="cardInfoVal">'.$value2.'</span></div>';
                                    }
                                    if(isset($value3) && $value3!==""){
                                        echo '<div class="cardInfo"><span class="cardInfoKey">Position: </span> <span class="cardInfoVal">'.$value3.'</span></div>';
                                    }
                                    if(isset($value4) && $value4!==""){
                                        echo '<div class="cardInfo"><span class="cardInfoKey">Shape: </span> <span class="cardInfoVal">'.$value4.'</span></div>';
                                    }
                                    if(isset($value9) && $value9!=="" && isset($value10) && $value10!==""){
                                        $outputDate1 = date_create($value9);
                                        $outputDate2 = date_create($value10);
                                       echo '<div class="cardInfo"><span class="cardInfoKey">Dated: </span> <span class="cardInfoVal"> ' . date_format($outputDate1, 'Y') . ' to ' . date_format($outputDate2, 'Y') .'</span></div>';
                                    }
                                    
//                                    echo '<div class="cardInfo"><span class="cardInfoKey">Shape: </span> <span class="cardInfoVal">'.$value4.'</span></div>';
                                    
                                    //BH FIXME here, where is entity instead of medium in the URL? 4-27-17
                                    
                                    if (isset($value13)) {

                                        if (1 == $row['fk_access']) {
											#JM fixed the href references so they work now for external photographs
                                            echo '<div class="cardInfo"><span class="cardInfoKey"></span><span class="cardInfoVal"> <a href="' . $value14 . $value7 . '" data-lightbox="example-1" data-title="' . $value5 . '<br>photo: ' . $value8 . '"><img src="' . $value12 . $value13 . '" height=50></img></a></span></div>';
                                        } else if (isset($_SESSION['userID']) && ($_SESSION['fk_access'] == $row['fk_access'] || $_SESSION['fk_repository'] == $row['fk_repository'])) {
                                            echo '<div class="cardInfo"><span class="cardInfoKey"></span><span class="cardInfoVal"><a href="' . $value14 . $value7 . '" data-lightbox="example-1" data-title="' . $value5 . '<br>photo: ' . $value8 . '"><img src="' . $value12 . $value13 . '" height=50></img></a></span></div>';
                                        } else {
                                            echo '<td><img src="' . $default . 'restricted_thumb.jpg" height=50></img></td>';
                                        }

                                    }else{
                                        echo '<div class="cardInfo"><span class="cardInfoKey"></span><span class="cardInfoVal"><img src="' . $default . 'no_image_thumb.jpg" height=50></img></span></div>';
                                    }
                                    echo '<div class="cardInfo"><span class="cardInfoKey">Component of item: </span> <span class="cardInfoVal"><a href=' . $address . '/entity/' . $value6 . '>' . $value5 . '</a></span></div>';
                                    echo "</div>";
                                }
                                else{
                                    echo '<tr><td>'.$addAsCard.'</td><td>'. $rowcount . '</td>';
                                    echo '<td>' . $value1 . '</td>';
                                    //echo '<td>' . $value2 . '</td>';
                                    echo '<td>' . $value3 . '</td>';
                                    //echo '<td>' . $value4 . '</td>';
                                    if(isset($value9) && $value9!=="" && isset($value10) && $value10!==""){
                                        $outputDate1 = date_create($value9);
                                        $outputDate2 = date_create($value10);
                                        echo '<td> ' . date_format($outputDate1, 'Y') . ' to ' . date_format($outputDate2, 'Y').'</td>';
                                    }
                                    else{
                                        echo '<td> </td>';
                                    }
                                    if (isset($value13)) {

                                        if (1 == $row['fk_access']) {
                                            echo '<td><a href="' . $value14 . $value7 . '" data-lightbox="example-1" data-title="' . $value5 . '<br>photo: ' . $value8 . '"><img src="' . $value12 . $value13 . '" height=50></img></a></td>';
                                        } else if (isset($_SESSION['userID']) && ($_SESSION['fk_access'] == $row['fk_access'] || $_SESSION['fk_repository'] == $row['fk_repository'])) {
                                            echo '<td><a href="' . $value14 . $value7 . '" data-lightbox="example-1" data-title="' . $value5 . '<br>photo: ' . $value8 . '"><img src="' . $value12 . $value13 . '" height=50></img></a></td>';
                                        } else {
                                            #echo '<td><img src="' . $default . 'restricted_thumb.jpg" height=50></img></td>';
                                            echo '<td><a href="' . $default . 'restricted.jpg" data-lightbox="example-1" data-title="' . $value5 . '<br>photo: ' . $value8 . '"> <img src="' . $default . 'restricted_thumb.jpg" height=50></img></td>';
                                        }

                                    }else{
                                        echo '<td><img src="' . $default . 'no_image_thumb.jpg" height=50></img></td>';
                                    }
                                    echo '<td><a href=' . $address . '/entity/' . $value6 . '>' . $value5 . '</a></td>';
                                    echo '</tr>';
                                    
                                }
                                $rowcount++;
                            }
                            if($count2<5){
                                echo "</div></div>";
                            }
                            else{
                                echo "</tbody></table></div></div>";
                            }
                        }
                    }else{
                        echo "No Data Found...";
                    }
                }
                echo "</div>"; //close page wrap
                break;

            case 'gallery' :
            echo '<div class="pageWrap">
                <script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
                <script type="text/javascript" src="js/jquery-ui.min.js"></script>
                <script type="text/javascript" src="js/pep.min.js"></script>
                <script type="text/javascript" src="spidergl/spidergl.js"></script>
                <script type="text/javascript" src="spidergl/multires.js"></script>
                
                <div style="display: flex; align-items: center; justify-content: center;">
                    <div id=toolbar style="margin:.5rem;float:left;width:2rem;height:400px;">
                        <button class="toolbarButton" id="zoomIn" touch-action="none">
                            <img src="css/icons/zoomin.png" alt="zoom in">
                        </button>
                        <button class="toolbarButton" id="zoomOut" touch-action="none">
                            <img src="css/icons/zoomout.png" alt="zoom out">
                        </button>
                        <!-- <button class="toolbarButton" id="light" touch-action="none">
                            <img src="css/icons/light.png" alt="light">
                        </button> -->
                        <button class="toolbarButton" id="fullscreen" touch-action="none">
                            <img src="css/icons/full.png" alt="fullscreen">
                        </button>
                        <button class="toolbarButton" id="help" touch-action="none">
                                <img src="css/icons/help.png" alt="help">
                                </button>
                        <button role=button id="flip" onclick="toggleFlip(this);" style="transform: rotate(-90deg);bottom: -2rem;position: relative;height: 2.25rem;right: 1.5rem;width: 5rem;">flip</button>
                    </div>
                    <div id="viewerCont">
            
                    </div>
                </div>
                <script type="text/javascript">
                    var viewerCont = "viewerCont";
                    function launchRTI() {
                        let params = window.location.search;
                        let dindex = params.indexOf("directory=") + 10;
                        let windex = params.indexOf("width=") + 6;
                        let hindex = params.indexOf("height=") + 7;
                        let dir = params.substring(dindex).split("&")[0] || "sample1";
                        let w = params.substring(windex).split("&")[0] || 900;
                        let h = params.substring(hindex).split("&")[0] || 600;
                        $("#canvas-width").val(w).change();
                        $("#canvas-height").val(h).change();
                        // var opts = {
                            // linkNode: "footer",
                            // linkNodeStyle: {},
                            // toolbarNode: "toolbar",
                            // toolbarStyle: {
                            // 	margin: ".5rem",
                            // 	cssFloat: "left",
                            // 	width: "2rem",
                            // 	height: "400px"
                            // }
                            // externalToolbar: true
                        // }
                        createRtiViewer(viewerCont, "rti/" + dir, w, h, opts);
                    }
                    launchRTI();
                    function toggleFlip(el) {
                        let isFlipped = el.innerHTML.indexOf("unflip") > -1;
                        if (!isFlipped) {
                            $("#" + viewerCont).css("transform", "scaleX(-1)");
                            $(el).text("unflip");
                        } else {
                            $("#" + viewerCont).css("transform", "");
                            $(el).text("flip");
                        }
                    }
                </script>
                <div style="display:flex;flex-wrap:wrap;">';

                foreach($dirArray as $dir){
                    echo '<div class="cardInfo"><a href="gallery/'+$dir+'">'+$dir+'</a></div>';
                }
                
                echo '</div>
            </div>';
                break;
            case 'about' :
            echo '<div class="pageWrap">';
                {
                    echo "<br>
                    <div class='aboutHeader'>The Digitial Sigillography Resource</div><br>
                    <i>Sigillography</i><br><br>
                    Hundreds of thousands of seals survive from medieval Europe, and they provide unique and
                    important information. A seal is 'a mark of authority or ownership, pressed in relief upon a plastic
                    material by the impact of a matrix or die-engraved intaglio'(1). Men and women from all levels of
                    society used seals to authenticate documents, but also to make statements about their family
                    connections, social aspirations and personal values. Seals incorporate both text and images so they
                    are powerful tools of expression. In a period starved of evidence concerning the individual, seals
                    offer insight into identity, and expose regional and local cultural variations. The advent of digital
                    technology offers an unprecedented and exciting opportunity to harness the extraordinary potential
                    of this unique historical resource. Today medieval seals are preserved in archives and museums across the British Isles where they are
                    often prominently and proudly displayed as iconic monuments of artistic and cultural heritage.
                    However, they remain poorly understood because there is no central place where researchers and
                    members of the general public can turn for information. This is partly because much of the
                    information is trapped in outdated and unstandardized formats. Many institutions began
                    cataloguing their collections in the nineteenth and twentieth centuries, well before the advent of
                    electronic data management systems. The result is that we now have information in a wide variety
                    of formats ranging from card indexes, to printed catalogues, to electronic databases. Scholars have long argued that to realize the full potential of sigillographic information, these
                    datasets need to be integrated. We have now reached the point where the technology makes this
                    entirely feasible, so sigillography has reached a critical juncture. The challenge is no longer
                    technological, but rather conceptual. The shift to a digital format offers an opportunity to investigate
                    the potential of new types of catalogues and indexes that enable novel ways of accessing the
                    materials, while also facilitating access for both scholars and the public.
                    <br><br>
                    (1) Brigitte Bedos-Rezak, 'Seals and Sigillography, Western European', in Joseph R. Strayer, ed. 
                    <i>Dictionary of the Middle Ages</i> (New York: Scribner, 1988), pp.123.
                    
                    <br><br>
                    <i>DigiSig</i><br><br>

                    DigiSig is an experimental digital humanities project which brings together a number of major
                    datasets, produced by the archives, museums, and the higher education sectors. These datasets have been
                    reconfigured, enhanced and integrated, so that can be searched in concert, and photographs added,
                    where possible. The system enables users to access sigillographic information in a novel format.

                    <br><br><i>The Author</i><br><br>

                    John McEwan BA (University of Western Ontario), MA PhD (Royal Holloway, University of London)
                    specializes in the political, social and cultural history of medieval Britain. His research focuses on
                    social organization, local government, and visual culture in London, c.1100-1350. He is involved in a
                    number of projects that investigate the application of electronic data management tools, including
                    geographic information systems, to the analysis of medieval sources. Among his recent publications
                    are: ‘Making a mark in medieval London: the social and economic status of seal-makers, c.1200-
                    1350', in Seals and their Context in the Middle Ages (2015), 'The politics of financial accountability:
                    auditing the chamberlain in London c.1298-1349', in Hiérarchie des Pouvoirs, Délégation de Pouvoir
                    et Responsabilité des Administrateurs dans L'Antiquité et au Moyen Âge (2012), and ‘The aldermen
                    of London, c.1200-80: Alfred Beaven revisited', Transactions of the London and Middlesex
                    Archaeological Society (2012). His current book project is concerned with the formation, articulation
                    and expression of collective identities in thirteenth-century London.
                    
                    <br><br><i>Acknowledgements</i><br><br>
                    This project was made possible by the generous support of a large number of scholars and
                    repositories who have offered both guidance and advice, as well as data and special access to the
                    historical materials. The project was carried out in 2014-15 at the Centre for Digital Humanities at St
                    Louis University, Missouri thanks to a fellowship provided by the Walsh Allen foundation. The author
                    wishes to thank all the members the centre's web development team, as well as James Ginther and
                    Debra Cashion, for their support throughout the year.
                    
                   
                    ";
                }
                echo "</div>"; //close page wrap
                break;

            case 'advanced search' :
                echo '<div class="pageWrap">';
                {
                    echo "Section under construction. Please check back regularly for updates";
                }
                echo "</div>"; //close page wrap
                break;

            case 'contact' :
                echo '<div class="pageWrap">';
                {
                    echo "<br>Walter J. Ong, <small>S.J.</small> Center for Digital Humanities<br>
                            Pius XII Memorial Library, 324 AB Tower<br>
                            Saint Louis University<br>
                            3650 Lindell Blvd<br>
                            St. Louis, MO 63108<br>
                            <a href='http://slu.academia.edu/JohnMcEwan' target='_blank'>http://slu.academia.edu/JohnMcEwan</a>";

                }
                echo "</div>"; //close page wrap
                break;

            
            
                default :    
                echo '<div class="pageWrap homeWrap">';           
                include "include/imageGallery.php";

                echo "<h2 class='using'>using digisig</h2>";
                echo "<div class='info_text'>
                    <h4>seal  <div class='icon_seal'></div></h4>
                    <p>
                        Find a particular 'seal' by entering its DIGISIG <i>identification number</i>, eg: '10213781'.
						The seal information page offers references to descriptions of the seal and a list of examples (impressions, matrices, casts).
						
                    </p>
                    <h4>item <div class='icon_item'></div></h4>
                    <p>
                        An 'item' is a document or object in an archive or museum. 
						To search for an item, enter the item's <i>shelfmark</i>, eg: 'DL10/87', or a <i>location</i>, eg: 'Westminster'. 
						The location is provided by the repository which holds the item, and may refer to where the item originates or where it was discovered.
						On the item information page you discover what seal impressions, matrices, and casts are components of the item. 
                    </p>
                    <h4>description <div class='icon_descr'></div></h4>
                    <p>
                        A 'description' is an entry in a seal catalogue. You can search descriptions in three ways. Each description can be located by its <i>identifier</i>, eg: 'P38'. 
						Descriptions can also be searched by entering a <i>name</i>, eg: 'Henry of Grosmont'. 
						Alternatively, you can search the text that the cataloguer has used to describe the seal's <i>motif</i>, eg: 'griffin'. 
						The description information page provides links to related descriptions and to the seal information page.
                    </p>
                    <h4>impression/matrix/cast <div class='icon_impression'></div><div class='icon_matrix'></div><div class='icon_cast'></div></h4>
                    <p>
                        Seals survive in different physical forms. A matrix is a stamp used to make a seal impression. 
						A seal impression is an object imprinted with a seal matrix. A cast is a modern copy of a seal impression. 
                    </p>
                </div>";
                echo "<div class='info_images'>
                    <div class='img_structure'>
                    <img src='./images/digisig_structure.jpg' alt='diagram' style='width: 100%;'>
                    </div>
                </div>";

                echo "</div>"; //close page wrap
                echo "<div class='sources'>";
                echo "<div class='sources_title'>
                <h3>Contributing Repositories & Sources</h3>
                <p><i>
                            Search <b>$sealcount</b> seal records from the following sources:
                        </i>
                    </p>
                </div>";
				
				// graph section -- needs development
/*				$query = "SELECT repository_fulltitle, numofcases FROM dataforgraph_view ORDER BY numofcases DESC";
                $queryresults = mysqli_query($link, $query);
				while ($row = mysqli_fetch_assoc($queryresults)) {
					$graphdata[ ] = $row;	
                }

				echo "<div id='graph'>";
				<canvas id='graph_canvas' height='100', width = '100'>
				<script src='include/chartjs/js/Char.min.js'>				
				</script>
				</canvas>
				echo "</div>";
*/
				
				//project section
                echo "<div id='projects'>";
                echo "<span class='separator_2'>Publications and Projects</span><br>";
                //somehow we have to exclude repository 'unknown'
                //AND title != 'unknown'
                $query = "SELECT DISTINCT title, uri_catalogue FROM search_view WHERE title NOT IN ('Public Index') AND title != 'unknown' ORDER BY title";
                $queryresults = mysqli_query($link, $query);  
                while ($row = mysqli_fetch_assoc($queryresults)) {
                    echo '<a href="' . $row['uri_catalogue'] . '" target="_blank">' . $row['title'] . '</a>';
                    echo "<br>";
                }
                echo "</div>";
                echo "<div id='repos'>";
                echo "<span class='separator_2' style='margin-top: 5px; display: block;'>Repositories  </span>";
                #$query = "SELECT DISTINCT repository_fulltitle, id_archoncode FROM shelfmark_view ORDER BY repository_fulltitle";
				#JM: switched query to run off of graph table --> resolves the issues mentioned above (lines 873 etc...)
                $query = "SELECT DISTINCT repository_fulltitle, id_archoncode FROM dataforgraph_view ORDER BY repository_fulltitle";
                $queryresults = mysqli_query($link, $query);
                
				
				while ($row = mysqli_fetch_assoc($queryresults)) {
                    echo '<a href="' . $archonsearch . "?_ref=" . $row['id_archoncode'] . '" target="_blank">' . $row['repository_fulltitle'] . '</a>';
                    echo "<br>";
                }
                echo "</div>";
                echo "<div style='clear:both'></div>";
                echo "</div>";
                


                             
        }
        echo "</div>"; //close content_wrap
        
        include "include/footer.php";
    ?>
                <div class="addedCardArea">
                    <div class="closeBtn" onclick="$('.addedCardArea').hide();">X</div>
                    <div class="addedCardHeader">Card Folder</div>
                    <div class="inst">To add cards to the folder, check the box under the heading '&#x2713;' in the results tables on the page.</div>
                    <div class="thecards"></div>
                </div>
            <div class="viewCardWidget">
                    <div class="toggleArrow" active="no" onclick="toggleCardWidget($(this));"> < </div>
                    <div class="cardCountText">You have <span id="cardcount">0</span> cards in your folder.</div>
                    <a class='viewCardLink' onclick="$('.addedCardArea').show(); $('.toggleArrow').click();">View Cards</a>
                </div>
		</body>
		<script src="<?php echo $basePath; ?>/digisig/include/lightbox/js/lightbox-plus-jquery.min.js"></script>
		<script>
            function toggleCardWidget($toggle){
                if($toggle.attr("active") == "no"){
                    $(".viewCardWidget").css("right", "0px");
                    $(".toggleArrow").html(" > ");
                    $toggle.attr("active", "yes");
                }
                else{
                    $(".viewCardWidget").css("right", "-190px");
                    $(".toggleArrow").html(" < ");
                    $toggle.attr("active", "no");
                }
            }

            var cardID = 0 ;
		    var basePath = '<?php echo 'http://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], '/')); ?>';
			var num_result_per_page = parseInt(<?php echo $num_result_per_page ?>);
			var table_text_len = 100;
			
			function getFullText(id) {
				$('#a_' + id).html($('#full_' + id).val());
				$('#get_' + id).html('(Less)').click(function() {
					$('#a_' + id).html($('#short_' + id).val() + '...');
					$('#get_' + id).html('(More)');
				});
			}

			function getNextData(field, index, term, address, exact, limit) {
				$('#load_next_pending_' + field).show();
				var offset = parseInt($('#show_more_btn_' + field).attr('offset'));
				$.post(basePath + '/digisig/include/loadNextData.php', {
					'field' : field,
					'index' : index,
					'term' : term,
					'address' : address,
					'exact' : exact,
					'offset' : offset,
					'limit' : limit
				}).done(function(data) {
                    console.log("Call back from loadNextData.php...");
                    console.log(data);
                    var counter = 0;
					if (data != '00000' && '' != data) {
						data = JSON.parse(data);
						var del_show_more = data['del_show_more'];
						var c = 0;
                        var length1 = Object.keys(data).length;
						for (d in data) {
							var v1 = data[d][0];
							var v2 = data[d][1];
							var v3 = data[d][2];
							var short_value2 = v2.substr(0, table_text_len);
							var lastRowNum = $('#show_more_tr_' + field).attr('last_row_num');
							if (v2.length > table_text_len) {
								$('#show_more_tr_' + field).before('<tr><td><input onchange="cardMe($(this), false, false);" type="checkbox"></td><td>' + lastRowNum + '</td><td><a id="a_' + v1 + '" href=' + address + '/entity/' + v1 + '>' + short_value2 + '...</a> <a id="get_' + v1 + '" onclick="getFullText(' + v1 + ')">(More)</a><input type="hidden" id="full_' + v1 + '" value="' + v2 + '" /><input type="hidden" id="short_' + v1 + '" value="' + short_value2 + '" /></td><td>' + v3 + '</td></tr>');
							} else {
								$('#show_more_tr_' + field).before('<tr><td><input onchange="cardMe($(this), false, false);" type="checkbox"></td><td>' + lastRowNum + '</td><td><a id="a_' + v1 + '" href=' + address + '/entity/' + v1 + '>' + v2 + '</a></td><td>' + v3 + '</td></tr>');
							}
							lastRowNum++;
							$('#show_more_tr_' + field).attr('last_row_num', lastRowNum);
							c++;
                            console.log("found row "+c+" of "+length1);
						}
						$('#show_more_tr_' + field).attr('last_row_num', lastRowNum--);
						$('#load_next_pending_' + field).hide();
						offset += num_result_per_page;
						$('#show_more_btn_' + field).attr('offset', offset);
					} else {
						//$('#load_next_pending_' + field).hide();
                        $('#load_next_pending_' + field).html("Could not get entries...");
					}
					if(c < num_result_per_page || c == 0){
					    $('#show_more_tr_' + field).remove();
					}
				});
			}
                        function linkToClipboard(){
                            var linkText = $("#permalink").html();
                            window.prompt("If you would like to copy to clipboard press 'Ctrl+C' (Windows) or 'Cmd-C' (Mac), then 'Enter' to close", linkText);
                        }
                        function viewFullImage($btn){
                            var $image = $btn.prev();
                            var source = $image.attr("src");
                            $("<div class='pageShade'></div>").appendTo("body");
                            var $fullImg = $("<div class='fullImgWrap'><div class='closeBtn' onclick='closeFullImg();'>X</div><img src='"+source+"'/></div>");
                            $(".pageShade").append($fullImg);
                        }
                        function closeFullImg(){
                            $(".fullImgWrap").remove();
                            $(".pageShade").remove();
                        }
                        function cardMe($checkbox, card, card2){
                            var cardCount = $("#cardcount").html();
                            cardCount = parseInt(cardCount);
                            if($checkbox.is(":checked")){
                                if(card == false){
                                    console.log("Assign a unique card ID");
                                    cardID++;
                                    card = cardID;
                                    console.log(cardID);
                                    $checkbox.attr("onchange", "cardMe($(this), '"+cardID+"', "+card2+");");                                
                                }
                                console.log("Add card to stack: " + card);
                                if(card2 === true){
                                    cardHTML = $checkbox.parent().parent().clone();
                                    cardHTML.find("input[type='checkbox']").remove();
                                    if(cardHTML.attr('cardID') === undefined || cardHTML.attr('cardID') === ''){
                                        cardHTML.attr("cardID", cardID);
                                    }  
                                }
                                else{
                                    var $dataLabels = $checkbox.closest("table").children("thead").children("tr").children("th");
                                    var $dataRow = $checkbox.closest("tr").children("td");
                                    var parsedObject = {};
                                    var keyArray = [];
                                    var valueArray = [];
                                    for(var i=1; i<$dataLabels.length; i++){
                                        console.log("Key "+$($dataLabels[i]).html());
                                        keyArray.push($($dataLabels[i]).html());
                                    }
                                    for(var j=1; j<$dataRow.length; j++){
                                        console.log("Value "+$($dataRow[j]).html())
                                        valueArray.push($($dataRow[j]).html());
                                    }
                                    for(var k=0; k<$dataRow.length-1; k++){
                                        parsedObject[keyArray[k]] = valueArray[k];
                                    }
                                    console.log(parsedObject);
                                    var cardHTML = $("<div cardID='"+card+"' class='card'></div>");
                                    $.each(parsedObject, function(key,value){
                                        var appender = "";
                                        if(key === "#"){
                                            appender = $("<div class='cardNum'>\n\
                                                "+key+" "+value+"\n\
                                            </div>");
                                        }
                                        else if(value.trim() !== ""){
                                            appender = $("<div class='cardInfo'>\n\
                                                <span class='cardInfoKey'>"+key+":</span><span class='cardInfoVal'> "+value+"</span>\n\
                                            </div>");
                                        }
                                        cardHTML.append(appender);
                                    });
                                }
                                console.log("Card html");
                                console.log(cardHTML);
                                $(".theCards").append(cardHTML);
                                cardCount++;
                                $("#cardcount").html(cardCount);
                                //$(".addedCardArea").show();
                            }
                            else{
                                //Remove from card stack
                                console.log("Remove card from stack: " + card);
                                $(".theCards").find("div[cardID='"+card+"']").remove();
                                cardCount--;
                                $("#cardcount").html(cardCount);
                            }
                            if($(".toggleArrow").attr("active")==="no"){
                                $(".viewCardWidget").css("right", "0px");
                                $(".toggleArrow").html(" > ");
                                $(".toggleArrow").attr("active", "yes");
                            }
                        }

                        function toggleSources(which){
                            console.log("TOGGLE SOURCES");
                            if(which === 1){
                                console.log("TOGGLE 1");                               
                                $("#sources_2").css("left", "-560px");
                                $("#sources_1").css("left", "48px");
                            }
                            else if (which === 2){
                                console.log("TOGGLE 2");                            
                                $("#sources_1").css("left", "-560px");
                                $("#sources_2").css("left", "48px");
                            }
                            else if(which === 0){
                                $("#sources_1").css("left", "-560px");
                                $("#sources_2").css("left", "-560px");
                            }
                            else{
                                console.warn("toggle called on odd target.");
                            }
                        }

                        
                        $(function(){
                            var url = window.location.href;
                            if(url.indexOf("/digisig/about")>-1 || url.indexOf("/digisig/contact")>-1){
                                $(".viewCardWidget").hide();
                            }
                        });
                        
		</script>
</html>

