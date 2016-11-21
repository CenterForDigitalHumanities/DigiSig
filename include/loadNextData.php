<?php
#connection details
require_once("../config/config.php");
    $field = $_POST['field'];
    $index = $_POST['index'];
    $term = $_POST['term'];
    $address = $_POST['address'];
    $exact = $_POST['exact'];
    $offset = $_POST['offset'];
    $limit = $_POST['limit'];
    $pagination_part = ' limit ' . $limit . ' offset ' . $offset;
    // search 'what' and 'from'? 
    $query3 = "SELECT field_title, field_url, field_column, field_returnedvariables FROM field WHERE field_url = '$field'";
//    echo $query3;
    $query3result = mysqli_query($link, $query3);
    $row = mysqli_fetch_object($query3result);
    $column = $row->field_column;
//    echo $column;
    $variables = $row->field_returnedvariables;

    // search 'where'?
    $query4 = "SELECT a_index, fk_catalogue, fk_repository FROM tb_index WHERE index_url = '$index'";
    $query4result = mysqli_query($link, $query4);
    $row = mysqli_fetch_object($query4result);
    $repository = $row->fk_repository;
    $catalogue = $row->fk_catalogue;

    //search how?
    if ($exact == "e") {
        $search = "= '$term'";
    } else {
        $search = "LIKE '%$term%'";
    }

    //echo nl2br("Get these variables from search_view table... ".  $variables ." \n");
    // make the SQL search string
    $query5 = "SELECT DISTINCT $variables FROM search_view WHERE ($column $search)";
    echo(nl2br($query5."\n"));

    // Searching by *both* repository and catalogue is not supported -- choose one
    if ($repository > 0) {
        $query5 = $query5 . " AND (fk_repository = $repository)";
    }

    if ($catalogue > 0) {
        $query5 = $query5 . " AND (fk_catalogue = $catalogue)";
    }

    //and the ordering variable
    $query5 = $query5 . " ORDER BY $column";
    
    // the full search string applied
    $query5result = mysqli_query($link, $query5 . $pagination_part);
    

    //test to see how many rows the query returned
    $numberofresults = mysqli_num_rows($query5result);

    //if there are returned rows (except from all_fields) then present output

    //echo "FIELD IS " . $field;
    //echo "number of results is ".$numberofresults;
    $i = 0;

    If ($field != "all_fields")
    {
        if ($numberofresults > 0) 
        {

            $return_value = array();

            while ($row = mysqli_fetch_array($query5result)) {
                $return_value[] = $row;
            }
            $return_value = json_encode($return_value);
            if(json_last_error() == JSON_ERROR_NONE){
                echo $return_value;
            }
            else{
                echo "0000" + json_last_error();
            }
        }
        else 
        {
            echo "00000";
        }
    }
    else{
        echo "00000";
    }
?>
