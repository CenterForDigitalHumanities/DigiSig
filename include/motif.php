<div class='pageWrap'>
    <form class="searchArea" name = "selectclass" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="submitFormClass()">

        <select name="field"/>
        <?php
        $query = "SELECT printphrase_class, id_class, classnotation, cases, class_sortorder FROM classification_view ORDER BY class_sortorder";
        $searchfields = mysqli_query($link, $query);
        echo "<option value='holder' disabled selected = 'true'>Select Class</option>";
        while ($row = mysqli_fetch_array($searchfields)) {
            if(preg_match('/^\d{1}$/', $row['classnotation'])){
                if($row['classnotation']>1){
                    echo "</optgroup>";
                }
                echo "<optgroup label='" . $row['printphrase_class'] . "'>";
            }
            echo "<option value=" . $row['id_class'] . ">"
                    . $row['classnotation']
                    . " "
                    . $row['printphrase_class'] 
                    . " ("
                    . $row['cases'] 
                    .")"
                    . "</option>";
        if(isset($motifClass)){
            if($row['id_class'] == $motifClass){
                $galleryTitle = $row['classnotation'];
            }
        }
            
        }
        ?>
        </select>

        <div class="searchPiece">
            <div class="searchBtn_1" onclick="$(this)
                        .next()
                        .click();">GO</div>
            <input class="searchBtn_2" type="submit" name ="submit" value ="motifs"/>
        </div>
    </form>


    <?php
    if (isset($motifClass)) {

//establish which part of the representation_view table to query
        $query1 = "SELECT pk_class, level, printphrase_class FROM classification_view WHERE id_class = '" . $motifClass . "' LIMIT 200;";
        $query1result = mysqli_query($link, $query1);
        if($query1result){
        $row = mysqli_fetch_assoc($query1result);
        $classnumber = $row['pk_class'];
        $classlevel = "level" . $row['level'];
        $classtitle = $row['printphrase_class'];

//create an array of the photographs and their metadata					
        $query2 = "SELECT * FROM representation_class_view WHERE " . $classlevel . " = " . $classnumber;
        $query2result = mysqli_query($link, $query2);
        $rowcount = mysqli_num_rows($query2result);

//Title for group of photographs					
if($rowcount==200){
        Echo "<h3>" . $classtitle . " <span class='badge' title='First " . $rowcount . " results shown'>" . $rowcount . "</span></h3>";
} else {
        Echo "<h3>" . $classtitle . " <span class='badge' title='" . $rowcount . " total results'>" . $rowcount . "</span></h3>";
}
        } else {
            echo '<p>No results; there may have been an error.</p>';
            $rowcount = 0;
        }

//if there are results then check to see if then assemble a list of the filename and establish if they are 'local'
        if ($rowcount > 0) {
            echo "<div class='galleron' title='" . $galleryTitle . "'>";
            while ($row = mysqli_fetch_array($query2result)) {

                $val1_filename = $row['representation_thumbnail'];
                $val2_thumb = $row['thumb'];

                if ($val2_thumb == "local" || $val2_thumb == null) {
                    $val3_connection = $small;
                } else
                    ($val3_connection = $val2_thumb);

                $val4_id_seal = $row['id_seal'];
                $val5_id_item = $row['id_item'];
                $val6_label = $row['repository_fulltitle'] . " " . $row['shelfmark'];

                $seal_connection = $address . "/entity/" . $val4_id_seal;

                echo "<div class='galleryDiv'><a target='_blank' href='" . $seal_connection . "' title= '" . $val6_label . "'>";
                echo "<img src='" . $val3_connection . $val1_filename . "' alt= '" . $val6_label . "'  height ='100'></a></div>";
            }
        }
        echo "</div>";
    }
    ?>
    <script>
        function submitFormClass () {
            document.getElementById('search_term').value = document.getElementById('search_term_').value;
            var searchTerm = document.getElementById('search_term').value;
            document.getElementById('search_term').value = searchTerm.replace('/', '_');
        }
    </script>

</div>