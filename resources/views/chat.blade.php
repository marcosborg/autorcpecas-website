<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Auto RC Peças AI</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .card-header {
            background-color: #700000;
            color: white;
            font-weight: bold;
            border-radius: 0 !important;
        }

        .card-footer {
            background-color: #700000;
            color: white;
            border-radius: 0 !important;
        }

        button.btn.btn-success {
            background-color: maroon;
            width: 100%;
        }

        button.btn.btn-success:active {
            background-color: crimson;
            width: 100%;
        }

        .card-footer {
            padding: 0 !important;
            background: transparent;
        }

        textarea#message-textarea {
            border: none;
        }

        .chat {
            background-color: #eeeeee;
            border: solid 1px;
            border-color: #cccccc;
            border-radius: 0;
            padding: 5px 10px;
            display: inline-block;
        }

        .client {
            background-color: #dddddd;
            border: solid 1px;
            border-color: #cccccc;
            border-radius: 0;
            padding: 5px 10px;
            display: inline-block;
            text-align: right;
        }

        .line-chat {
            display: flex;
            justify-content: flex-start;
            margin: 10px 0;
        }

        .line-client {
            display: flex;
            justify-content: flex-end;
            margin: 10px 0;
        }

        .message {
            font-size: small;
        }

        .card-body {
            overflow-y: scroll;
            height: 370px;
            background: #fafafa;
        }
    </style>
</head>

<body>

    <div class="card rounded-0">
        <div class="card-header">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-dots"
                viewBox="0 0 16 16">
                <path
                    d="M5 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0m4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                <path
                    d="m2.165 15.803.02-.004c1.83-.363 2.948-.842 3.468-1.105A9.06 9.06 0 0 0 8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6a10.437 10.437 0 0 1-.524 2.318l-.003.011a10.722 10.722 0 0 1-.244.637c-.079.186.074.394.273.362a21.673 21.673 0 0 0 .693-.125zm.8-3.108a1 1 0 0 0-.287-.801C1.618 10.83 1 9.468 1 8c0-3.192 3.004-6 7-6s7 2.808 7 6c0 3.193-3.004 6-7 6a8.06 8.06 0 0 1-2.088-.272 1 1 0 0 0-.711.074c-.387.196-1.24.57-2.634.893a10.97 10.97 0 0 0 .398-2" />
            </svg> Chat online
        </div>
        <div class="card-body" id="chat-container"></div>
        <div class="card-footer">
            <textarea class="form-control" id="message-textarea" placeholder="Escreva uma mensagem"></textarea>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js">
    </script>
    <script>
        const chat_container = $('#chat-container');
        const message_textarea = $('#message-textarea');
        var thread_id = null;
        var conversation_id = null;
        $(() => {
            $('#message-textarea').keypress(function (e) {
                if (message_textarea.val().length > 1) {
                    if (e.which == 13) {
                        if (!thread_id) {
                            message_textarea.LoadingOverlay('show');
                            addMessageToLog ('user', message_textarea.val()).then((resp) => {
                                conversation_id = resp.id;
                                startConversation().then((resp) => {
                                    message_textarea.LoadingOverlay('hide');
                                    thread_id = resp.data[0].thread_id;
                                    addMessageToLog ('assistant', resp.data[0].content[0].text.value).then(() => {
                                        addMessageToContent('assistant', resp.data[0].content[0].text.value);
                                    });
                                });
                            });
                        } else {
                            message_textarea.LoadingOverlay('show');
                            addMessageToLog ('user', message_textarea.val()).then(() => {
                                sendMessage().then((resp) => {
                                    message_textarea.LoadingOverlay('hide');
                                    addMessageToLog ('assistant', resp.data[0].content[0].text.value).then(() => {
                                        addMessageToContent('assistant', resp.data[0].content[0].text.value);
                                    });
                                });
                            });
                        }
                    }
                }
            });
        });
        startConversation = async () => {
            let data = {
                message: message_textarea.val()
            }
            addMessageToContent('user', message_textarea.val());
            message_textarea.val('');
            return new Promise((resolve, reject) => {
                $.post({
                    url: '/api/chat/start-conversation',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    success: (resp) => {
                        resolve(resp);
                    },
                    error: (err) => {
                        reject(err);
                    }
                });
            });
        }
        sendMessage = async () => {
            let data = {
                message: message_textarea.val(),
                thread_id: thread_id
            }
            addMessageToContent('user', message_textarea.val());
            message_textarea.val('');
            return new Promise((resolve, reject) => {
                $.post({
                    url: '/api/chat/send-message',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    success: (resp) => {
                        resolve(resp);
                    },
                    error: (err) => {
                        reject(err);
                    }
                });
            });
        }
        addMessageToContent = (role, message) => {
            switch (role) {
                case 'assistant':
                    html = '<div class="line-chat">';
                    html += '<div class="chat">';
                    html += '<div class="message">';
                    html += '<small>Assistente virtual</small><br>';
                    html += message;
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    break;
                default:
                    html = '<div class="line-client">';
                    html += '<div class="client">';
                    html += '<div class="message">';
                    html += '<small>Eu</small><br>';
                    html += message;
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    break;
            }
            chat_container.append(html);
            chat_container.scrollTop(chat_container[0].scrollHeight);
        }
        addMessageToLog = async (role, message) => {
            let data = {
                message: message,
                conversation_id: conversation_id,
                role: role
            }
            return new Promise((resolve, reject) => {
                $.post({
                    url: '/api/chat/add-message-to-log',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    success: (resp) => {
                        resolve(resp);
                    },
                    error: (err) => {
                        reject(err);
                    }
                });
            });
        }
    </script>

</body>

</html>