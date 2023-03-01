$(function () {
    "use strict";
    buildChosen();
});

function buildChosen() {
    $(".chosen-select").chosen(), $(".chosen-search").append('<i class="glyph-icon icon-search"></i>'), $(".chosen-single div").html('<i class="glyph-icon icon-caret-down"></i>')
}
