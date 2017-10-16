{% block content %}
<div class="forCostFilter blockToInlineBlock">
    <input class="form-control forCostFilterField" name="color" placeholder="Enter color" value="{{color}}">
</div>

<div class="forCostFilter blockToInlineBlock">
    <input class="form-control forCostFilterField" name="minLength" placeholder="Enter start length" value="{{minLength}}">
    <input class="form-control forCostFilterField" name="maxLength" placeholder="Enter final length" value="{{maxLength}}">
</div>

<div class="forCostFilter blockToInlineBlock">
    <input class="form-control forCostFilterField" name="minWidth" placeholder="Enter start width" value="{{minWidth}}">
    <input class="form-control forCostFilterField" name="maxWidth" placeholder="Enter final width" value="{{maxWidth}}">
</div>
{% endblock %}