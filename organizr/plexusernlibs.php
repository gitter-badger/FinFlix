<?php
ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL | E_STRICT);
require_once('user.php');
qualifyUser("admin", true);
echo prettyPrint(libraryList());