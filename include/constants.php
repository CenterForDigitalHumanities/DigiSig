<?php

// Constants

# URL Root
$address = 'http://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], '/') + 1)."digisig";

# Location of medium manifestation photographs
$medium = $address . "/images/medium/";

# Location of thumb manifestation photographs
$small = $address . "/images/small/";

# Location of description photographs
$description = $address . "/images/descriptions/";

# Location of default+failure photographs
$default = $address . "/images/default/";

# Default page title
$title = "DIGISIG";

# Number of search results to display
$num_result_per_page = 100;

# National Archives page to search archon codes
$archonsearch = "http://discovery.nationalarchives.gov.uk/details/a"

?>
