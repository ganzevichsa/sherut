{% for (var i=0, file; file=o.files[i]; i++) { %}
<tr class="template-download fade">
    <td>
    <span class="preview">
    {% if (file.thumbnailUrl) { %}
<a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
    {% } %}
</span>
</td>
<td>
<p class="name">
    {% if (file.url) { %}
<a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
    {% } else { %}
<span>{%=file.name%}</span>
    {% } %}
</p>
    {% if (file.error) { %}
    <div><span class="label label-danger">Error</span> {%=file.error%}</div>
    {% } %}
</td>
<td>
<span class="size">{%=o.formatFileSize(file.size)%}</span>
</td>
<td>
{% if (file.deleteUrl) { %}
<button class="btn btn-md btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
<span class="button-content">
    <i class="glyph-icon icon-trash"></i>
    Delete
    </span>
    </button>
    <input type="checkbox" name="delete" value="1" class="toggle width-reset float-left">
    {% } else { %}
<button class="btn btn-md btn-warning cancel">
    <span class="button-content">
    <i class="glyph-icon icon-ban-circle"></i>
    Cancel
    </span>
    </button>
    {% } %}
</td>
</tr>
    {% } %}
