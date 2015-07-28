<?php

//Tabulate query result
function queryResult($field, $index, $term, $address, $exact) {
    
// search 'what' and 'from'? 
$query3 = "SELECT field_title, field_url, field_column, field_returnedvariables FROM field WHERE field_url ILIKE '$field'";
$query3result = pg_query($query3);
$row = pg_fetch_object($query3result);
$column = $row->field_column;
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
}
else {
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
$query5result = pg_query($query5);

//test to see how many rows the query returned
$numberofresults = pg_num_rows($query5result);

//if there are returned rows (except from all_fields) then present output

If ($field != "all_fields") {
If ($numberofresults > 0) {
echo $numberofresults;
        if ($numberofresults > 1) {
        echo " results found for " . $term;
        }
        else {
            echo " result found for " . $term;
        }
        echo " in " . $field;
        
//drawing the results in a tabular form
echo '<table><table border = 1><tr><td></td><td>Heading</td>';
$rowcount = 1;
while ($row = pg_fetch_array($query5result, NULL, PGSQL_NUM)) {
    $value1 = $row[0];
    $value2 = $row[1];
    $value3 = $row[2];
    echo '<tr><td>' . $rowcount . '</td>';
    echo '<td><a href=' . $address . '/entity/'.$value1.'>'. $value2 . '</a></td><td>'. $value3. '<td></tr>';
    $rowcount++;
}
echo '</table>';
}
Else {echo "<p>no results in " . $field . "</p>";}
}
}



function queryview($entity, $id) {
 //convert view number to view text string and find out what variables to return
$query6 = "SELECT entity_view_short, entity_column_short, entity_returnedvariables_short, entity_url FROM entity WHERE entity_url ILIKE '$entity'";
$query6result = pg_query($query6);
$row = pg_fetch_object($query6result);
$column = $row->entity_column_short;
$view = $row->entity_view_short;
$variables = $row->entity_returnedvariables_short;

// the basic search string
$query7 = "SELECT $variables FROM $view WHERE $column = $id";

$queryviewresult = pg_query($query7);
return $query7;
}



#this function outputs a table listing seal descriptions
// the function can omit one description -- flagged by the $duplicate value

Function sealdescription ($query12result, $address, $duplicate) {
        
    echo '<table border = 1><tr><td></td><td>Name</td><td></td><td>Reference</td>';
    $rowcount = 1;

while ($row = pg_fetch_array($query12result)) {
    $value1 = $row[index];
    $value2 = $row[sealdescription_identifier];
    $value3 = $row[id_sealdescription];
    $value4 = $row[realizer];
        if ($value3 != $duplicate) { 
    echo '<tr><td>' . $rowcount . '</td>';
    echo '<td>' . $value4 . '</td>';
    echo '<td>' . $value1 . '</td>';
    echo '<td><a href=' . $address . '/entity/' . $value3. '>' . $value2 . '</a></td></tr>';
    $rowcount++;
}
}
    echo "</table><br>";
}

?>