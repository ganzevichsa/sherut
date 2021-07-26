$(document).ready(function(){
    $('.createClassicRow').on('submit',function(e){
        e.preventDefault();

        var form_array = $(this).serializeArray(); // use serializeArray
        form_array.push({
            name: '_token',
            value: $(this).attr('data-token')
        });
        var self = this;
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: form_array,
            response: 'json',
            success: function(data) {
                if($(self).attr('data-main') == 'true') {
                    location.reload();
                }else{
                    $($(self).attr('data-list')).append('<option value="'+data.id+'">'+data.name+'</option>');
                    $($(self).attr('data-list')).trigger("chosen:updated");
                    $($(self).attr('data-modal')).modal('hide');
                }
            },
            error: function(data) {
                let message = '';
                $.each(data.responseJSON.errors,function(key,value){
                    message += value;
                })
                alert(message);
            }
        })
    })
    if($('select[name="category_id"]').val() != '') {
        onChangeList('#categoriesList','#subcategoriesList');
    }
    if($('select[name="area_id"]').val() != '') {
        onChangeList('#areasList','#citiesList');
    }
    if($('select[name="organization_id"]').val() != '') {
        onChangeList('#organizationsList','#hrsList');
    }

    $('.last_date_for_registration').datepicker();
});
function menuTab(self) {
    $(self).closest('.nav-tabs').find('li').removeClass('active');
    $(self).parent('li').addClass('active');
    $('.tab-content .tab-pane').removeClass('in active');
    $('.tab-content '+$(self).attr('href')).addClass('in active');
}
function createRow(modal,url, list, main, route, edit, parent, val) {
    if(edit) {
        $(modal).find('form').attr('action',url+"/"+edit);
        $(modal).find('form').append('<input type="hidden" name="_method" value="PUT">')
        $.ajax({
            url: url+"/"+edit,
            type: 'GET',
            success: function(data) {
                if (typeof data.data !== 'undefined') {
                    data = data.data
                }
                console.log(data);
                $(modal).find('form').find('input[name="name"]').val(data.name);
                $(modal).find('form').find('input[name="value"]').val(data.value);
                // Добавляем два новых поля, priority && min_value
                $(modal).find('form').find('input[name="priority"]').val(data.priority);
                $(modal).find('form').find('input[name="min_value"]').val(data.min_value);
                $(modal).find('form').find('textarea[name="text"]').val(data.text);

                if(data.video_url) {
                    $(modal).find('form').find('input[name="video_url"]').val(data.video_url);
                }
                if(data.images) {
                    $(modal).find('.img-block').html('');
                    $.each(data.images, function(key, value){
                        $(modal).find('.img-block').append('<div class="img-block-item"><img src="/storage/categories/'+value.image+'" height="200"><a href="javascript:;" style="color:red" data-id="'+value.id+'" onclick="removeCategoryImage(this)"><i class="glyph-icon icon-remove"></i></a></div>');
                    });
                    $(modal).find('.img-block').show();
                }
                if(parent) {
                    $(modal).find('form').find('select[name="'+parent+'"]').val(data[parent]);
                    $(modal).find('form').find('select[name="'+parent+'"]').trigger("chosen:updated");
                }
            }
        })
    }else {
        $(modal).find('form').attr('action',url);
        $(modal).find('form').find('input[name="_method"]').remove();
    }
    $(modal).find('form').attr('data-modal',modal);
    $(modal).find('form').attr('data-list',list);
    $(modal).find('form').attr('data-main',main);
    $(modal).find('form').attr('data-route',route);
    $(modal).modal('show');
}

function removeCategoryImage(self) {
    let id = $(self).attr('data-id');
    $.ajax({
        url: 'categories/removeFile/'+id,
        type: 'POST',
        data: {"_token": $('.createClassicRow').attr('data-token')},
        success: function() {
            $(self).closest('.img-block-item').remove();
        }
    })
}

function onChangeList(list, subList) {
    if ($(list).val() != '') {
        $.ajax({
            url: $(list).attr('data-url'),
            type: 'GET',
            data: {id: $(list).val()},
            success: function(data) {
                $(subList).html('');
                $.each(data, function(key, value){
                    if($(subList).attr('data-old') != ''){
                        if(jQuery.isArray(JSON.parse($(subList).attr('data-old')))) {
                            if ($.inArray(value.id.toString(), JSON.parse($(subList).attr('data-old'))) >  -1 || $.inArray(value.id, JSON.parse($(subList).attr('data-old'))) >  -1) {
                                $(subList).append('<option selected value="'+value.id+'">'+value.name+'</option>');
                            }else {
                                $(subList).append('<option value="'+value.id+'">'+value.name+'</option>');
                            }
                        }else {
                            if($(subList).attr('data-old') == value.id) {
                                $(subList).append('<option selected value="'+value.id+'">'+value.name+'</option>');
                            }else{
                                $(subList).append('<option value="'+value.id+'">'+value.name+'</option>');
                            }
                        }
                    }else{
                        $(subList).append('<option value="'+value.id+'">'+value.name+'</option>');
                    }
                });
                $(subList).trigger("chosen:updated");
            }
        })
    }
}

function midrashaBlock(self, id) {
    $('.midrasha-block').hide();
    if ($(self).val() == id) {
        $('.midrasha-block').show();
    }
}

function addNewInput(self) {
    $(self).closest('div').find('.inputs').append("<input type='text' name='"+$(self).closest('div').find('.inputs input').eq(0).attr('name')+"' class='form-control'>");
}

function removeImage(self) {
    if(confirm('האם אתה בטוח?')){
        $.ajax({
            url: $(self).attr('data-url'),
            type: 'GET',
            success: function() {
                $(self).closest('div').remove();
            }
        })
    }
}

function jobChecked(self) {
    var checked = 0;
    if($(self).prop("checked") == true){
        checked = 1;
    }
    $.ajax({
        url: $(self).attr('data-url'),
        data: {checked: checked},
    })
}

