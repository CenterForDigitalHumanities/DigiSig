<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>digisig/include/lightbox/css/lightbox.css">
    <?php echo "<title>" . $title . "</title>" ?>
</head>

<body>
<!--    <p>
        DigiSig is a new resource for the study of sigillography, particularly medieval 
        seals from the British Isles.
        It currently contains:
        <?php //echo "<u><b>$sealcount</b></u>" ?> seal records and
        <?php //echo "<u><b>$imagecount</b></u>" ?> images 
    </p>
    <p>
        Based at the centre for Digital Humanities at St Louis University, Missouri, 
        it aims to foster sigillographic research by linking and matching sigillographic 
        datasets and making that information available.
    </p>-->
    
    <form class="searchArea" name = "search" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="submitFormSearch()">
        <div class="searchTitle">SEARCH</div>
        <div class="searchPiece">
        <!--<p style="color: white;">Select Field:<br/></p>-->
        <select name="field"/>
            <?php
                $query = "SELECT pk_field, field_url, field_title, field_order FROM field ORDER BY field_order";
                $searchfields = mysqli_query($link, $query);
                $default_select = "";
                echo "<option value='holder' disabled>Select Field</option>"; 
                while ($row = mysqli_fetch_array($searchfields)){
                    if($row['field_url'] === "all_fields"){
                        $default_selected = "selected";
                    }
                    else{
                        $default_selected = "";
                    }
                    echo "<option value=". $row['field_url'] . " ".$default_selected.">" . $row['field_title'] . "</option>"; 
                }
            ?>
            </select>
        </div>
        <!--<p style="color: white;">Select Index:<br/></p>-->
        <div class="searchPiece">
            <!--<p style="color: white;">Select Index:<br/></p>-->
            <?php
    //            $query13 = "SELECT pk_index, a_index, index_order, index_url FROM tb_index ORDER BY index_order";
    //            $searchindex = mysqli_query($link, $query13);
    //            $array = array();
    //            $all2 = [];
    //            while ($row = mysqli_fetch_array($searchindex)){
    //                $array[] = $row;
    //            }
    //            for ($i = 0; $i < count($array); $i++) {
    //                $all2[] = implode(',', $array[$i]);
    //              }
    //            foreach ($all2 as $aa) {
    //                print "an index:  ".$aa . " !<br/>\n";
    //            }
            ?>
            <select name="index"/>
                <?php
                    $query1 = "SELECT pk_index, a_index, index_order, index_url FROM tb_index ORDER BY index_order";
                    $searchindex = mysqli_query($link, $query1);
                    $default_selected = "";
                    $all_indexes = "<option value='all' selected><b>All Indexes</b></option>";
                    echo "<option value='holder' disabled>Select Index</option>";
                    echo $all_indexes;
                    $query2 = "SELECT pk_index, a_index, index_order, index_url, fk_catalogue FROM tb_index WHERE fk_catalogue > 0 ORDER BY index_order";
                    $searchindex1 = mysqli_query($link, $query2);
                    echo '<option value= "catalogue" disabled>Catalogue</option>';
                    while ($row = mysqli_fetch_array($searchindex1)){
                        echo "<option value=".$row['index_url'] . ">" . $row['a_index'] . "</option>";               
                        }
                    $query3 = "SELECT pk_index, a_index, index_order, index_url, fk_repository FROM tb_index WHERE fk_repository > 0 ORDER BY index_order";
                    $searchindex2 = mysqli_query($link, $query3);
                    echo '<option value= "catalogue" disabled>Repository</option>';
                    while ($row = mysqli_fetch_array($searchindex2)){
                        echo "<option value=".$row['index_url'] . ">" . $row['a_index'] . "</option>";
                    }
            ?>
        </select>
        </div>
        <div class="searchPiece">
            <span style="color: white; margin-left: 10px; display: inline-block;">Search Terms:</span>
            <input id="search_term_" type='text' size ="20" maxlength="40" value="<?php if(isset($term)){echo str_replace("_", "/", $term);} ?>"/>
            <input type="hidden" id="search_term" name="term" />
            <p style="display: inline-block; color: white;">Exact Match?</p>
            <input type="checkbox" title="Please note that this method is case sensitive." name="exact"/>
        </div>
        <div class="searchPiece">
            <div class="searchBtn_1" onclick="$(this).next().click();">GO</div>
            <input class="searchBtn_2" type="submit" name ="submit" value ="SEARCH"/>
        </div>
    </form>
    
    
</body>
</html>
<script>
    function submitFormSearch(){
        document.getElementById('search_term').value = document.getElementById('search_term_').value;
        var searchTerm = document.getElementById('search_term').value;
//        alert(searchTerm.replace('/', '_'));
        document.getElementById('search_term').value = searchTerm.replace('/', '_');
    }
</script>
