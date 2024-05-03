<!doctype html>
<html lang="en-us" dir="ltr">
    <head>
        <title><?php
        
        if(!empty($page_title)) {
            echo($page_title . " - " . $site_title);
        } else {
            echo($site_title);
        }
        
        ?></title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="/main.css" type="text/css" charset="utf-8">
    </head>
    <body>
        <header>
            <h1><a href="index.php"><?php echo $site_title; ?></a></h1>
        </header>
    