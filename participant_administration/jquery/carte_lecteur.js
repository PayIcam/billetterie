$(document).ready(function()
{
    function add_alert(message, alert_type="danger")
    {
        var message_displayed = '<div class="alert alert-'+alert_type+' alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Attention ! </strong>' + message + '</div>'
        $("#alerts").append(message_displayed);
    }

    window.JCAPPUCINO_APPLET =  'ws://localhost:9191/events';
    var login = $('input[name=login]').val();
    var $search_input = $('input[id=recherche], input[name="bracelet_identification"]');
    var $badgeuse_indicator = $('#badgeuse_indicator');
    var $on_off = $('#on_off');
    var service = {
        callback: {}
    };

    service.connect = function() {
        var ws = new WebSocket(JCAPPUCINO_APPLET);

        var handle = function(event, message) {
            for(i in service.callback[event]) {
                service.callback[event][i](message);
            }
        }

        ws.onopen = function() {
            console.log('onopen');
            handle("onopen", "");
            $badgeuse_indicator.attr('title', 'Connexion au lecteur de carte : établie');
            $on_off.removeClass('badge-warning').addClass('badge-success').text('ON');
        };

        ws.onerror = function() {
            console.log('onerror');
            handle("onerror", "");
            $badgeuse_indicator.attr('title', 'Connexion au lecteur de carte : Erreur');
            $on_off.removeClass('badge-success').addClass('badge-warning').text('ERROR');
        };

        ws.onclose = function() {
            console.log('onclose');
            handle("onclose", "");
            $badgeuse_indicator.attr('title', 'Connexion au lecteur de carte : Non établie');
            $on_off.removeClass('badge-success').addClass('badge-warning').text('OFF');
        };

        ws.onmessage = function(message) {
            console.log('onmessage');
            var data = message.data.split(':');
            var event = data[0], data = data[1];
            handle(event, data);
            $badgeuse_indicator.removeClass('has-warning').removeClass('has-error').addClass('has-success').attr('title', 'Connexion au lecteur de carte : établie');
        };

        service.ws = ws;
    }

    service.send = function(event, message) {
        service.ws.send(event + ':' + message);
    }

    service.subscribe = function(event, callback) {
        if(!service.callback[event]) {
            service.callback[event] = [];
        }
        service.callback[event].push(callback);
    }

    service.connect();

    function badge_success_check(data) {
        if(data != null) {
            add_alert('Le badge a déjà été affecté à ' + data, 'danger');
        }
    }

    // -------------------- Vérification & Réception badge_ic -------------------- //

    service.subscribe("cardInserted", function(badge_id) {
        console.log('badge_id : '+badge_id);
        $search_input.val(badge_id).keyUp().animate({
            backgroundColor: "#d9edf7",
            borderColor: "#31708F",
            color: "#31708f",
        }, 500 ).animate({
            backgroundColor: "#fff",
            borderColor: "#ccc",
            color: "#555",
        }, 500 );
    });
});