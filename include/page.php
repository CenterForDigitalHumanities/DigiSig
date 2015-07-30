<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost:8080/DigiSig/include/lightbox/css/lightbox.css">
    <?php echo "<title>" . $title . "</title>" ?>
</head>

<body>
    <p>
        DigiSig is a new resource for the study of sigillography, particularly medieval 
        seals from the British Isles.

        It currently contains:
        <?php echo "<u><b>$sealcount</b></u>" ?> seal records
        0 images 
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
            $searchfields = pg_query($query);
            while ($row = pg_fetch_assoc($searchfields)){
                echo "<option value=". $row[field_url] . ">" . $row[field_title] . "</option>"; 
            }
        ?>
        </select>
        <p style="color: white;">Select Index:<br/></p>
        <select name="index"/>
            <?php
                $query2 = "SELECT pk_index, index, index_order, index_url FROM index ORDER BY index_order";
                $searchindex = pg_query($query2);
                while ($row = pg_fetch_assoc($searchindex)){
                    echo "<option value=".$row[index_url] . ">" . $row[index] . "</option>";               
                    }
            ?>
        </select>
        <p style="color: white;">Search Terms:</p>
        <input id="search_term_" type='text' size ="20" maxlength="40" value="<?php echo str_replace("_", "/", $term); ?>"/>
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