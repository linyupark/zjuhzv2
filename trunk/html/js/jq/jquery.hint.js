jQuery.hint = function(){
    $('.hint').each(function(i){
        $(this).mouseover(function(){
            var offset = $(this).offset();
            var txt = $(this).attr('tooltip');
            $('body').append('<div id="hint_div" style="position:absolute;left:'+offset.left+'px;top:'+(offset.top+30)+'px">'+txt+'</div>');
            $('#hint_div').css({'opacity':0,'padding':'5px','border':'1px solid #ccc','background':'#ffc'});
            $('#hint_div').fadeTo(500,0.8);
        });
        $(this).mouseout(function(){
            $('#hint_div').remove();
        });
    });
}