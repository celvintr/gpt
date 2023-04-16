<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ChatGPT</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <style>
       html, body{background-color:#444654;
            height: 100%; }
        .card{background-color:#343641;
        color:#fff;}

        .container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        #chat-history {
            overflow-y: scroll;
            height: 300px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color:#32343F;
            color:#000;
        }
        .message-row {
            width: 100%;
            clear: both;
        }

        .chat-message {
            border-radius: 20px;
            padding: 10px;
            margin-bottom: 10px;
            max-width: 80%;
            font-size: 14px;
            line-height: 1.4;
        }

        .user-input {
            background-color:#444654;
            color: #fff;
            text-align: left;
            float: right;
            width: 100%;
        }

        .bot-response {
            background-color:#32343F;
            color: #fff;
            text-align: left;
            float: left;
            width: 100%;
        }
        #text{
            background-color: #40414F;
            color:#fff;
        }

        .typing::after {
  content: "";
  animation: typing 1s infinite;
}

@keyframes typing {
  from {
    width: 0;
  }
  to {
    width: 100%;
  }
}

    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">ChatGPT 3</div>
                    <div class="card-body">
                        <div id="chat-history"></div>
                        <form id="chat-form" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="text">Pregunta o mensaje:</label>
                                <input type="text" name="text" id="text" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>



function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function saveChatHistory() {
    setCookie('chat_history', $('#chat-history').html(), 1);
}

        function updateScroll() {
            $('#chat-history').scrollTop($('#chat-history')[0].scrollHeight);
        }

        function typeWriter(element, text, index, callback) {
    if (index < text.length) {
        element.append(text[index]);
        updateScroll(); // Actualizar la posición del scroll

        setTimeout(function() {
            typeWriter(element, text, index + 1, callback);
        }, 50); // Puedes ajustar este valor para controlar la velocidad de escritura
    } else {
        callback();
    }
}






        $(document).ready(function() {

            var chat_history_cookie = getCookie('chat_history');
                if (chat_history_cookie) {
                    $('#chat-history').html(chat_history_cookie);
                }


            // Obtener el token CSRF
            var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Configurar la solicitud AJAX con el token CSRF
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });

            // Manejar el envío del formulario
            $('#chat-form').submit(function(event) {
                event.preventDefault();
                var input = $('#text').val();

                // Enviar la solicitud AJAX
                $.ajax({
                    url: '/chat',
                    method: 'POST',
                    data: { text: input },
                    success: function(response) {
    // Obtener la respuesta del bot
    var botResponse = response.response;

    // Agregar la entrada del usuario al historial de chat
    var user_input = '<div class="user-input"><img width="30" src="https://thumbs.dreamstime.com/z/hombre-avatar-del-friki-104871313.jpg">' + input + '</div>';
    $('#chat-history').append(user_input);

    // Crear un nuevo elemento div para la respuesta del bot
    var bot_response_element = $('<div class="bot-response"></div>');

    // Agregar la respuesta del bot al historial de chat
    $('#chat-history').append(bot_response_element);

    // Crear un elemento span para el efecto de escritura
    var response_span = $('<span class="bot-text"></span>');
    bot_response_element.append('<img width="30" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRtb_VYBDIkiE8jmdcDA1McUg5SDyaLzeJXON5ioum09DHelbxilqVsDYjk0Juj06EXwp4&usqp=CAU"> ');
    bot_response_element.append(response_span);

    // Mostrar la respuesta del bot letra por letra con el efecto de escritura
typeWriter(response_span, botResponse, 0, function() {
    // Hacer scroll hasta el final del chat
    updateScroll();

    // Guardar el historial del chat en la cookie
    saveChatHistory();
});

    $('#text').val('');
},

                    error: function() {
                        // Manejar errores de la solicitud AJAX
                        alert('Ocurrió un error al enviar el mensaje.');
                    }
                });
            });
        });
    </script>



</body>
</html>
