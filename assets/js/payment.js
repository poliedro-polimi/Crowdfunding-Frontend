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
            $("#tshirt_data").show().find("input select").prop("disabled", false);
        }
        else{
            $("#tshirt_data").hide().find("input select").prop("disabled", true);
        }

        if(!$("#reward0").prop("checked")) {
            $("#location_data").show().find("input select").prop("disabled", false);
        }
        else{
            $("#location_data").hide().find("input select").prop("disabled", true);
        }

        $(".qty input").val(0);

        var $t = $(this);
        var $qty = $t.closest(".form-inline").find(".qty input");
        if($qty.length>0) {
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

    $("#email, #email2").change(function(ev){
        var $e1 = $("#email");
        var $e2 = $("#email2");
        if($e1.val()==$e2.val()){
            $e1.closest(".form-group").removeClass("has-error").addClass("has-success");
            $e2.closest(".form-group").removeClass("has-error").addClass("has-success");
        }
        else{
            $e1.closest(".form-group").removeClass("has-success").addClass("has-error");
            $e2.closest(".form-group").removeClass("has-success").addClass("has-error");
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
        payment: function(data, actions){
            return $.ajax({
                url: BACKEND_URL+'/create',
                method: 'post',
                crossDomain: true,
                contentType: 'application/json; charset=UTF-8',
                dataType: 'json',
                jsonp: false,
                data: JSON.stringify({
                    donation: 2,
                    stretch_goal: 0,
                    items: 0,
                    shirts: [],
                    notes: '',
                    lang: 'it',
                    reference: {
                        firstname: 'Tester',
                        lastname: 'Testing',
                        email: 'info-buy@poliedro-polimi.it',
                        phone: '3342752215'
                    }
                })
            }).then(function(data) {
                if(typeof(data.paymentID)!='undefined') {
                    return data.paymentID;
                }
                else{
                    return null;//Just guessing...PayPal Documentation on error handling is null
                }
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
                    lang: 'it',
                    paymentID: data.paymentID,
                    payerID: data.payerID
                })
            }).then(function(){
                //TODO redirect?
            });
        },
        onCancel: function(data, actions){

        },
        onError: function(data, actions){

        }
    }, '#pay-button');

});

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
    $("#handle-label span").text(value+"€");
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
        }
        else{
            $t.prop("disabled", true);
            if($t.prop("checked")){
                $t.prop("checked", false);
            }
        }
    });
}
