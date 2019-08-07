var ipay = new IPAY({api_token: 'bdgaei7palileg54u4l475o92i'});

function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            console.log($(input).data('target'))
            $('.'+$(input).data('target')).attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }
}


$(document).ready(function(){
    $(".load").change(function(){
        readURL(this);
    });

    $('.btn-pay').on('click', function(e){

        e.preventDefault();

        var check = $('.agree').is(':checked');
        console.log('check='+check);

        if(check)
            location.href = $(this).data('url');
        else{
            alert('Для продолжения оплаты необходимо согласиться с договором оферты');
            return false;
        }
    })

    /* галерея картинок произвольного продукта */
    $('.product-img-other-view img, .product-img-other img').on('click', function(){
        $('.product-img-main img').attr({
            'src':  $(this).data('origin'),
            'data-origin': $(this).data('origin'),
            'data-imgid': $(this).data('imgid')
        });

        $('.product-img-other-view img, .product-img-other img').removeClass('active');
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
                    console.log(remove_id + '==' + $('.product-img-main img').data('imgid'));
                    $('#img-'+remove_id).remove()
                    if($('.product-img-main img').data('imgid') == remove_id){
                        $('.product-img-main img').remove();
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

    /* начинки */
    $('#fill-field .field-orders-fill').each(function(){
        $(this).append('<span class="del-fill">X</span>');
    })

    $('#fill-field').on('click', '.del-fill', function(){
        if($('.del-fill').length > 1){
            console.log($('.del-fill').length);
            $(this).parents('.field-orders-fill').eq(0).remove();
        }
    })


    $('.add-fill').on('click', function(){
        $(".field-orders-fill").eq(0).clone().addClass('added').appendTo("#fill-field");
        $(".added:last-child select").prop('selectedIndex',0);
        return false;
    })
    /* /начинки */

    $('#load-pic').on('click', function(){
        $('#orders-images').trigger('click');
    })

    /* checkbox на согласие */
    $('.tort2016_ch').click(function(){

        $(this).toggleClass('checked');

        if($(this).hasClass('checked')){
            $('.agree').attr('checked', true);
            $('.agree').attr('value', 1);
        }
        else{
            $('.agree').attr('checked', false);
            $('.agree').attr('value', 0);
        }
    })
    /* /checkbox на согласие */

})