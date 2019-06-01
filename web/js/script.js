var ipay = new IPAY({api_token: 'bdgaei7palileg54u4l475o92i'});

$(document).ready(function(){
    $('.btn-pay').on('click', function(e){

        e.preventDefault();

        var check = $('.agree').is(':checked');

        if(check)
            location.href = $(this).data('url');
        else
            return false;
    })

    /* галерея картинок произвольного продукта */
    $('.product-img-other img').on('click', function(){
        $('.product-img-main img').attr('src', $(this).data('origin'));

        var imgid = $(this).data('imgid');
        var modelid = $(this).data('modelid');

        $.ajax({
            type: "POST",
            url: '/orders/set-main-img',
            data: "imgid="+imgid+'&modelid='+modelid,
            success: function(msg){
                console.log(msg);
            },
            error: function(msg){
                console.log(msg);
            }
        });
    })

    // удаление картинки
    $('.product-img-del').on('click', function(){
        var remove_id = $(this).data('imgid');
        var model_id = $(this).data('modelid');

        $.ajax({
            type: "POST",
            url: '/orders/remove-img',
            data: "imgid="+remove_id+'&modelid='+model_id,
            success: function(msg){
                console.log(msg);
                if(msg == 1){
                    $('#img-'+remove_id).remove()
                }
            },
            error: function(msg){
                console.log(msg);
            }
        });

        return false;
    })


    /* галерея картинок произвольного продукта */


})