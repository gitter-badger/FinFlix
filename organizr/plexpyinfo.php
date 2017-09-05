<?php
ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL | E_STRICT);
require_once('user.php');
qualifyUser("admin", true);

function plexpyUsers($break = null){
    if($break){ $break = "<br/>";}else{ $break = ''; }
    $allLibs = implode(', ', libraryList()['libraries']);
    $totalUsers = count(libraryList()['users']);
    $startCount = 1;
    $output = 'USER_LIBRARIES = {';
    foreach (libraryList()['users'] as $user => $id){
        if($startCount == $totalUsers){ $comma = ""; }else{ $comma = ", ";}
        $output .= $id.': ['.$allLibs.']'.$comma.$break;
        $startCount++;
    }
    $output .= '}';
    return $output;
}

echo plexpyUsers(true);