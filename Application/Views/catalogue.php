<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    {% block stylesheets %}
    <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../../css/myCss.css">
    {% endblock %}
</head>
<body>

<nav class="navbar navbar-default">
    <div class="container-fluid">
        <ul class="nav navbar-nav navbar-left">
            <li>
                <a class=" active" href="/catalogue/shoes/1">Shoes</a>
            </li>
            <li>
                <a href="/catalogue/jacket/1">Jacket</a>
            </li>
            <li>
                <a href="/catalogue/plaid/1">Plaid</a>
            </li>

        </ul>
        <ul class="nav navbar-nav navbar-right">
            {% if login %}
            <li><a href="/historyOfOrders">History of orders </a></li>
            <li><a href="/showBasket">Basket <span class="badge">{% if countProductsInBasket %}{{countProductsInBasket}}{% else %}0{% endif %}</span></a></li>

            <li>
                <form action="/logout" method="post" class="blockToInlineBlock">
                    <input type="hidden" name="csrfToken" value="{{csrfToken}}">
                    <button class="btn btn-info">Logout: {{login}}</button>
                </form>
            </li>

            {% else %}
            <li><a href="/historyOfOrders">History of orders </a></li>
            <li><a href="/showBasket">Basket <span class="badge">{% if countProductsInBasket %}{{countProductsInBasket}}{% else %}0{% endif %}</span></a></li>
            <li><a href="/login" type="button" class="btn btn-info" >Login</a></li>
            {% endif %}
        </ul>
    </div>
</nav>

<form action="/catalogue/{{kind}}/1" method="get">
    <div class="forCostFilter blockToInlineBlock">
        <input class="form-control forCostFilterField" name="minCost" placeholder="Enter start cost" value="{{minCost}}">
        <input class="form-control forCostFilterField" name="maxCost" placeholder="Enter final cost" value="{{maxCost}}">
    </div>

    {% if kind == "shoes" %}
    {{ block("content", "shoesFilter.php") }}
    {% endif %}

    {% if kind == "jacket" %}
    {{ block("content", "jacketFilter.php") }}
    {% endif %}

    {% if kind == "plaid" %}
    {{ block("content", "plaidFilter.php") }}
    {% endif %}

    <button type="submit" class="btn btn-info">Show</button>
</form>

<div class="card-deck">
    {% for product in products %}
    <div class="card">
        <img class="card-img-top" src="../../{{product.picture}}" alt="Card image cap">
        <div class="card-body">
            <h4 class="card-title">{{product.cost}}</h4>
            <a href="/product?id={{product.id}}" type="button" class="btn btn-primary" class="card-link">{{product.product_name}}</a>
            <a href="/takeToTheBasket?id={{product.id}}" type="button" class="btn btn-success" class="card-link">Add to the basket</a>
        </div>
    </div>
    {% endfor %}
</div>

<nav aria-label="Page navigation example">
    <ul class="pagination">
        {% if pages.min != 1 %}
        <li class="page-item"><a class="page-link" href="http://127.0.0.1/catalogue/{{kind}}/{{pages.min-1}}{{filters}}">Previous</a></li>
        {% endif %}

        {% for page in pages.min..pages.max %}
        <li class="page-item"><a class="page-link" href="http://127.0.0.1/catalogue/{{kind}}/{{page}}{{filters}}">{{page}}</a></li>
        {% endfor %}

        {% if pages.max < sumPages %}
        <li class="page-item"><a class="page-link" href="http://127.0.0.1/catalogue/{{kind}}/{{pages.max+1}}{{filters}}">Next</a></li>
        {% endif %}
    </ul>
</nav>

{% if admin %}
    <a href="/adminGoods" type="button" class="btn btn-primary">For admin goods list</a>
{% endif %}

{{error}}
</body>
</html>