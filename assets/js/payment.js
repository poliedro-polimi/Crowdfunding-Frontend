var BACKEND_URL = 'https://poliedropolimi.pythonanywhere.com/paypal';

paypal.Button.render({
    env: 'sandbox',
    locale: 'it_IT',
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
