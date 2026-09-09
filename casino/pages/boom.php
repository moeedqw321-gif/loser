<?php
// اگر نیاز به سشن داری:
// check_session();
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <title>بوم</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="msapplication-tap-highlight" content="no">
    <link rel="shortcut icon" href="/default/modules/games/boom/assets/favicon.png" type="image/x-icon">
</head>
<body class="body-tag" oncontextmenu="return false">

    <div class="gm_background background"></div>
    <div id="main_container" class="main_container"></div>

    <script src="/default/modules/games/boom/js/jquery-3.3.1.min.js"></script>
    <script src="/default/modules/games/boom/js/sweetalert.min.js"></script>
    <script src="/default/modules/games/boom/js/soundjs.min.js"></script>
    <script src="/default/modules/games/boom/js/game.min.js?v=003"></script>

    <script>
        $(document).ready(function () {
            new SplashGame({
                auth: "/default/modules/games/boom/auth.php",
                language: "/default/modules/games/boom/language.php",
                font: "Tahoma",
                assets: {
                    path: "/default/modules/games/boom/assets/",
                    files: [
                        { name: "back", file: "back.png" },
                        { name: "cancel", file: "exit.png" }
                    ]
                },
                currency: {
                    delimiters: ['.', ','],
                    symbol: {
                        default: { right: "" },
                        short: { right: "" },
                        full: { right: " تومان" }
                    }
                },
                exit: "/users/casino/",
                help: "/users/casino/",
                rtl: true
            }).start();
        });
    </script>
</body>
</html>