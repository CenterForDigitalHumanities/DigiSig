<?php

//Tabulate query result
function queryResult($field, $index, $term, $address, $exact, $offset, $limit) {
    $num_result_per_page = 100;
    $table_text_len = 100;
    $link = mysqli_connect('localhost:3306', 'root', '1229@Oxford', 'digisigres');
    // $link = mysqli_connect('localhost:3306', 'digisig', '1EMeeIIINnn', 'digisigres');
    $pagination_part = ' limit ' . $limit . ' offset ' . $offset;
    // search 'what' and 'from'? 
    $query3 = "SELECT field_title, field_url, field_column, field_returnedvariables FROM field WHERE field_url = '$field'";
    $query3result = mysqli_query($link, $query3);
    $row = mysqli_fetch_object($query3result);
    $column = $row->field_column;
    $variables = $row->field_returnedvariables;
    
    if('' != $column or null != $column)
    {
        // search 'where'?
        $query4 = "SELECT a_index, fk_catalogue, fk_repository FROM tb_index WHERE index_url = '$index'";    
        $query4result = mysqli_query($link, $query4);
        $row = mysqli_fetch_object($query4result);
        $repository = $row->fk_repository;
        $catalogue = $row->fk_catalogue;
    
        //search how?
        if ($exact == "e") {
            $search = "= '$term'";
        }
        else {
            $search = "LIKE '%$term%'";
        }
    
        // make the SQL search string
        $query5 = "SELECT DISTINCT $variables FROM search_view WHERE ($column $search)";
    
    
        // Searching by *both* repository and catalogue is not supported -- choose one
        if ($repository > 0) {
            $query5 = $query5 . " AND (fk_repository = '$repository')";
        }
    
        if ($catalogue > 0) {
            $query5 = $query5 . " AND (fk_catalogue = '$catalogue')";
        }   
        $query5 = $query5 . " ORDER BY $column";

        // the full search string applied
        $query5result = mysqli_query($link, $query5.$pagination_part);
    
        //test to see how many rows the query returned
        $numberofresults = mysqli_num_rows($query5result);
        //get total amout of results
        $query5count_result = mysqli_query($link, $query5);
        $count = mysqli_num_rows($query5count_result);
    
        //if there are returned rows (except from all_fields) then present output
    
        If ($field != "all_fields") {
            $field_str = ucfirst($field);
            If ($numberofresults > 0) {
                //drawing the results in a tabular form
                $rowcount = 1;
                $addAsCard = "<input type='checkbox' onchange='cardMe($(this), false, false);' />";
                if($count < 5){
                    $addAsCard = "";
                    echo '<div class="theCards_body">';
                    echo '<div class="resultWrap">';
                    echo "<span class='resultCount'>$count</span>";
                    if ($numberofresults > 1) {
                        echo " results found for <span class='resultTerm'>" . $term ."</span>";
                    }
                    else {
                        echo " result found for <span class='resultTerm'>" . $term ."</span>";
                    }
                    echo " in <span class='resultTerm'>" . $field_str ."</span></div>";
                }
                else{
                    echo '<div class="tableWrap">';
                    echo '<div class="resultWrap_2">';
                    echo "<span class='resultCount'>$count</span>";
                    if ($numberofresults > 1) {
                        echo " results found for <span class='resultTerm'>" . $term ."</span>";
                    }
                    else {
                        echo " result found for <span class='resultTerm'>" . $term ."</span>";
                    }
                    echo " in <span class='resultTerm'>" . $field_str ."</span></div>";
                    echo '<table class="metaTable maxmin"><thead><th>&#x2714;</th><th>#</th><th>'.$field_str.'</th><th>Reference</th></thead><tbody>';
                }
                while ($row = mysqli_fetch_array($query5result)){
                    $value1 = $row[0];
                    $value2 = $row[1];
                    $value3 = $row[2];
                    
                    if($value1 == ""){
                        $value1 = "<i>empty</i>";
                    }
                    if($value2 == ""){
                        $value2 = "<i>empty</i>";
                    }
                    if($value3 == ""){
                        $value3 = "<i>empty</i>";
                    }
                    
                    if($numberofresults < 5){
                        echo '<div class="card"><label><input type="checkbox" onchange="cardMe($(this), false, true);"/> Add To Slider </label>';
                        echo '<div class="cardNum">#'.$addAsCard . $rowcount .'</div>';
                        if(isset($value2)){
                            if(strlen($value2) >= $table_text_len){
                                $short_value2 = substr($value2, 0, $table_text_len);
                                echo '<div class="cardInfo"><span class="cardInfoKey">'.$field_str.': </span><span class="cardInfoVal">'
                                    . '<a id="a_'.$value1.'" href=' . $address . '/entity/'.$value1.'>'. $short_value2 . '...</a> <a id="get_'.$value1.'" onclick="getFullText('.$value1.')">(More)</a><input type="hidden" id="full_'.$value1.'" value="'.$value2.'" /><input type="hidden" id="short_'.$value1.'" value="'.$short_value2.'" /></span></div>';
                                echo '<div class="cardInfo"><span class="cardInfoKey">Reference: </span> <span class="cardInfoVal">'.$value3.'</span></div>';
                            }else{
                                echo '<div class="cardInfo"><span class="cardInfoKey">'.$field_str.': </span>'
                                        . '<span class="cardInfoVal"><a id="a_'.$value1.'" href=' . $address . '/entity/'.$value1.'>'.$value2.'</a></span></div>';
                                echo '<div class="cardInfo"><span class="cardInfoKey">Reference: </span> <span class="cardInfoVal">'.$value3.'</span></div>';
                            }
                        }
                        echo "</div>";
                    }
                    else{
                        echo '<tr><td>'.$addAsCard.'</td><td>'. $rowcount . '</td>';
                        if(strlen($value2) >= $table_text_len){
                            $short_value2 = substr($value2, 0, $table_text_len);
                            echo '<td><a id="a_'.$value1.'" href=' . $address . '/entity/'.$value1.'>'. $short_value2 . '...</a> <a id="get_'.$value1.'" onclick="getFullText('.$value1.')">(More)</a><input type="hidden" id="full_'.$value1.'" value="'.$value2.'" /><input type="hidden" id="short_'.$value1.'" value="'.$short_value2.'" /></td><td>'. $value3. '</td></tr>';
                        }else{
                            echo '<td><a id="a_'.$value1.'" href=' . $address . '/entity/'.$value1.'>'.$value2.'</a></td><td>'. $value3. '</td></tr>';
                        }
                        
                    }
                    
            
                    $rowcount++;
                }

                if($count < 5){
                    echo "</div>";
                }
                else{
                    if($numberofresults < $count ){
                    echo '<tr id="show_more_tr_'.$field.'" last_row_num='.$rowcount--.'><td colspan="3"><input type="button" id="show_more_btn_'.$field.'" value="Show More" offset='.($num_result_per_page+1).' onclick=\'getNextData("'.$field.'", "'.$index.'", "'.$term.'", "'.$address.'", "'.$exact.'", '.$limit.')\' /><span id="load_next_pending_'.$field.'" style="display:none">Loading...</span></td></tr></table></div>';    
                    }
                    else{
                        echo '</table></div>';
                    }
                }
                
            }
            else {echo "<div class='tableWrap'><div class='resultWrap'><span class='resultCount'>0</span> results in <span class='resultTerm'>" . $field_str . "</span></div></div>";}
        }
    }
}



function queryview($entity, $id) {
     $link = mysqli_connect('localhost:3306', 'root', '1229@Oxford', 'digisigres');
    //$link = mysqli_connect('localhost:3306', 'digisig', '1EMeeIIINnn', 'digisigres');

     //convert view number to view text string and find out what variables to return
    $query6 = "SELECT entity_view_short, entity_column_short, entity_returnedvariables_short, entity_url FROM entity WHERE entity_url = '$entity'";
    $query6result = mysqli_query($link, $query6);
    $row = mysqli_fetch_object($query6result);
    $column = $row->entity_column_short;
    $view = $row->entity_view_short;
    $variables = $row->entity_returnedvariables_short;
    
    // the basic search string
    $query7 = "SELECT $variables FROM $view WHERE $column = $id";
    
    $queryviewresult = mysqli_query($link, $query7);
    return $query7;
}



#this function outputs a table listing seal descriptions
// the function can omit one description -- flagged by the $duplicate value

function sealdescription ($query12result, $address, $duplicate) {
    $count = mysqli_num_rows($query12result);
    
    $rowcount = 1;
    $addAsCard = "<input type='checkbox' onchange='cardMe($(this), false, false);' />";
    if($count < 5){
        $addAsCard = "";
        echo '<div class="theCards_body indent">';
        
    }
    else{
        echo '<div class="tableWrap"><table class="metaTable indent"><thead><th>#</th><th>Name</th><th>Reference</th><th>Seal Description</th></thead><tbody>';
    }
    while ($row = mysqli_fetch_array($query12result)) {
        $value1 = $row['a_index'];
        $value2 = $row['sealdescription_identifier'];
        $value3 = $row['id_sealdescription'];
        $value4 = $row['realizer'];
        if (isset($duplicate) && $value3 != $duplicate) { 
            if($count < 5){
                echo '<div class="card"><label><input type="checkbox" onchange="cardMe($(this), false, true);"/> Add To Slider </label>';
                echo '<div class="cardNum"># '. $addAsCard . $rowcount .'</div>';
                if(isset($value4) && $value4!==""){
                    echo '<div class="cardInfo"><span class="cardInfoKey">Name: </span> <span class="cardInfoVal">'.$value4.'</span></div>';
                }
                if(isset($value1) && $value1!==""){
                    echo '<div class="cardInfo"><span class="cardInfoKey">Reference: </span> <span class="cardInfoVal">'.$value1.'</span></div>';
                }
                if(isset($value3) && $value3!=="" && isset($value2) && $value2!==""){
                   echo '<div class="cardInfo"><span class="cardInfoKey">Seal Description: </span> <span class="cardInfoVal"><a href="' . $address . '/entity/' . $value3. '">' . $value2 . '</a></span></div>';
                }
                echo "</div>";
            }
            else{
                echo '<tr><td> '. $addAsCard . $rowcount . '</td>';
                echo '<td>' . $value4 . '</td>';
                echo '<td>' . $value1 . '</td>';
                echo '<td><a href="' . $address . '/entity/' . $value3. '">' . $value2 . '</a></td></tr>';
            }
            $rowcount++;
        }
    }
    if($count < 5){
        echo "</div>";
    }
    else{
        echo "</tbody></table></div>";
    }
}

?>