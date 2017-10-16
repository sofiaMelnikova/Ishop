{% block content %}
<div class="forCostFilter blockToInlineBlock">
    <input class="form-control forCostFilterField" name="brand" placeholder="Enter brand" value="{{brand}}">
</div>

<div class="forCostFilter blockToInlineBlock">
    <input class="form-control forCostFilterField" name="color" placeholder="Enter color" value="{{color}}">
</div>

<div class="forCostFilter blockToInlineBlock">
    <input class="form-control forCostFilterField" name="size" placeholder="Enter size" value="{{size}}">
</div>

<div class="forCostFilter blockToInlineBlock">
    <input class="form-control forCostFilterField" name="gender" placeholder="Enter gender" value="{{gender}}">
</div>
{% endblock %}