<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    {% block stylesheets %}
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/myCss.css">
    {% endblock %}
</head>

<body class="content">
    <form enctype="multipart/form-data" action="/saveUserProfile" method="post">
        <input type="hidden" name="csrfToken" value="{{csrfToken}}">

        <div class="forAvatar"><img class="forAvatar" src="{{user.avatar}}"></div>
        <input name="avatar" type="file" />

        <div class="form-group">
            <label>Your Full name</label>
            <input class="form-control" name="fio" placeholder="Enter your full name" value="{{user.fio}}">
        </div>

        <div class="form-group">
            <label class="mr-sm-2" for="inlineFormCustomSelect">Your login (e-mail):</label>
            <input class="form-control" name="login" placeholder="Enter e-mail" value="{{user.login}}">
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input class="form-control" name="phone" placeholder="Enter phone" value="{{user.phone}}">
        </div>

        <button type="submit" class="btn btn-info" name="id" value="{{user.id}}">Save</button>
        <a href="/catalogue" type="button" class="btn btn-primary">Catalog</a>
    </form>

{% for error in errors %}
    <div>{{error}}</div>
{% endfor %}

</body>
</html>