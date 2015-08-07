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

                            echo "ITEM";
                            echo "<br> DIGISIG ID:" . $id;
                            echo "<br> Permalink: http://digisig.org/entity/" . $id;

                            echo "<br><br>" . $value1 . ": " . $value2;
                            //all the other values listed under shelfmark are optional
                            if (isset($value15)) {
                                echo '<a href="' . $value14 . $value15 . '" target="_blank">external link</a>';

                            }

                            if (isset($value10)) {
                                echo "<br> dated:" . $value10;
                                if (isset($value11)) {
                                    echo " to " . $value11;
                                }
                            }

                            if (isset($value12)) {
                                echo "<br> Location:" . $value12;
                            }

                            If (isset($value13)) {
                                echo "<br> Description:" . $value13;
                            }

                            //show table of associated impressions
                            $query12 = "SELECT * FROM shelfmark_view WHERE id_item = $id ORDER BY position_latin";
                            $query12result = mysqli_query($link, $query12);

                            // table detailing which seal impressions are associated with this item
                            echo '<table border = 1><tr><td></td><td>Examples</td></tr><tr><td></td><td>nature</td><td>number</td><td>position</td><td>shape</td></tr>';
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
                                echo '<td><a href=' . $address . '/entity/' . $value7 . '>view seal</a></td>';
                                If (isset($value18)) {
                                    if (1 == $row['fk_access']) {
                                        echo '<td><a href="' . $value19 . $value8 . '" data-lightbox="example-1" data-title="' . $value2 . '<br>photo: ' . $value9 . '"><img src="' . $value17 . $value18 . '" </img></a></td></tr>';
                                    } else if (isset($_SESSION['userID']) && ($_SESSION['fk_access'] == $row['fk_access'] || $_SESSION['fk_repository'] == $row['fk_repository'])) {
                                        echo '<td><a href="' . $value19 . $value8 . '" data-lightbox="example-1" data-title="' . $value2 . '<br>photo: ' . $value9 . '"><img src="' . $value17 . $value18 . '" </img></a></td></tr>';
                                    } else {
                                        echo '<td><a href="' . $default . 'restricted.jpg" data-lightbox="example-1" data-title="' . $value2 . '<br>photo: ' . $value9 . '"><img src="' . $default . 'restricted_thumb.jpg"></img></a></td></tr>';
                                    }
                                }

                                $rowcount++;
                            }
                            echo '</table>';
                        }

                        //for seal descriptions
                        If ($entity == 3) {
                            $row = mysqli_fetch_array($query8result);

                            //assign variables
                            $value1 = $row['sdv_index'];
                            $value2 = $row['catalogue_volume'];
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
                            echo "SEAL DESCRIPTION";
                            echo "<br> DIGISIG ID: " . $id;
                            echo "<br> Permalink: http://digisig.org/entity/" . $id . "<br>";

                            // title
                            echo $value1 . ":" . $value4;
                            if (isset($value2)) {
                                echo ", vol." . $value2;
                            }
                            if (isset($value3)) {
                                if (strpos($value3, '-') !== false) {
                                    echo ", p." . $value3;
                                } else {
                                    echo ", pp." . $value3;
                                }
                            }
                            if (isset($value13)) {
                                echo '<a href="' . $value14 . $value13 . '" target="_blank">external link</a>';
                            }
                            //output entry -- only output variables with values

                            if (isset($value5)) {
                                echo '<br><br> Name:' . $value5 . '<br>';
                            }

                            if (isset($value6)) {
                                echo '<br> Motif:' . $value6 . '<br>';
                            }

                            if (isset($value7)) {
                                echo '<br> Legend:' . $value7 . '<br>';
                            }

                            if (isset($value8)) {
                                echo '<br> Shape:' . $value8 . '<br>';
                            }

                            if (isset($value9)) {
                                echo '<br> Size Vertical:' . $value9 . '<br>';
                            }

                            if (isset($value10)) {
                                echo '<br> Size Horizontal:' . $value10 . '<br>';
                            }

                            //prepare the photograph -- if it is available
                            if (isset($value12)) {
                                if (1 == $row['fk_access']) {
                                    echo '<a href="' . $description . $value12 . '" data-lightbox="example-1" data-title=""><img src="' . $description . $value12 . '" height=200></img></a><br>';
                                } else if (isset($_SESSION['userID']) && ($_SESSION['fk_access'] == $row['fk_access'] || $_SESSION['fk_repository'] == $row['fk_repository'])) {
                                    echo '<a href="' . $description . $value12 . '" data-lightbox="example-1" data-title=""><img src="' . $description . $value12 . '" height=200></img></a><br>';
                                } else {
                                    echo '<td><a href="' . $default . 'restricted.jpg"><img src="' . $default . 'restricted_thumb.jpg" height=50></img></a></td></tr>';
                                }
                            }

                            //link to seal page
                            echo '<br><a href=' . $address . '/entity/' . $value11 . '>view seal</a><br>';

                            //check for other seal descriptions

                            if(isset($value11) && '' != $value11){
                                $query12 = "SELECT * FROM sealdescription_view WHERE id_seal = $value11";
                                $query12result = mysqli_query($link, $query12);
    
                                $count = mysqli_num_rows($query12result);
                                if ($count > 1) {
                                    echo "other descriptions";
                                    $duplicate = $id;
                                    sealdescription($query12result, $address, $duplicate);
                                }
                            }
                        }

                        //for a seal
                        If ($entity == 1) {

                            echo "SEAL";
                            echo "<br> DIGISIG ID:" . $id;
                            echo "<br> Permalink: http://digisig.org/entity/" . $id;

                            echo '<table border = 1><tr><td>Shape</td><td>Height</td><td>Width</td></tr>';

                            // note that a seal can have two faces but I am going to assume that the double side ones are the same
                            $row = mysqli_fetch_array($query8result);
                            $value3 = $row['shape'];
                            $value4 = $row['face_vertical'];
                            $value5 = $row['face_horizontal'];

                            echo '<td>' . $value3 . '</td>';
                            echo '<td>' . $value4 . '</td>';
                            echo '<td>' . $value5 . '</td></tr>';
                            $id_seal = $row['id_seal'];

                            echo "</table><br>";

                            // call seal description function to make list of associated seal descriptions

                            $query12 = "SELECT * FROM sealdescription_view WHERE id_seal = $id";
                            $query12result = mysqli_query($link, $query12);
                            $count = mysqli_num_rows($query12result);
                            if ($count > 1) {
                                echo "other descriptions";
                                $duplicate = $id;
                                sealdescription($query12result, $address, $duplicate);
                            }

                            // list of associated seal impressions
                            $query10 = "SELECT * FROM shelfmark_view WHERE id_seal = $id";
                            $query10result = mysqli_query($link, $query10);

                            echo '<table border = 1><tr><td>Examples</td></tr><tr><td></td><td>';
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
                                echo '<td>' . $value4 . '</td>';
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
                            echo "</table><br>";
                        }
                    }else{
                        echo "No Data Found...";
                    }

                }
                break;

            case 'about' :
                {
                    echo "<br>Text about the project";
                }
                break;

            case 'advanced search' :
                {
                    echo "<br>Text about forthcoming search options";
                }
                break;

            case 'contact' :
                {
                    echo "<br>Where to contact us";
                }
                break;

            default :
                echo "<div class='searchResults'>";
                echo "<div class='resultsTitle'>Results</div>";
                echo "<span class='separator'>publications and projects</span><br>";

                $query = "SELECT DISTINCT title, uri_catalogue FROM search_view WHERE title NOT IN ('Public Index') ORDER BY title";
                $queryresults = mysqli_query($link, $query);
                while ($row = mysqli_fetch_assoc($queryresults)) {
                    echo '<a href="' . $row['uri_catalogue'] . '" target="_blank">' . $row['title'] . '</a>';
                    echo "<br>";
                }

                echo "<span class='separator'>repositories</span><br>";
                $query = "SELECT DISTINCT repository_fulltitle, id_archoncode FROM shelfmark_view ORDER BY repository_fulltitle";
                $queryresults = mysqli_query($link, $query);
                while ($row = mysqli_fetch_assoc($queryresults)) {
                    echo '<a href="' . $archonsearch . "?_ref=" . $row['id_archoncode'] . '" target="_blank">' . $row['repository_fulltitle'] . '</a>';
                    echo "<br>";
                }
                echo "</div>";
        }

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
								$('#show_more_tr_' + field).before('<tr><td>' + lastRowNum + '</td><td><a id="a_' + v1 + '" href=' + address + '/entity/' + v1 + '>' + short_value2 + '...</a> <a id="get_' + v1 + '" onclick="getFullText(' + v1 + ')">(More)</a><input type="hidden" id="full_' + v1 + '" value="' + v2 + '" /><input type="hidden" id="short_' + v1 + '" value="' + short_value2 + '" /></td><td>' + v3 + '<td></tr>');
							} else {
								$('#show_more_tr_' + field).before('<tr><td>' + lastRowNum + '</td><td><a id="a_' + v1 + '" href=' + address + '/entity/' + v1 + '>' + v2 + '</a></td><td>' + v3 + '<td></tr>');
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
		</script>
</html>

