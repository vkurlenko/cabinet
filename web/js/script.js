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
        $('.product-img-main img').attr({
            'src':  $(this).data('origin'),
            'data-origin': $(this).data('origin'),
            'data-imgid': $(this).data('imgid')
        });

        $('.product-img-other img').removeClass('active');
        $(this).addClass('active');

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
                    if($('.product-img-main img').data('imgid') == remove_id){
                        var next_img = $('.product-img-other div:first-child img');
                        console.log(next_img.length);
                        if(next_img.length == 0){
                            $('.product-img-main img').remove();
                        }
                        else{
                            $('.product-img-main img').attr({
                                'src':  $(next_img).data('origin'),
                                'data-origin': $(next_img).data('origin'),
                                'data-imgid': $(next_img).data('imgid')
                            });
                        }

                        //
                    }
                }
            },
            error: function(msg){
                console.log(msg);
            }
        });

        return false;
    })
    /* галерея картинок произвольного продукта */

    /* выбор диапазона дат */
    $('input[name="dates"]').daterangepicker(
        {
            "locale": {
                "format": "YYYY-MM-DD",
                "separator": " - ",
                "applyLabel": "Выбрать",
                "cancelLabel": "Отмена",
                "fromLabel": "С",
                "toLabel": "По",
                "customRangeLabel": "Custom",
                "weekLabel": "W",
                "daysOfWeek": [
                    "Вс",
                    "Пн",
                    "Вт",
                    "Ср",
                    "Чт",
                    "Пт",
                    "Сб"
                ],
                "monthNames": [
                    "Январь",
                    "Февраль",
                    "Март",
                    "Апрель",
                    "Май",
                    "Июнь",
                    "Июль",
                    "Август",
                    "Сентябрь",
                    "Октябрь",
                    "Ноябрь",
                    "Декабрь"
                ],
                "firstDay": 1
            },
            "opens": "left",
            "autoApply": false,
            "parentEl": ".orders-nav"
        }
    );

    $('.date-range a').on('click', function()
    {
        $('input[name="dates"]').click();
        return false;
    })


    $('input[name="dates"]').on('apply.daterangepicker', function(ev, picker) {
        document.location.href = '/orders/index?daterange='+$('input[name="dates"]').val();
    });
    /* /выбор диапазона дат */

    $('#fill-field .field-orders-fill').each(function(){
        $(this).append('<span class="del-fill">X</span>');
    })

    $('.del-fill').on('click', function(){
        console.log('del');
        $(this).parents('.field-orders-fill').eq(0).remove();
    })

    $('.add-fill').on('click', function(){
        //$(this).prepend('html');
        $(".field-orders-fill").eq(0).clone().addClass('added').appendTo("#fill-field");
        $(".added:last-child select").prop('selectedIndex',0);
        return false;
    })

    $('#load-pic').on('click', function(){
        $('#orders-images').trigger('click');
    })
})