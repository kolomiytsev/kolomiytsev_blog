<?php
include("config.php");

$categoryId = !empty($_GET['category']) ? (int)$_GET['category'] : null;
$postId = !empty($_GET['post']) ? (int)$_GET['post'] : null;
$page = !empty($_GET['p']) ? (int)$_GET['p'] : FIRST_PAGE;

//

$sql = "SELECT id, name
        FROM category";
$res = $db->query($sql);

$category = "<h3>Категории</h3>";

while ($row = $res->fetch_assoc()) {
    $category .= "<h5>" . '<a href="/blog/category/' . $row['id'] . '">' . $row['name'] . "</a>" . "</h5>";
}

//

if (empty($postId)) {
    $sql = "SELECT p.id post_id, p.name post_name, p.body post_body, c.id category_id, c.name category_name 
        FROM post p
        JOIN category c ON c.id = p.category_id";
    $sqlCount = "SELECT COUNT(id) count FROM post";
    if (!empty($categoryId)) {
        $sql .= " WHERE c.id =" . $categoryId;
        $sqlCount .= " WHERE category_id=" . $categoryId;
    }

    $postCount = $db->query($sqlCount)->fetch_assoc();
    $pageLimit = ceil($postCount['count'] / COUNT_POST_ON_PAGE);

    $offset = ($page - 1) * COUNT_POST_ON_PAGE;
    $sql .= " LIMIT " . $offset . ',' . COUNT_POST_ON_PAGE;
    
    $res = $db->query($sql);
    
    $content = "";
    while ($row = $res->fetch_assoc()) {
        $content .= "<h2>" . '<a href="/blog/post/' . $row['post_id'] . '">' . $row['category_name'] . ' > ' . $row['post_name'] . "</a></h2>";
        $content .= "<div style=\"margin: 0 auto; width: 650px;\">" . $row['post_body'] . "</div>";
    }

    // Пагинация

    $bottomContent = '';
    if ($pageLimit > 1) {
        if ($page > $pageLimit) {
            $page = FIRST_PAGE;
        }
        $paginationPageList = "";
        $paginationNavigation = "";
        $pageContainerStart = '<span style="font-size: 22px; margin-right: 22px;">';
        $pageContainerEnd = '</span>';
        $pageValue = "";
        
        for ($pageCount = 1; $pageCount <= $pageLimit; $pageCount++) {

            if ($pageCount == $page) {
                $pageValue = $pageCount;
            } else {
                $pageValue = '<a href="?p=' . $pageCount .'">' . $pageCount . '</a>';
            }
            $paginationPageList .= $pageContainerStart . $pageValue . $pageContainerEnd;
            if ($pageCount == $page) {
                $nextPage = $pageCount + 1;
                $prevPage = $pageCount - 1;
                $paginationNavigation .= '<div>';
                if ($pageCount == 1) {
                    $pageValue = $pageContainerStart . '<a href="?p=' . $nextPage . '">Туда-></a>' . $pageContainerEnd;
                } elseif ($pageCount == $pageLimit) {
                    $pageValue = $pageContainerStart . '<a href="?p=' . $prevPage . '"><-Сюда</a>' . $pageContainerEnd;
                } else {
                    $pageValue = $pageContainerStart . '<a href="?p=' . $prevPage . '"><-Сюда</a>' . ' <-_' . $pageContainerEnd;
                    $pageValue .= $pageContainerStart . '_-> ' . '<a href="?p=' . $nextPage . '">Туда-></a>' . $pageContainerEnd;
                }
                $paginationNavigation .= $pageContainerStart . $pageValue . $pageContainerEnd;
                $paginationNavigation .= '</div>';
            }
        }
        $bottomContent .= $paginationPageList . $paginationNavigation;
    }

} else {
    $sql = "SELECT p.name post_name, p.body post_body, p.cdate post_cdate, c.id category_id, c.name category_name 
            FROM post p
            JOIN category c ON c.id = p.category_id
            WHERE p.id =" . $postId;
    $row = $db->query($sql)->fetch_assoc(); 

    $content = "<h1>" . $row['category_name'] . " > " . $row['post_name'] . "</h1>\n
        <div style=\"margin: 0 auto; width: 700px;\">" . $row['post_body'] . "</div>\n
        <div style=\"margin: 0 auto; text-align: right; width: 700px;\">" . "<strong>" . $row['post_cdate'] . "</strong>" . "</div>";
    
    //
    
    $commentTitle = "<h3>Комментарии</h3>";
    $errorMessage = "";
    $errors = array();
    
    if (!empty($_POST['comment'])) {
        $name = !empty($_POST['name']) ? preg_replace('#\s+#', ' ', strip_tags(trim($_POST['name']))) : null;
        $email = !empty($_POST['email']) ? preg_replace('#\s+#', ' ', strip_tags(trim($_POST['email']))) : null;
        $body = !empty($_POST['body']) ? preg_replace('#\s+#m', ' ', strip_tags(trim($_POST['body']))) : null;

        if (empty($name)) {
            $errors[] = "Имя введите, будьте столь любезны.";
        }
        if (empty($email)) {
            $errors[] = "Почту введите, пожалуйста.";
        } 
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "У вас ошибка при вводе Почты произошла, проверьте, пожалуйста.";
        }
        if (empty($body)) {
            $errors[] = "Комментарий напишите, пожалуйста.";
        }
        if (preg_match('#http://#i', $name) || preg_match('#http://#im', $body)) {
            $errors[] = "Публикация ссылок запрещена.";
        }
        if (empty($errors)) {
            $sql = "INSERT INTO comment(name, email, body, cdate, post_id) 
                    VALUES(" . "'" . $name . "'" . ", " . "'" . $email . "'" . ", " . "'". $body . "'" . ", NOW(), " . "'" . $postId . "'" . ")";
            $res = $db->query($sql);

            if (empty($res)) {
                $errors[] = "Неведомая ранее ошибочка сер.";
            } else {
                $mailMessage = $name . "\n" . $email . "\n" . $body . "\n";
                mail(ADMIN_EMAIL, "Комментарий", $mailMessage, "From: kolomiytsev.net\r\n" . "Reply-To: mail@kolomiytsev.net\r\n");

                $name = null;
                $email = null;
                $body = null;
            }
        } 
        if (!empty($errors)) {
            $errorMessage = '<div><p>' . implode("</p><p>", $errors) . '</p></div>';
        }
    }
    
    $commentForm = '<div style="width: 700px; margin: 0 auto;">
        <h4 id="comment">Будь мужиком, напиши коммент!</h4>
        <form method="POST">
        <p>Имя</p>
        <input type="text" name="name" value="' . (!empty($name) ? $name : "") . '"/>
        <p>Электронная почта</p>
        <input type="text" name="email" value="' . (!empty($email) ? $email : "") . '"/>
        <p>Комментарий</p><textarea id="commentBody" name="body">' . (!empty($body) ? $body : "") . '</textarea>
        <div style="padding-top: 10px;">
        <button type="submit" name="comment" value="Ok">Жми!</button>
        </div>
        </form>
        </div>';
    
    $sql = "SELECT name, email, body, cdate
            FROM comment
            WHERE post_id=" . $postId;
    $res = $db->query($sql);
    
    $commentList = "";
    if (mysqli_num_rows($res)) {
        while ($row = $res->fetch_assoc()) {
            $commentList .= '<div style="border: 1px solid gray; padding: 15px; margin-bottom: 20px;">
                <p> <strong>' . $row['name'] . "</strong> - " . $row['cdate'] . "</p>
                <p>" . $row['email']  . "</p>
                <p> <em>" . $row['body'] . "</em> </p>
                <button type=\"submit\" name=\"response\" value=\"Response\" onClick=\"response('" . $row['name'] . "')\">Ответить</button>
                </div>";
        }
    } else {
        $commentList .= "Комментариев пока нет.";
    }
    $bottomContent = $commentTitle . $errorMessage . $commentForm . $commentList;
}

include("render.php");
?>
