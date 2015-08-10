<?php $basePath = 'http://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], '/') + 1); ?>
<html>

	<head>
		<script src="<?php echo $basePath; ?>digisig/include/lightbox/js/lightbox-plus-jquery.min.js"></script>
		<link rel="stylesheet" href="<?php echo $basePath; ?>digisig/css/digisigSkin.css" />

	</head>
	<body>

		<?php // ALPHA version: July 2015

        #functions

        #connection details
        include "config/config.php";

        #constants and default values
        include "include/constants.php";


session_start();
include "header.php";
echo '<div class="pageWrap">';
//user login

        #constants and default values
        include "include/constants.php";

        //my functions
        include "include/function.php";
        //functions copied from other people
        include "include/function_parsepath.php";

        $exact = "";
        if (isset($_POST['submit'])) {

            $page = "/" . strtolower($_POST['submit']);

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

            $url = ($address . $page . $field . $index . $term . $exact);
            // reload the page with the new header
            header('Location:' . $url);
        }

        // reset the post array to clear any lingering data
        $_POST = array();

        /* If the page has NOT received instructions via 'post'
         * check to see if header contains search instructions
         */

        $path_info = parse_path();

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

        include "include/page.php";

        // load the optional extra parts of the page depending on the header

        switch($path_info['call_parts'][0]) {

            case 'search' :

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
                break;

            case 'entity' :

                # show information about a specific entity

                // first test that we have an entity number and proceed if yes
                if ($id > 0) {
                    # 1) determine what view to query using the entity number
                    $query6 = "SELECT * FROM entity WHERE entity_code = $entity";
                    $query6result = mysqli_query($link, $query6);
                    $row = mysqli_fetch_object($query6result);
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
                            
                            
                            //echo "ITEM";
                            echo '<div class="seal sealPiece">ITEM</div>
                            <div class="sealMetadata sealPiece"><span class="sealLabel">Digisig ID: </span><span id="digisigID">DIGISIG ID: ' .$id.'</span>
                            <span clss="sealLabel">Permalink: </span><span id="permalink">http://digisig.org/entity/'. $id .'</span>
                            <input class="digiBtn" type="button" value="Copy Link" onclick="linkToClipboard();" />
                            </div>';

                            //echo "<br><br>" . $value1 . ": " . $value2;
                            //all the other values listed under shelfmark are optional
                            echo '<table class="metaTable"><thead><th>Title</th><th>Location</th><th>Description</th><th>Dated</th><th>External Link</th></thead>'
                            . '<tbody><tr><td>'.$value1.':'.$value2.'</td><td>'.$value12.'</td><td>'.$value13.'</td><td>'.$value10.'</td><td>'.$value14.$value15.'</td></tr></tbody></table>';
//                            if (isset($value15)) {
//                                echo '<a href="' . $value14 . $value15 . '" target="_blank">external link</a>';
//
//                            }
//
//                            if (isset($value10)) {
//                                echo "<br> dated:" . $value10;
//                                if (isset($value11)) {
//                                    echo " to " . $value11;
//                                }
//                            }
//
//                            if (isset($value12)) {
//                                echo "<br> Location:" ;
//                            }
//
//                            If (isset($value13)) {
//                                echo "<br> Description:" . $value13;
//                            }
                            

                            //show table of associated impressions
                            $query12 = "SELECT * FROM shelfmark_view WHERE id_item = $id ORDER BY position_latin";
                            $query12result = mysqli_query($link, $query12);

                            // table detailing which seal impressions are associated with this item
                            echo "<div class='separator_2'>Examples</div>";
                            echo '<table class="metaTable">'
                            . '<thead><th>#</th><th>Nature</th><th>Number</th><th>Position</th><th>Shape</th><th>Seal Link</th><th>Thumbnail</th></thead>'
                            . '<tbody>'; //'<tr><td></td><td>nature</td><td>number</td><td>position</td><td>shape</td></tr>'
                            
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
                                if ($value16 == "local") {
                                    $value16 = $medium;
                                    $value17 = $small;
                                }
                                echo '<tr><td>' . $rowcount . '</td>';
                                echo '<td>' . $value3 . '</td>';
                                echo '<td>' . $value4 . '</td>';
                                echo '<td>' . $value5 . '</td>';
                                echo '<td>' . $value6 . '</td>';
                                echo '<td><a href="' . $address . '/entity/' . $value7 . '">view seal entry</a></td>';
                                If (isset($value18)) {
                                    if (1 == $row['fk_access']) {
                                        echo '<td><a href="' . $value19 . $value8 . '" data-lightbox="example-1" data-title="' . $value2 . '<br>photo: ' . $value9 . '"><img src="' . $value17 . $value18 . '" /></a></td></tr>';
                                    } else if (isset($_SESSION['userID']) && ($_SESSION['fk_access'] == $row['fk_access'] || $_SESSION['fk_repository'] == $row['fk_repository'])) {
                                        echo '<td><a href="' . $value19 . $value8 . '" data-lightbox="example-1" data-title="' . $value2 . '<br>photo: ' . $value9 . '"><img src="' . $value17 . $value18 . '" /></a></td></tr>';
                                    } else {
                                        echo '<td><a href="' . $default . 'restricted.jpg" data-lightbox="example-1" data-title="' . $value2 . '<br>photo: ' . $value9 . '"><img src="' . $default . 'restricted_thumb.jpg"/></a></td></tr>';
                                    }
                                }

                                $rowcount++;
                            }
                            echo '</tbody></table>';
                        }

                        //for seal descriptions
                        If ($entity == 3) {
                            $row = mysqli_fetch_array($query8result);

                            //assign variables
                            $value1 = $row['a_index'];
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
                            //formulate header
                            echo '<div class="seal sealPiece">Seal Description</div>
                            <div class="sealMetadata sealPiece"><span class="sealLabel">DigiSig ID: </span><span id="digisigID">' .$id.'</span>
                            <span clss="sealLabel">Permalink: </span><span id="permalink">http://digisig.org/entity/'. $id .'</span>
                            <input class="digiBtn" type="button" value="Copy Link" onclick="linkToClipboard();" />
                            </div>';
                            $tableHeader = "<thead><th>Title</th>";
                            $tableBody = "<tbody><tr>";
                            
                            // title
                            //echo $value1 . ":" . $value4;
                            $tableBody .= "<td>".$value1.":".$value4."</td>";
                            if (isset($value2)) {
                                $tableHeader .= "<th>Volume</th>";
                                $tableBody .= "<td>".$value2."</td>";
                                //echo ", vol." . $value2;
                            }
                            if (isset($value3)) {
                                if (strpos($value3, '-') !== false) {
                                    $tableHeader .= "<th>Page</th>";
                                    $tableBody .= "<td>p.".$value3."</td>";
                                    //echo ", p." . $value3;
                                } else {
                                    $tableHeader .= "<th>Page</th>";
                                    $tableBody .= "<td>pp.".$value3."</td>";
                                    //echo ", pp." . $value3;
                                }
                            }
                            if (isset($value13)) {
                                $tableHeader .= "<th>External Link</th>";
                                $tableBody .= "<td><a href='" . $value14 . $value13 . "'>" . $value14 . $value13 . "</a></td>";
                                //echo '<a href="' . $value14 . $value13 . '" target="_blank">external link</a>';
                            }
                            //output entry -- only output variables with values

                            if (isset($value5)) {
                                $tableHeader .= "<th>Name</th>";
                                $tableBody .= "<td>".$value5."</td>";
                                //echo '<br><br> Name:' . $value5 . '<br>';
                            }

                            if (isset($value6)) {
                                $tableHeader .= "<th>Motif</th>";
                                $tableBody .= "<td>".$value6."</td>";
                                //echo '<br> Motif:' . $value6 . '<br>';
                            }

                            if (isset($value7)) {
                                $tableHeader .= "<th>Legend</th>";
                                $tableBody .= "<td>".$value7."</td>";
                                //echo '<br> Legend:' . $value7 . '<br>';
                            }

                            if (isset($value8)) {
                                $tableHeader .= "<th>Shape</th>";
                                $tableBody .= "<td>".$value8."</td>";
                                //echo '<br> Shape:' . $value8 . '<br>';
                            }

                            if (isset($value9)) {
                                $tableHeader .= "<th>Size Y</th>";
                                $tableBody .= "<td>".$value9."</td>";
                                //echo '<br> Size Vertical:' . $value9 . '<br>';
                            }

                            if (isset($value10)) {
                                $tableHeader .= "<th>Size X</th>";
                                $tableBody .= "<td>".$value10."</td>";
                                //echo '<br> Size Horizontal:' . $value10 . '<br>';
                            }

                            //prepare the photograph -- if it is available
                            if (isset($value12)) {
                                $tableHeader .= "<th>Image</th>";
                                
                                if (1 == $row['fk_access']) {
                                    $tableBody .= '<td><img class="sealThumbnail" src="' . $description . $value12 . '"/>'
                                    . '<input class="digiBtn viewImgBtn" type="button" value="View Image" onclick="viewFullImage($(this));"/></td>';
                                   
                                    //echo '<a href="' . $description . $value12 . '" data-lightbox="example-1" data-title=""><img src="' . $description . $value12 . '" height=200></img></a><br>';
                                } else if (isset($_SESSION['userID']) && ($_SESSION['fk_access'] == $row['fk_access'] || $_SESSION['fk_repository'] == $row['fk_repository'])) {
                                    $tableBody.= '<td><img class="sealThumbnail" src="' . $description . $value12 . '" height="200"/>'
                                    . '<input class="digiBtn viewImgBtn" type="button" value="View Image" onclick="viewFullImage($(this));"/></td>';
                                   // echo '<a href="' . $description . $value12 . '" data-lightbox="example-1" data-title=""><img src="' . $description . $value12 . '" height=200></img></a><br>';
                                } else {
                                    $tableBody .= '<td><img class="sealThumbnail" src="' . $default . 'restricted_thumb.jpg" height=50/>'
                                    . '<input class="digiBtn viewImgBtn" type="button" value="View Image" onclick="viewFullImage($(this));"/></td>';
                                    //echo '<td><a href="' . $default . 'restricted.jpg"><img src="' . $default . 'restricted_thumb.jpg" height=50></img></a></td></tr>';
                                }
                            }

                            //link to seal page
                            $tableHeader .= "<th>Seal Link</th></thead>";
                            $tableBody .= "<td><a href='". $address ."/entity/". $value11."'>view seal entry</a></td></tr></tbody>";
                            echo "<table>".$tableHeader.$tableBody."</table>";
                            //echo '<br><a href=' . $address . '/entity/' . $value11 . '>view seal entry</a><br>';

                            //check for other seal descriptions

                            if(isset($value11) && '' != $value11){
                                $query12 = "SELECT * FROM sealdescription_view WHERE id_seal = $value11";
                                $query12result = mysqli_query($link, $query12);
    
                                $count = mysqli_num_rows($query12result);
                                if ($count > 1) {
                                    //echo "other descriptions";
                                    echo "<div class='separator_2'>Other Descriptions</div>";
                                    $duplicate = $id;
                                    sealdescription($query12result, $address, $duplicate);
                                }
                            }
                        }

                        //for a seal
                        If ($entity == 1) {

                            echo '<div class="seal sealPiece">SEAL</div>
                            <div class="sealMetadata sealPiece"><span class="sealLabel">Digisig ID: </span><span id="digisigID">DIGISIG ID: ' .$id.'</span>
                            <span clss="sealLabel">Permalink: </span><span id="permalink">http://digisig.org/entity/'. $id .'</span>
                            <input class="digiBtn" type="button" value="Copy Link" onclick="linkToClipboard();" />
                            </div>';

                            echo '<table class="metaTable">'
                            . '<thead><th>Shape</th><th>Height</th><th>Width</th></thead>'
                            . '<tbody><tr>';

                            // note that a seal can have two faces but I am going to assume that the double side ones are the same
                            $row = mysqli_fetch_array($query8result);
                            $value3 = $row['shape'];
                            $value4 = $row['face_vertical'];
                            $value5 = $row['face_horizontal'];

                            echo '<td>' . $value3 . '</td>';
                            echo '<td>' . $value4 . '</td>';
                            echo '<td>' . $value5 . '</td></tr>';
                            $id_seal = $row['id_seal'];

                            echo "</tbody></table>";

                            // call seal description function to make list of associated seal descriptions

                            $query12 = "SELECT * FROM sealdescription_view WHERE id_seal = $id";
                            $query12result = mysqli_query($link, $query12);
                            $count = mysqli_num_rows($query12result);
                            $duplicate = $id;
                            if ($count > 0) {
                                //echo "<div class='separator_2'>Other Descriptions</div>";
                                echo "<br><div class='separator_2'>Other Descriptions</div>";
                                $duplicate = $id;
                                sealdescription($query12result, $address, $duplicate);
                            }

                            // list of associated seal impressions
                            $query10 = "SELECT * FROM shelfmark_view WHERE id_seal = $id";
                            $query10result = mysqli_query($link, $query10);
                            echo '<div class="separator_2">Examples</div>';
                            echo '<table class="metaTable"><thead><th>#</th><th>Nature</th><th>Number</th><th>Position</th><th>Dated</th><th>External Link</th><th>Thumbnail</th></thead>'
                            . '<tbody>';
                            $rowcount = 1;

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
                                $value7 = $row['representation_filename'];

                                //test to see if the connection string indicates that it is in the local image store
                                if ($value12 == "local") {
                                    $value12 = $small;
                                    $value14 = $medium;
                                }
                                echo '<tr><td>' . $rowcount . '</td>';
                                echo '<td>' . $value1 . '</td>';
                                echo '<td>' . $value2 . '</td>';
                                echo '<td>' . $value3 . '</td>';
//                                echo '<td>' . $value4 . '</td>';
                                echo '<td> dated:' . $value9 . ' to ' . $value10;
                                echo '<td><a href=' . $address . '/entity/' . $value6 . '>' . $value5 . '</a></td>';
                                if (isset($value13)) {

                                    if (1 == $row['fk_access']) {
                                        echo '<td><a href="' . $value12 . $value13 . '" data-lightbox="example-1" data-title="' . $value5 . '<br>photo: ' . $value8 . '"><img src="' . $value12 . $value13 . '" height=50></img></a></td>';
                                    } else if (isset($_SESSION['userID']) && ($_SESSION['fk_access'] == $row['fk_access'] || $_SESSION['fk_repository'] == $row['fk_repository'])) {
                                        echo '<td><a href="' . $value12 . $value13 . '" data-lightbox="example-1" data-title="' . $value5 . '<br>photo: ' . $value8 . '"><img src="' . $value12 . $value13 . '" height=50></img></a></td>';
                                    } else {
                                        echo '<td><a href="' . $default . 'restricted.jpg" data-lightbox="example-1" data-title="' . $value5 . '<br>photo: ' . $value8 . '"><img src="' . $default . 'restricted_thumb.jpg" height=50></img></a></td></tr>';
                                    }

                                }
                                echo '</tr>';
                                $rowcount++;
                            }
                            echo "</tbody></table>";
                        }
                    }else{
                        echo "No Data Found...";
                    }

                }
                break;

            case 'about' :
                {
                    echo "<br>
                    <div class='aboutHeader'>About Digitial Sigillography</div><br>
                    Hundreds of thousands of seals survive from medieval Europe, and they provide unique and

                    important information. A seal is ‘a mark of authority or ownership, pressed in relief upon a plastic

                    material by the impact of a matrix or die-engraved intaglio’. Men and women from all levels of

                    society used seals to validate documents, but also to make statements about their family

                    connections, social aspirations and personal values. Seals incorporate both text and images so they

                    are powerful tools of expression. In a period starved of evidence concerning the individual, seals

                    offer insight into identity, and expose regional and local cultural variations. The advent of digital

                    technology offers an unprecedented and exciting opportunity to harness the extraordinary potential

                    of this unique historical resource.


                    Today medieval seals are preserved in archives and museums across the British Isles where they are

                    often prominently and proudly displayed as iconic monuments of artistic and cultural heritage.

                    However, they remain poorly understood because there is no central place where researchers and

                    members of the general public can turn for information. This is partly because much of the

                    information is trapped in outdated and unstandardized formats. Many institutions began

                    cataloguing their collections in the nineteenth and twentieth centuries, well before the advent of

                    electronic data management systems. The result is that we now have information in a wide variety

                    of formats ranging from card indexes, to printed catalogues, to electronic databases.


                    Scholars have long argued that to realize the full potential of sigillographic information, these

                    datasets need to be integrated. We have now reached the point where the technology makes this

                    entirely feasible, so sigillography has reached a critical juncture. The challenge is no longer

                    technological, but rather conceptual. The shift to a digital format offers an opportunity to investigate

                    the potential of new types of catalogues and indexes that enable novel ways of accessing the

                    materials, while also facilitating access for both scholars and the public.";
                }
                break;

            case 'advanced search' :
                {
                    echo "<br>Text about forthcoming search options";
                }
                break;

            case 'contact' :
                {
                    echo "<br>Center for Digital Humanities<br>
                            Pius XII Memorial Library, 324 AB Tower<br>
                            Saint Louis University<br>
                            3650 Lindell Blvd<br>
                            St. Louis, MO 63103<br>
                            <a href='http://slu.academia.edu/JohnMcEwan'>http://slu.academia.edu/JohnMcEwan</a>";
                }
                break;

            default :
                echo "<div class='homeCopy'>
                    DigiSig<br><br>

                    DigiSig is an experimental digital humanities project which brings together a number of major
                    datasets, produced by the archives, museums, and the higher education sectors, that are publicly
                    accessible and extensively used by the public and academic researchers. These datasets have been
                    reconfigured, enhanced and integrated, so that can be searched in concert, and photographs added,
                    where possible. The system enables users to access sigillographic information in traditional ways,
                    but in a novel format.

                    <br><br>The Author<br><br>

                    John McEwan BA (University of Western Ontario), MA PhD (Royal Holloway, University of London)
                    specializes in the political, social and cultural history of medieval Britain. His research focuses on
                    social organization, local government, and visual culture in London, c.1100-1350. He is involved in a
                    number of projects that investigate the application of electronic data management tools, including
                    geographic information systems, to the analysis of medieval sources. Among his recent publications
                    are: ‘Making a mark in medieval London: the social and economic status of seal-makers, c.1200-
                    1350', in Seals and their Context in the Middle Ages (2015), 'The politics of financial accountability:
                    auditing the chamberlain in London c.1298-1349', in Hiérarchie des Pouvoirs, Délégation de Pouvoir
                    et Responsabilité des Administrateurs dans L’Antiquité et au Moyen Âge (2012), and ‘The aldermen
                    of London, c.1200-80: Alfred Beaven revisited’, Transactions of the London and Middlesex
                    Archaeological Society (2012). His current book project is concerned with the formation, articulation
                    and expression of collective identities in thirteenth-century London.
                    
                    <br><br>Acknowledgements<br><br>
                    This project was made possible by the generous support of a large number of scholars and
                    repositories who have offered both guidance and advice, as well as data and special access to the
                    historical materials. The project was carried out in 2014-15 at the Centre for Digital Humanities at St
                    Louis University, Missouri thanks to a fellowship provided by the Wash Allen foundation. The author
                    wishes to thank all the members the centre's web development team, as well as James Ginther and
                    Debra Cashions, for their support throughout the year. 
                    </div><br>";
                
                echo "<div class='searchResults'>";
                //echo "<div class='resultsTitle'>Results</div>";
                echo "<span class='separator'>Publications and Projects</span><br>";

                $query = "SELECT DISTINCT title, uri_catalogue FROM search_view WHERE title NOT IN ('Public Index') ORDER BY title";
                $queryresults = mysqli_query($link, $query);
                while ($row = mysqli_fetch_assoc($queryresults)) {
                    echo '<a href="' . $row['uri_catalogue'] . '" target="_blank">' . $row['title'] . '</a>';
                    echo "<br>";
                }

                echo "<span class='separator'>Repositories</span><br>";
                $query = "SELECT DISTINCT repository_fulltitle, id_archoncode FROM shelfmark_view ORDER BY repository_fulltitle";
                $queryresults = mysqli_query($link, $query);
                while ($row = mysqli_fetch_assoc($queryresults)) {
                    echo '<a href="' . $archonsearch . "?_ref=" . $row['id_archoncode'] . '" target="_blank">' . $row['repository_fulltitle'] . '</a>';
                    echo "<br>";
                }
                echo "</div>";

        }
        echo "</div>"; //close page wrap
        include "include/footer.php";
    ?>

		</body>
		<script src="<?php echo $basePath; ?>digisig/include/lightbox/js/lightbox-plus-jquery.min.js"></script>
		<script>
		    var basePath = '<?php echo 'http://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], '/') + 1); ?>';
			var num_result_per_page = <?php echo $num_result_per_page ?>;
			
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
				$.post(basePath + 'digisig/include/loadNextData.php', {
					'field' : field,
					'index' : index,
					'term' : term,
					'address' : address,
					'exact' : exact,
					'offset' : offset,
					'limit' : limit
				}).done(function(data) {
					if (data != '00000' && '' != data) {
						data = JSON.parse(data);
						for (d in data) {
							var v1 = data[d][0];
							var v2 = data[d][1];
							var v3 = data[d][2];
							var short_value2 = v2.substr(0, 50);
							var lastRowNum = $('#show_more_tr_' + field).attr('last_row_num');
							if (v2.length > 50) {
								$('#show_more_tr_' + field).before('<tr><td>' + lastRowNum + '</td><td><a id="a_' + v1 + '" href=' + address + '/entity/' + v1 + '>' + short_value2 + '...</a> <a id="get_' + v1 + '" onclick="getFullText(' + v1 + ')">(More)</a><input type="hidden" id="full_' + v1 + '" value="' + v2 + '" /><input type="hidden" id="short_' + v1 + '" value="' + short_value2 + '" /></td><td>' + v3 + '</td></tr>');
							} else {
								$('#show_more_tr_' + field).before('<tr><td>' + lastRowNum + '</td><td><a id="a_' + v1 + '" href=' + address + '/entity/' + v1 + '>' + v2 + '</a></td><td>' + v3 + '</td></tr>');
							}
							lastRowNum++;
							$('#show_more_tr_' + field).attr('last_row_num', lastRowNum);
						}
						$('#show_more_tr_' + field).attr('last_row_num', lastRowNum--);
						$('#load_next_pending_' + field).hide();
						offset += parseInt(num_result_per_page);
						$('#show_more_btn_' + field).attr('offset', offset);
					} else {
						$('#load_next_pending_' + field).hide();
					}
				});
			}
                        function linkToClipboard(){
                            var linkText = $("#permalink").html();
                            window.prompt("If you would like to copy to clipboard press 'Ctrl+C' (Windows) or 'Cmd-C' (Mac), then 'Enter' to close", linkText);
                        }
		</script>
</html>

