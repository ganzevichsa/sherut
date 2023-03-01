function addManagerRow() {
    let count = parseInt($('.manager-block').attr('data-count'));
    count++;
    $('.manager-block').append(
        '<div class="row manager-block-item" data-id="'+count+'">' +
            '<div class="col-md-3">' +
                '<input type="text" class="form-control" name="manager_name_'+count+'" value="" placeholder="Manager Name">' +
            '</div>' +
            '<div class="col-md-3">' +
                '<input type="text" class="form-control" name="manager_phone_'+count+'" value="" placeholder="Phone Name">' +
            '</div>' +
        '</div>');
    $('.manager-block').attr('data-count',count);
    $('input[name="managers_count"]').val(count);
}
