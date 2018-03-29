var BACKEND_URL = 'https://poliedropolimi.pythonanywhere.com/paypal';

$(function(){

    $("#donation_slider").slider({
        min: 0,
        max: 15,
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
            $qty.prop("disabled", false);
            $qty.val(Math.floor($("#amount").val() / $t.data("threshold")));
            $qty.change();
        }
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

    $("#email, #email2").change(validate_emails);

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
        payment: function(data, actions){
            return new paypal.Promise(function(resolve, reject) {
                $.ajax({
                    lang: backendLang,
                    url: BACKEND_URL + '/create',
                    method: 'post',
                    crossDomain: true,
                    contentType: 'application/json; charset=UTF-8',
                    dataType: 'json',
                    jsonp: false,
                    data: JSON.stringify(build_pay_data())
                }).done(function (data, tstatus, xhr) {
                    resolve(data.paymentID);
                }).fail(function(xhr, tstatus, error){
                    var resp = JSON.parse(xhr.responseText);
                    backendErrorHandler(resp);
                    reject(new Error(resp.message));
                });
            });

            /*
            if(form_is_valid()) {
                return $.ajax({
                    lang: backendLang,
                    url: BACKEND_URL + '/create',
                    method: 'post',
                    crossDomain: true,
                    contentType: 'application/json; charset=UTF-8',
                    dataType: 'json',
                    jsonp: false,
                    data: JSON.stringify(build_pay_data())
                }).then(function (data, tstatus, xhr) {
                    return data.paymentID;
                }, function(xhr, tstatus, error){
                    throw JSON.parse(xhr.responseText);
                });
            }
            else{
                if(backendLang=='it'){
                    throw {message: "Verifica gli errori nel modulo di donazione"};
                }
                else if (backendLang=='en'){
                    throw {message: "Check the errors in the form"};
                }
            }*/
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
            }).then(function(){
                alert("Success!!!");
            }, function(xhr){
                throw JSON.parse(xhr.responseText);
            });
        },
        onCancel: function(data, actions){
            console.log(data);
            console.log(actions);
        },
        onError: function(data, actions){
            console.log(typeof data);
            console.log(data);
            console.log(data.message);
            backendErrorHandler(data);
        }
    }, '#pay-button');

});

function backendErrorHandler(response){
    $("#error_box").text(response.message);
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
            type: $t.find('input[name=shirt-type]').val()
        });
    });

    return output;
}

function form_is_valid(){
    var ret = true;
    var $nome = $('#nome');
    if($.trim($nome.val())==''){
        $nome.closest('.form-group').addClass("has-error");
        ret = ret && false;
    }
    else{
        $nome.closest('.form-group').removeClass("has-error");
    }

    var $cognome = $('#cognome');
    if($.trim($cognome.val())==''){
        $cognome.closest('.form-group').addClass("has-error");
        ret = ret && false;
    }
    else{
        $cognome.closest('.form-group').removeClass("has-error");
    }

    ret = validate_emails() && ret;

    var $tel = $('#tel');
    if($.trim($tel.val())==''){
        $tel.closest('.form-group').addClass("has-error");
        ret = ret && false;
    }
    else{
        $tel.closest('.form-group').removeClass("has-error");
    }

    var $amt = $('#amount');
    if($.trim($amt.val())=='' || $amt.val()<=0){
        $amt.closest('.form-group').addClass("has-error");
        ret = ret && false;
    }
    else{
        $amt.closest('.form-group').removeClass("has-error");
    }

    ret = validate_rewards() && ret;

    return ret;
}

function validate_emails(){
    var $e1 = $("#email");
    var $e2 = $("#email2");
    if($e1.val()==$e2.val() && $e1.val()!=''){
        $e1.closest(".form-group").removeClass("has-error").addClass("has-success");
        $e2.closest(".form-group").removeClass("has-error").addClass("has-success");
        return true;
    }
    else{
        $e1.closest(".form-group").removeClass("has-success").addClass("has-error");
        $e2.closest(".form-group").removeClass("has-success").addClass("has-error");
        return false;
    }
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
    switch($selected.attr('id')){
        case 'reward3':
            ret = ret && validate_tshirts();
        case 'reward1':
        case 'reward2':
            ret = ret && validate_location();
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
