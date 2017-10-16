<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<form class="form-horizontal" action="/restoringPassword" method="post">

    <input type="hidden" name="csrfToken" value="{{csrfToken}}">

    <fieldset>

        <!-- Form Name -->
        <legend>Check in</legend>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="email">Email</label>
            <div class="col-md-4">
                <input id="email" name="email" type="text" placeholder="email" class="form-control input-md" required="">
            </div>
        </div>

        <!-- Button -->
        <div class="form-group">
            <label class="col-md-4 control-label" for="submit"></label>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">SUBMIT</button>
            </div>
        </div>

    </fieldset>
</form>

<div>{{error}}</div>

</body>
</html>