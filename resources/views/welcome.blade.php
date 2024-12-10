<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Gatara | Test</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <!-- Styles -->
        <style>
            *, html, body {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            .gpt-chat input, .gpt-chat select, button {
                width: 300px;
                max-width: 300px;
                border: 2px solid black;
                border-radius: 6px;
                color: black;
                padding: 0 10px;
                height: 30px;
                max-height: 30px;
                margin: 10px;
            }

            button {
                cursor: pointer;
            }

            .image-recognition {
                padding: 10px;
            }

            body {
                padding-top: 50px;
            }
        </style>
    </head>
    <body>
        <div id="app">
            <div class="image-recognition">
                <input type="file" name="image" id="image" accept="image/*">
                <img style="width: 60%;" src="#" alt="">
            </div>
            <div class="gpt-chat">
                <input type="text" placeholder="Ime" name="name" id="name">
                <select name="gatara" id="gatara">
                    <option disabled selected>Odaberi gataru</option>
                    <option value="sladja">Sladja (opšte proricanje)</option>
                    <option value="ljubica">Ljubica (ljubavno proricanje)</option>
                    <option value="rada">Rada (proricanje poslovnih prilika)</option>
                </select>
                <button>Pošalji</button>
                <p class="result"></p>
            </div>
        </div>

        <script>
            let imageInput = document.getElementById('image');
            imageInput.addEventListener('change', function(e) {
                let reader = new FileReader();
                reader.readAsDataURL(e.target.files[0]);
                reader.onload = function() {
                    document.querySelector('img').src = reader.result;
                    jQuery.ajax({
                        url: window.location.origin + '/get-image-info', method: 'post', headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        }, data: {
                            'image_url': reader.result
                        },  success: function (result) {
                            alert(result.choices[0].message.content);
                        }
                    });
                };
            });

            document.querySelector('button').addEventListener('click', function() {
                let name = document.getElementById('name').value;
                let gatara = document.getElementById('gatara').value;
                jQuery.ajax({
                    url: window.location.origin + '/get-chat-gpt-response', method: 'get', headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    }, data: {
                        'name': name,
                        'gatara': gatara
                    },  success: function (result) {
                        document.querySelector('p.result').innerHTML = result.response.choices[0].message.content.replace(/\n/g, '<br>');
                    }
                });
            });
        </script>
    </body>
</html>
