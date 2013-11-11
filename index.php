<?php
include("config.php");

$pageId = !empty($_GET['page']) ? (int)$_GET['page'] : DEFAULT_PAGE;

$sql = "SELECT name, body
        FROM page
        WHERE id =" . $pageId;
$res = $db->query($sql)->fetch_assoc();

$content = "";
$content .= "<h1>" . $res['name'] . "</h1>";
$content .= "<div style=\"margin: 0 auto; width: 700px;\">" . $res['body'] . "</div>";
$category = "";
$bottomContent ="";

include("render.php");
?>
