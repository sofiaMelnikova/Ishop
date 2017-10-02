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
{% if editProduct is defined %}
    <form enctype="multipart/form-data" action="/saveChangeProduct" method="post">
        <input type="hidden" name="stokeId" value="{{product.id}}">
        <div><img class="photoProduct" src="{{product.picture}}"></div>

        <div class="form-group">
            <label for="exampleFormControlFile1">Choose new photo</label>
            <input name="photo" type="file" class="form-control-file" id="exampleFormControlFile1">
        </div>
{% else %}
        <form enctype="multipart/form-data" action="http://127.0.0.1/addGood" method="post">
            <div><img src="pictures/addPhoto.png"></div>
            <input name="photo" type="file" />
{% endif %}


    <div class="form-group">
        <label class="mr-sm-2" for="inlineFormCustomSelect">You add Shoes</label>
        <input class="form-control" name="kind" value="shoes" type="hidden">
    </div>

    <div class="form-group">
        <label>Product`s name</label>
        <input class="form-control" name="productName" placeholder="Enter product`s name" value= {% if product.product_name is defined %} {{ product.product_name }} {% else %} {{ product.productName }} {% endif %}>
    </div>

    <div class="form-group">
        <label>Brand</label>
        <input class="form-control" name="brand" placeholder="Enter brand" value={{ product.brand }}>
    </div>

    <div class="form-group">
        <label>Color</label>
        <input class="form-control" name="color" placeholder="Enter color" value = {% if product.color %} {{ product.color }} null {% endif %}>
    </div>

    <div class="form-group">
        <label>Size</label>
        <input class="form-control" type="number" min="{{properties.size.min}}" max="{{properties.size.max}}" name="size" placeholder="Enter size" value={{ product.size }}>
    </div>

    <div class="form-group">
        <label>Material</label>
        <input class="form-control" name="material" placeholder="Enter material" value={{ product.material }}>
    </div>

    <div class="col-auto">
        <label class="mr-sm-2" for="inlineFormCustomSelect">Gender</label>
        <select name="gender" class="form-control" id="exampleFormControlSelect1">
            <option></option>
            <option value="man" {% if product.gender == 'man' %} selected {% endif %}>man</option>
            <option value="woman" {% if product.gender == 'woman' %} selected {% endif %}>woman</option>
        </select>
    </div>

    <div class="form-group">
        <label>Made in</label>
        <input class="form-control" name="producer" placeholder="Enter producer" value={{ product.producer }}>
    </div>

    <div class="form-group">
        <label>Count</label>
        <input class="form-control" name="count" placeholder="Enter count" value={{ product.count }}>
    </div>

    <div class="form-group">
        <label>Cost</label>
        <input class="form-control" name="cost" placeholder="Enter cost" value={{ product.cost }}>
    </div>

    {% if editProduct is defined %}
        <button type="submit" class="btn btn-success">Save change</button>
    {% else %}
        <button type="submit" class="btn btn-primary">Submit</button>
    {% endif %}
</form>

{% if editProduct is defined %}
    <form action="/deleteProduct" method="post" class="toInlineBlock">
        <button type="submit" class="btn btn-danger" name="id" value="{{product.id}}">Delete</button>
    </form>

    <a href="/adminGoods" type="button" class="btn btn-primary">For admin goods list</a>
    <a href="/catalogue" type="button" class="btn btn-primary">For uer catalog</a>
{% else %}
    <a href="/adminGoods" type="button" class="btn btn-primary">For admin goods list</a>
    <a href="/catalogue" type="button" class="btn btn-primary">For uer catalog</a>
{% endif %}

{{error}}

</body>
</html>