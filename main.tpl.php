<html>
<head>
<link href="style.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
    function response(name) {
        window.location = "#comment";
        document.getElementById('commentBody').innerHTML = name + ', ';
    }
</script>
</head>
<body>
    <div style="margin: 0 auto; width:900px;">{MENU}</div>
    <hr>
    <div style="width:200px; float:right;">{CATEGORY}</div>
    <div style="margin: 0 auto; width: 700px;">{CONTENT}</div>
    <div style="margin: 0 auto; width: 700px;">{BOTTOM_CONTENT}</div>
</body>
</html>
