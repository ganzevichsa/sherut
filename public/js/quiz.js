function changeType(self) {
    let val = $(self).val();
    if (val != '') {
        answerWithValue(self);
    }
}

function answerWithValue(self) {
    if($(self).val() != '') {
        $('.answer-single-block').hide();
        $('.answer-single-block').each(function(){
           if(parseInt($(self).val()) == $(this).attr('data-type')) {
               $(this).show();
           }
       })
    }
}


function addNewAnswer(self) {
    let answersCount = parseInt($('input[name="answers_count"]').val());
    ++answersCount;
    $('input[name="answers_count"]').val(answersCount);
    let newAnswer = $('.answer-single-block').eq(1).find('.col-md-6').eq(0).clone().html();
    $('.answer-single-block').eq(1).append('<div class="col-md-6" data-num="'+answersCount+'">'+newAnswer+'</div>');
    $('.answer-single-block .col-md-6').each(function(){
            console.log('test');
        if(parseInt($(this).attr('data-num')) == answersCount) {
            $(this).find('textarea[name="answer_1"]').text('');
            $(this).find('textarea[name="answer_1"]').attr('name','answer_'+answersCount);
        }
    })
}
