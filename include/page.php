<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/dsr/include/lightbox/css/lightbox.css">
    <?php echo "<title>" . $title . "</title>" ?>
</head>

<body>
    
<p>DigiSig</p>
<p>Digital Sigillography Resource</p>
<p>version: Alpha</p>
<form name ="navigate" action="<?php $_server['PHP_SElF'] ?>" method="post">
    <p>
        <input type="submit" name ="submit" value ="HOME"/>
        <input type="submit" name ="submit" value ="ABOUT"/>
        <input type="submit" name ="submit" value ="ADVANCED SEARCH"/>
        <input type="submit" name ="submit" value ="CONTACT"/>
    </p>
</form>

DigiSig is a new resource for the study of sigillography, particularly medieval 
seals from the British Isles.

It currently contains:
<?php echo $sealcount ?> seal records
0 images

Based at the centre for Digital Humanities at St Louis University, Missouri, 
it aims to foster sigillographic research by linking and matching sigillographic 
datasets and making that information available.

<form name = "search" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="submitFormSearch()">
    <p>Select Field:<br/>
        <select name="field"/>
        <?php
            $query = "SELECT pk_field, field_url, field_title, field_order FROM field ORDER BY field_order";
            $searchfields = pg_query($query);
            while ($row = pg_fetch_assoc($searchfields)){
                echo "<option value=". $row[field_url] . ">" . $row[field_title] . "</option>"; 
            }
        ?>
        </select>
        </p>
    <p>Select Index:<br/>
        <select name="index"/>
        <?php
            $query2 = "SELECT pk_index, index, index_order, index_url FROM index ORDER BY index_order";
            $searchindex = pg_query($query2);
            while ($row = pg_fetch_assoc($searchindex)){
                echo "<option value=".$row[index_url] . ">" . $row[index] . "</option>";               
                }
        ?>
        </select>
        </p>
    <p>Search Terms:<br />
        <input id="search_term_" type='text' size ="20" maxlength="40" value="<?php echo str_replace("_", "/", $term); ?>"/>
           
        <input type="hidden" id="search_term" name="term" />

    Exact Match?
        <input type="checkbox" title="Please note that this method is case sensitive." name="exact"/></p> 

    <input type="submit" name ="submit" value ="SEARCH"/>
        
    </p>
</form>
<script>
    function submitFormSearch(){
        document.getElementById('search_term').value = document.getElementById('search_term_').value;
        var searchTerm = document.getElementById('search_term').value;
//        alert(searchTerm.replace('/', '_'));
        document.getElementById('search_term').value = searchTerm.replace('/', '_');
    }
</script>