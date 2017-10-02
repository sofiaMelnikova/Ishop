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
{% if products is defined %}
    <table class="table">
        <thead>
        <tr>
            <th>Order`s number</th>
            <th>Photo</th>
            <th>Product`s name</th>
            <th>Cost</th>
            <th>Data</th>
        </tr>
        </thead>
        <tbody>
        {% for product in products %}
        <tr>
            <th scope="row">{{product.orders_id}}</th>
            <td><img class="photoProduct" src="/{{product.picture}}"></td>
            <td>{{product.product_name}}</td>
            <td>{{product.actual_cost}}</td>
            <td>{{product.executed_at}}</td>
        </tr>
        {% endfor %}
        </tbody>
    </table>
{% else %}
<form action="/historyOfOrders" method="get">
    <div class="form-group">
        <label>Phone</label>
        <input class="form-control" name="phone" placeholder="Enter your phone number">
    </div>
    <button class="btn btn-info">Show history</button>
</form>
{% endif %}
<a href="/catalogue" type="button" class="btn btn-primary">Catalogue</a>
{{ error }}
</body>
</html>