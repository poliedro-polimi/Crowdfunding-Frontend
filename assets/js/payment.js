var BACKEND_URL = 'https://poliedropolimi.pythonanywhere.com/paypal';

$(function(){

    $("#donation_slider").slider({
        min: 0,
        max: 16,
        value: initialAmount,
        slide: function(ev, ui){
            amountChange(ui.value);
        }
    });

    $("#amount").change(function(){
        amountChange($(this).val());
    });

    amountChange(initialAmount);

    $("input[name=chosenReward]").change(function(){
        if($("#reward3").prop("checked")){
            $("#tshirt_data").show();
        }
        else{
            $("#tshirt_data").hide();
        }

        if(!$("#reward0").prop("checked")) {
            $("#location_data").show();
        }
        else{
            $("#location_data").hide();
        }

        $(".qty input").val(0).prop("disabled", true);

        var $t = $(this);
        var $qty = $t.closest(".form-inline").find(".qty input");
        if($qty.length>0) {
            var maxQty = Math.floor($("#amount").val() / $t.data("threshold"));
            $qty.prop("disabled", false);
            $qty.attr("max", maxQty);
            $qty.val(maxQty);
            $qty.change();
        }

        setRequiredFields();
    });

    $(".qty input").change(function(ev){
        var $t = $(this);
        var ths = $t.closest(".form-inline").find("input[type=radio]").data("threshold");
        var qty = $t.val();
        var amt = $("#amount").val();

        if(ths*qty > amt){
            $t.val(Math.floor(amt / ths));
            $t.closest(".qty").addClass("has-warning");
            setTimeout(function(){$t.closest(".qty").removeClass("has-warning");}, 2000);
        }

        if($t.attr("id")=="qty3"){
            tshirtSection($t.val());
        }
    });

    paypal.Button.render({
        env: 'sandbox',
        locale: payPalLocale,
        commit: true,
        style: {
            color: 'gold',
            size: 'large',
            shape: 'rect',
            label: 'paypal'
        },
        validate: function(actions) {
            form_is_valid()?actions.enable():actions.disable();

            onFormChangedValue(actions);
        },
        payment: function(resolve, reject){
            return $.ajax({
                lang: backendLang,
                url: BACKEND_URL + '/create',
                method: 'post',
                crossDomain: true,
                contentType: 'application/json; charset=UTF-8',
                dataType: 'json',
                jsonp: false,
                data: JSON.stringify(build_pay_data())
            }).done(function (data) {
                resolve(data.payment_id);
            }).fail(function(xhr){
                var resp = JSON.parse(xhr.responseText);
                backendErrorHandler(resp);
                reject(new Error());
            });
        },
        onAuthorize: function(data, actions){
            return $.ajax({
                url: BACKEND_URL+'/execute',
                method: 'post',
                crossDomain: true,
                contentType: 'application/json; charset=UTF-8',
                dataType: 'json',
                jsonp: false,
                data: JSON.stringify({
                    lang: backendLang,
                    paymentID: data.paymentID,
                    payerID: data.payerID
                })
            }).then(function(data){
                window.location.href = confirmationUrl(typeof(data.donation_id)!='undefined'?data.donation_id:null, wasMailSent(data));
            }, function(xhr){
                var resp = JSON.parse(xhr.responseText);
                backendErrorHandler(resp);
            });
        },
        onCancel: function(data, actions){
            console.log(data);
            console.log(actions);
        },
        onError: function(data, actions){
            console.log(data);
            console.log(actions);
        }
    }, '#pay-button');

});

function onFormChangedValue(actions){
    $('#donation_data input, #donation_data select').change(function(){
        //Using setTimeout to give time to other event handlers on the fields to do their job
        setTimeout(function(){form_is_valid()?actions.enable():actions.disable()}, 10);
    });

    $("#donation_slider").on('slide', function(){
        setTimeout(function(){form_is_valid()?actions.enable():actions.disable()}, 10);
    });
}

function tshirtSection(qty){
    var currentSections = $(".tshirt_chooser");
    var $copy;
    var diff = qty - currentSections.length;

    if(diff > 0){
        for(var i = 0; i < diff; i++){
            $copy = currentSections.first().clone();
            $copy.find("input[type=radio]").attr("name", "shirt-type"+(currentSections.length+i)).prop("checked", false);
            $copy.find("option").prop("selected", false);
            $copy.appendTo("#tshirt_data");
        }
    }
    else if(diff < 0){
        currentSections.slice(diff).remove();
    }
}

function amountChange(value) {
    $("#handle-label").find("span").text(value+"€");
    $("#amount").val(value);
    $("#donation_slider").slider("value", value);
    $(".donation_objective").each(function(){
        if($(this).data("threshold")<=value){
            //Do things
        }
    });
    $("input[name=chosenReward]").each(function(){
        var $t=$(this);
        var thr = $t.data("threshold") || 0;
        if(thr<=value){
            $t.prop("disabled", false);
            $t.closest('label').removeClass("input_disabled");
        }
        else{
            $t.prop("disabled", true);
            $t.closest('label').addClass("input_disabled");
            $t.closest(".form-inline").find(".qty input").prop("disabled", true);
            if($t.prop("checked")){
                $t.prop("checked", false);
                $("#reward0").prop("checked", true);
            }
        }
    });
    $(".qty input:not(:disabled)").change();
}

function setRequiredFields(){
    var dynamicRequire = $("#nome, #cognome, #email, #tel");

    if($("#reward0").prop("checked")){
        dynamicRequire.siblings("label").find(".required").remove();
    }
    else{
        dynamicRequire.each(function(){
            var $label = $(this).siblings("label");
            if($label.find('.required').length==0) {
                var content = $label.html();
                var colon = content.indexOf(":");
                $label.html(content.slice(0, colon) + '<span class="required">&nbsp;*</span>' + content.slice(colon));
            }
        });
    }
}

function build_pay_data(){
    var $reward = $("input[name=chosenReward]:checked");
    var output = {
        donation: parseFloat($("#amount").val()),
        stretch_goal: parseInt($reward.val()),
        items: parseInt($reward.closest(".form-inline").find(".qty input").val() || 0),
        notes: $("#notes").val()
    };

    switch($reward.attr('id')){
        case 'reward3':
            output.shirts = build_shirts_data();
        case 'reward1':
        case 'reward2':
            output.reference = {
                firstname: $("#nome").val(),
                lastname: $("#cognome").val(),
                email: $("#email").val(),
                phone: $("#tel").val(),
                location: $("input[name=location]:checked").val()
            };
    }
    return output;
}

function build_shirts_data() {
    var output = [];
    $('.tshirt_chooser').each(function(){
        var $t = $(this);
        output.push({
            size: $t.find('select[name=shirt-size]').val(),
            type: $t.find('input:checked').val()
        });
    });

    return output;
}

function form_is_valid(){
    var ret = true;
    var ret2;

    if(!$('#reward0').prop("checked")) {//General info is optional if there is no gadget to order
        var $nome = $('#nome');
        if ($.trim($nome.val()) == '') {
            $nome.closest('.form-group').addClass("has-error");
            ret = ret && false;
        }
        else {
            $nome.closest('.form-group').removeClass("has-error");
        }

        var $cognome = $('#cognome');
        if ($.trim($cognome.val()) == '') {
            $cognome.closest('.form-group').addClass("has-error");
            ret = ret && false;
        }
        else {
            $cognome.closest('.form-group').removeClass("has-error");
        }

        var $tel = $('#tel');
        if ($.trim($tel.val()) == '') {
            $tel.closest('.form-group').addClass("has-error");
            ret = ret && false;
        }
        else {
            $tel.closest('.form-group').removeClass("has-error");
        }

        var $email = $('#email');
        if ($.trim($email.val()) == '') {
            $email.closest('.form-group').addClass("has-error");
            ret = ret && false;
        }
        else {
            $email.closest('.form-group').removeClass("has-error");
        }
    }
    else{
        $("#general_data input").closest('.form-group').removeClass("has-error");
    }

    var $amt = $('#amount');
    if($.trim($amt.val())=='' || $amt.val()<=0){
        $amt.closest('.form-group').addClass("has-error");
        ret = ret && false;
    }
    else{
        $amt.closest('.form-group').removeClass("has-error");
    }

    ret2 = validate_rewards();
    ret = ret && ret2;

    return ret;
}

function validate_rewards(){
    var $selected = $('input[name=chosenReward]:checked');
    var $qty = $selected.closest('.form-inline').find('qty input');
    var amt = $("#amount").val();

    if($selected.length==0){
        $("input[name=chosenReward]").closest(".form-group").addClass("has-error");
        return false;
    }
    else{
        $("input[name=chosenReward]").closest(".form-group").removeClass("has-error");
    }

    if($qty.val()<=0 || $selected.data('threshold') * $qty.val() <= amt){
        $qty.closest('.form-group').addClass("has-error");
        return false;
    }
    else{
        $qty.closest('.form-group').removeClass("has-error");
    }

    var ret = true;
    var ret2;
    switch($selected.attr('id')){
        case 'reward3':
            ret2 = validate_tshirts();//This is to avoid short-circuir behaviour in &&
            ret = ret && ret2;
        case 'reward1':
        case 'reward2':
            ret2 = validate_location();
            ret = ret && ret2;
            break;
    }

    return ret;
}

function validate_tshirts(){
    var ret = true;
    $('.tshirt_chooser').each(function(){
        var $t = $(this);
        if($t.find("input[type=radio]:checked").length!=1){
            ret = ret && false;
        }
        return true;
    });

    return ret;
}

function validate_location(){
    var $loc = $('[name=location]:checked');
    if($loc.length==0){
        $('input[name=location]').closest(".form-group").addClass("has-error");
        return false;
    }
    else{
        $('input[name=location]').closest(".form-group").removeClass("has-error");
        return true;
    }
}

function confirmationUrl(donation_id, mail_sent){
    return confirm_url+"?reward="+($('#reward0').prop('checked')?0:1)
      +(donation_id?'&donation='+encodeURIComponent(donation_id):'')
      +(mail_sent?'':'&mail_fail=1');
}

function wasMailSent(data){
    if(typeof(data.error) != 'undefined' && data.error.type == '_MAIL_ERROR'){
        return false;
    }
    return true
}

function backendErrorHandler(response){
    if(typeof (response.error.type) != 'undefined'){
        var msg;
        switch(response.error.type){
            case '_VALIDATION_ERROR':
                msg = validation_error_msg;
                break;
            case '_PAYPAL_ERROR':
                msg = paypal_error_msg;
                break;
            case '_APP_ERROR':
                msg = app_error_msg;
                break;
            default:
                msg = response.error.type+": "+response.error.message;
        }

        if(response.error.donation_id){
            msg += "\n"+donation_id_msg+" "+response.error.donation_id;
        }

        $("#error_box").text(msg);
    }
}
