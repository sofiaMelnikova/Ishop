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
<body>
<div class="bs-example" data-example-id="contextual-panels">
    <div><img class="photoProduct" src="{{product.picture}}"></div>

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Product`s name</h3>
        </div>
        <div class="panel-body">{{product.product_name}}</div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Kind</h3>
        </div>
        <div class="panel-body">{{product.kinds_value}}</div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Color</h3>
        </div>
        <div class="panel-body">{{product.color}}</div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Material</h3>
        </div>
        <div class="panel-body">{{product.material}}</div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Length</h3>
        </div>
        <div class="panel-body">{{product.length}}</div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Width</h3>
        </div>
        <div class="panel-body">{{product.width}}</div>
    </div>

    {% if product.producer %}
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Made in</h3>
        </div>
        <div class="panel-body">{{product.producer}}</div>
    </div>
    {% endif %}

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Count</h3>
        </div>
        <div class="panel-body">{{product.count}}</div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Price</h3>
        </div>
        <div class="panel-body">{{product.cost}}</div>
    </div>
    <a href="/catalogue" type="button" class="btn btn-info">For uer catalog</a>
</div>
</body>
</html>