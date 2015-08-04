<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/DigiSig/include/lightbox/css/lightbox.css">
    <?php echo "<title>" . $title . "</title>" ?>
</head>

<body>
    <p>
        DigiSig is a new resource for the study of sigillography, particularly medieval 
        seals from the British Isles.
        It currently contains:
        <?php echo "<u><b>$sealcount</b></u>" ?> seal records and
        <?php echo "<u><b>$imagecount</b></u>" ?> images 
    </p>
    <p>
        Based at the centre for Digital Humanities at St Louis University, Missouri, 
        it aims to foster sigillographic research by linking and matching sigillographic 
        datasets and making that information available.
    </p>
    <form class="searchArea" name = "search" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="submitFormSearch()">
        <div class="searchTitle">Search</div>
        <p style="color: white;">Select Field:<br/></p>
        <select name="field"/>
        <?php
            $query = "SELECT pk_field, field_url, field_title, field_order FROM field ORDER BY field_order";
            $searchfields = mysqli_query($link, $query);
            while ($row = mysqli_fetch_array($searchfields)){
                echo "<option value=". $row['field_url'] . ">" . $row['field_title'] . "</option>"; 
            }
        ?>
        </select>
        <p style="color: white;">Select Index:<br/></p>
        <select name="index"/>
            <?php
                $query1 = "SELECT pk_index, a_index, index_order, index_url FROM tb_index ORDER BY index_order";
                $searchindex = mysqli_query($link, $query1);
                //This query returns a blank, which is where the errors for this page are coming from
                echo "Trying to get the SEARCH INDEX ======================== ".$searchindex." !!!!! ";
                while ($row = mysqli_fetch_array($searchindex)){
                    echo "<option value=".$row['index_url'] . ">" . $row['a_index'] . "</option>";               
                    }
                    echo '<option value= "catalogue" disabled>Catalogue</option>';    
                
                $query2 = "SELECT pk_index, a_index, index_order, index_url, fk_catalogue FROM tb_index WHERE fk_catalogue > 0 ORDER BY index_order";
                $searchindex = mysqli_query($link, $query2);
                while ($row = mysqli_fetch_array($searchindex)){
                    echo "<option value=".$row['index_url'] . ">" . $row['a_index'] . "</option>";               
                    }

                echo '<option value= "repository" disabled>Repository</option>';

                $query3 = "SELECT pk_index, a_index, index_order, index_url, fk_repository FROM tb_index WHERE fk_repository > 0 ORDER BY index_order";
                $searchindex = mysqli_query($link, $query3);
                while ($row = mysqli_fetch_array($searchindex)){
                    echo "<option value=".$row['index_url'] . ">" . $row['a_index'] . "</option>";
                    }
            ?>
        </select>
        <p style="color: white;">Search Terms:</p>
        <input id="search_term_" type='text' size ="20" maxlength="40" value="<?php if(isset($term)){echo str_replace("_", "/", $term);} ?>"/>
        <input type="hidden" id="search_term" name="term" />
        <p style="display: inline-block; color: white;">Exact Match?</p>
        <input type="checkbox" title="Please note that this method is case sensitive." name="exact"/><br>
        <input style="margin-left: 10px;" type="submit" name ="submit" value ="SEARCH"/>
    </form>
    
<script>
    function submitFormSearch(){
        document.getElementById('search_term').value = document.getElementById('search_term_').value;
        var searchTerm = document.getElementById('search_term').value;
//        alert(searchTerm.replace('/', '_'));
        document.getElementById('search_term').value = searchTerm.replace('/', '_');
    }
</script>