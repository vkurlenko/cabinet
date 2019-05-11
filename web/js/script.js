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
})