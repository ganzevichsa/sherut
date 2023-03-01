{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success bg-green" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-md btn-default start" disabled>
                <span class="button-content">
                <i class="glyph-icon icon-upload"></i>
                Start
                </span>
                </button>
            {% } %}
            {% if (!i) { %}
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
