<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    {% block stylesheets %}
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/myCss.css">
    {% endblock %}
</head>
<body class="content">

<table class="table">
    <thead>
    <tr>
        <th>#</th>
        <th>Photo</th>
        <th>Product`s name</th>
        <th>Cost of one position</th>
        <th>Count</th>
        <th>Sum</th>
        <th>Dec</th>
        <th>Inc</th>
    </tr>
    </thead>
    <tbody>
    {% for product in products %}
    <tr>
        <th scope="row">1</th>
        <td><img class="card-img-top" src="{{product.picture}}" alt="Card image cap"></td>
        <td>{{product.product_name}}</td>
        <td>{{product.cost}}</td>
        <td>{{product.countInBasket}}</td>
        <td>{{product.sum}}</td>
        <td><a href="/takeToTheBasket?id={{product.id}}" type="button" class="btn btn-success">+</a></td>
        <td><a href="/deleteProductFromBasket?id={{product.id}}" type="button" class="btn btn-danger">-</a></td>
    </tr>
    {% endfor %}
    </tbody>
</table>

<a href="/createOrder" type="button" class="btn btn-success">Create order on {{resultSum}}$</a>
<a href="/catalogue" type="button" class="btn btn-primary">Catalogue</a>

</body>
</html>