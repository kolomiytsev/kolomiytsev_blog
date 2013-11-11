<?php

$template = file_get_contents('main.tpl.php');

$sql = "SELECT id, name FROM page";
$res = $db->query($sql);

$menu = "<h4 style=\"display:inline; margin-right: 50px;\"><a href=\"/\">Главная страница</a></h4>";
while ($menuData = $res->fetch_assoc()) {
    $menu .= "<h4 style=\"display:inline; margin-right: 50px;\"><a href=\"/page/" . $menuData['id'] . "\">" . $menuData['name'] . "</a></h4>";
}
$menu .= "<h4 style=\"display:inline; margin-right: 50px;\"><a href=\"/blog\">Блог (:</a></h4>";

$replace = array(
    '{MENU}' => $menu,
    '{CONTENT}' => $content,
    '{CATEGORY}' => $category,
    '{BOTTOM_CONTENT}' => $bottomContent,
);

$template = str_replace(array_keys($replace), $replace, $template);

echo $template;
