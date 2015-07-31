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
    $query3 = "SELECT field_title, field_url, field_column, field_returnedvariables FROM field WHERE field_url ILIKE '$field'";
//    echo $query3;
    $query3result = pg_query($query3);
    $row = pg_fetch_object($query3result);
    $column = $row->field_column;
//    echo $column;
    $variables = $row->field_returnedvariables;

    // search 'where'?
    $query4 = "SELECT index, fk_catalogue, fk_repository FROM index WHERE index_url ILIKE '$index'";    
    $query4result = pg_query($query4);
    $row = pg_fetch_object($query4result);
    $repository = $row->fk_repository;
    $catalogue = $row->fk_catalogue;

    //search how?
    if ($exact == "e") {
        $search = "= '$term'";
    } else {
        $search = "ILIKE '%$term%'";
    }

    // make the SQL search string
    $query5 = "SELECT DISTINCT $variables FROM search_view WHERE ($column $search)";

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
    $query5result = pg_query($query5 . $pagination_part);

    //test to see how many rows the query returned
    $numberofresults = pg_num_rows($query5result);

    //if there are returned rows (except from all_fields) then present output

    If ($field != "all_fields")
    {
        if ($numberofresults > 0) 
        {
            $return_value = array();
            while ($row = pg_fetch_array($query5result, NULL, PGSQL_NUM)) {
                $return_value[] = $row;
            }
            echo json_encode($return_value);
        }
        else 
        {
            echo "00000";
        }
    }
?>
