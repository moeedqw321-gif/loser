<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {

    /* =========================
       Mobile Mask
    ========================= */

    var mobile = document.getElementById('mobile');

    if (mobile && typeof IMask !== 'undefined') {
        new IMask(mobile, {
            mask: '+(98) 000 000 0000'
        });
    }


    /* =========================
       CAPTCHA
    ========================= */

    var canvas = document.getElementById('captchaCanvas');
    var captchaInput = document.getElementById('captcha');

    if (!canvas || !captchaInput) {
        return;
    }

    var ctx = canvas.getContext('2d');

    var characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    var captchaCode = '';


    function random(min, max) {
        return Math.floor(
            Math.random() * (max - min + 1)
        ) + min;
    }


    function generateCaptchaCode() {

        captchaCode = '';

        for (var i = 0; i < 5; i++) {
            captchaCode += characters.charAt(
                random(0, characters.length - 1)
            );
        }

    }


    function drawCaptcha() {

        /* Background */

        ctx.clearRect(
            0,
            0,
            canvas.width,
            canvas.height
        );

        ctx.fillStyle = '#f7f7f7';

        ctx.fillRect(
            0,
            0,
            canvas.width,
            canvas.height
        );


        /* Noise dots */

        for (var i = 0; i < 180; i++) {

            ctx.beginPath();

            ctx.arc(
                random(0, canvas.width),
                random(0, canvas.height),
                random(1, 2),
                0,
                Math.PI * 2
            );

            ctx.fillStyle =
                'rgba(' +
                random(70, 180) + ',' +
                random(70, 180) + ',' +
                random(70, 180) + ',' +
                '0.35)';

            ctx.fill();

        }


        /* Random lines */

        for (var j = 0; j < 7; j++) {

            ctx.beginPath();

            ctx.moveTo(
                random(0, canvas.width),
                random(0, canvas.height)
            );

            ctx.lineTo(
                random(0, canvas.width),
                random(0, canvas.height)
            );

            ctx.strokeStyle =
                'rgba(' +
                random(50, 150) + ',' +
                random(50, 150) + ',' +
                random(50, 150) + ',' +
                '0.55)';

            ctx.lineWidth = random(1, 2);

            ctx.stroke();

        }


        /* CAPTCHA characters */

        var colors = [
            '#111111',
            '#222222',
            '#333333',
            '#444444',
            '#555555',
            '#7b0000',
            '#003366'
        ];


        for (var k = 0; k < captchaCode.length; k++) {

            ctx.save();

            var character =
                captchaCode.charAt(k);

            var x = 25 + (k * 43);

            var y = random(31, 39);

            var angle =
                random(-18, 18) *
                Math.PI / 180;


            ctx.translate(x, y);

            ctx.rotate(angle);


            ctx.font =
                'bold ' +
                random(28, 34) +
                'px Arial';


            ctx.textAlign = 'center';

            ctx.textBaseline = 'middle';


            ctx.fillStyle =
                colors[
                    random(0, colors.length - 1)
                ];


            ctx.shadowColor =
                'rgba(0,0,0,0.20)';

            ctx.shadowBlur = 1;


            ctx.fillText(
                character,
                0,
                0
            );


            ctx.restore();

        }


        /* Curved lines over CAPTCHA */

        for (var z = 0; z < 3; z++) {

            ctx.beginPath();

            ctx.moveTo(
                0,
                random(8, 42)
            );

            ctx.bezierCurveTo(
                50,
                random(0, 50),
                150,
                random(0, 50),
                250,
                random(5, 45)
            );

            ctx.strokeStyle =
                'rgba(0,0,0,0.45)';

            ctx.lineWidth = 1;

            ctx.stroke();

        }

    }


    function createCaptcha() {

        generateCaptchaCode();

        drawCaptcha();

    }


    /* فقط حروف انگلیسی و عدد */

    captchaInput.addEventListener(
        'input',
        function () {

            this.value =
                this.value
                    .replace(/[^a-zA-Z0-9]/g, '')
                    .toUpperCase()
                    .substring(0, 5);

        }
    );


    /* Paste */

    captchaInput.addEventListener(
        'paste',
        function (event) {

            event.preventDefault();

            var text =
                (event.clipboardData ||
                 window.clipboardData)
                .getData('text');

            text =
                text
                    .replace(/[^a-zA-Z0-9]/g, '')
                    .toUpperCase()
                    .substring(0, 5);

            this.value = text;

        }
    );


    /* Submit validation */

    var form =
        captchaInput.closest('form');


    if (form) {

        form.addEventListener(
            'submit',
            function (event) {

                var entered =
                    captchaInput.value
                        .trim()
                        .toUpperCase();


                if (entered.length !== 5) {

                    event.preventDefault();

                    alert(
                        'لطفاً کد امنیتی ۵ کاراکتری را وارد کنید.'
                    );

                    captchaInput.focus();

                    return false;

                }


                if (entered !== captchaCode) {

                    event.preventDefault();

                    alert(
                        'کد امنیتی وارد شده صحیح نیست.'
                    );

                    captchaInput.value = '';

                    createCaptcha();

                    captchaInput.focus();

                    return false;

                }

            }
        );

    }


    /* Generate on page load */

    createCaptcha();

});
</script>


<div class="register">

    <div class="container" align="center">

        {if !empty($smarty.const.get_message)}

            {assign var=message_details value=(object)$smarty.const.get_message}

            {if !empty($message_details->message)}

                {if $message_details->type == 'fail'}
                    {assign var=alert_type value='alert-danger'}

                {elseif $message_details->type == 'warning'}
                    {assign var=alert_type value='alert-warning'}

                {elseif $message_details->type == 'success'}
                    {assign var=alert_type value='alert-success'}

                {/if}


                <div class="row">

                    <div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">

                        <div style="
                            text-align:right !important;
                            width:100% !important;
                            margin-top:10px !important;
                            margin-bottom:-30px !important;
                        ">

                            <div
                                class="alert {$alert_type}"
                                style="display:block !important;"
                            >
                                {$message_details->message}
                            </div>

                        </div>

                    </div>

                </div>

            {/if}

        {/if}


        <div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">

            <section class="signupbox">

                <h1>برای شروع ثبت نام کنید</h1>


                {form_open($action)}

                    <div class="page-area container inline form-container">

                        <div
                            class="p30 inline"
                            align="right"
                            style="
                                margin-top:-30px !important;
                                text-align:right !important;
                                width:100% !important;
                            "
                        >


                            <!-- نام -->

                            <div class="mt15">

                                <div class="left form-title mw160">
                                    نام
                                </div>

                                <div class="left form-element">

                                    <input
                                        type="text"
                                        name="first_name"
                                        class="form-input"
                                        style="
                                            height:40px;
                                            text-align:right !important;
                                        "
                                    >

                                </div>

                                <div class="clear"></div>

                            </div>


                            <!-- نام خانوادگی -->

                            <div class="mt15">

                                <div class="left form-title mw160">
                                    نام خانوادگی
                                </div>

                                <div class="left form-element">

                                    <input
                                        type="text"
                                        name="last_name"
                                        class="form-input"
                                        style="
                                            height:40px;
                                            text-align:right !important;
                                        "
                                    >

                                </div>

                                <div class="clear"></div>

                            </div>


                            <!-- ایمیل -->

                            <div class="mt15">

                                <div class="left form-title mw160">
                                    ایمیل
                                </div>

                                <div class="left form-element">

                                    <input
                                        type="text"
                                        name="email"
                                        class="form-input"
                                        style="
                                            height:40px;
                                            text-align:left !important;
                                        "
                                    >

                                </div>

                                <div class="clear"></div>

                            </div>


                            <!-- موبایل -->

                            <div class="mt15">

                                <div class="left form-title mw160">
                                    شماره موبایل
                                </div>

                                <div class="left form-element">

                                    <input
                                        type="text"
                                        id="mobile"
                                        name="mobile"
                                        class="form-input"
                                        style="
                                            height:40px;
                                            direction:ltr !important;
                                            text-align:left !important;
                                        "
                                    >

                                </div>

                                <div class="clear"></div>

                            </div>


                            <!-- رمز عبور -->

                            <div class="mt15">

                                <div class="left form-title mw160">
                                    کلمه عبور
                                </div>

                                <div class="left form-element">

                                    <input
                                        type="password"
                                        name="password"
                                        class="form-input"
                                        style="
                                            height:40px;
                                            text-align:right !important;
                                        "
                                    >

                                </div>

                                <div class="clear"></div>

                            </div>


                            <!-- تکرار رمز -->

                            <div class="mt15">

                                <div class="left form-title mw160">
                                    تکرار کلمه عبور
                                </div>

                                <div class="left form-element">

                                    <input
                                        type="password"
                                        name="confirmPassword"
                                        class="form-input"
                                        style="
                                            height:40px;
                                            text-align:right !important;
                                        "
                                    >

                                </div>

                                <div class="clear"></div>

                            </div>


                            <!-- ========================== -->
                            <!-- CAPTCHA INPUT -->
                            <!-- ========================== -->

                            <div class="mt15">

                                <div class="left form-title mw160">
                                    کد امنیتی
                                </div>

                                <div class="left form-element">

                                    <input
                                        type="text"
                                        name="captcha"
                                        id="captcha"
                                        class="form-input"
                                        maxlength="5"
                                        autocomplete="off"
                                        spellcheck="false"
                                        inputmode="text"
                                        style="
                                            height:40px;
                                            text-align:left !important;
                                            direction:ltr !important;
                                            text-transform:uppercase;
                                        "
                                    >

                                </div>

                                <div class="clear"></div>

                            </div>


                            <!-- ========================== -->
                            <!-- CAPTCHA IMAGE -->
                            <!-- ========================== -->

                            <div class="mt15">

                                <div class="left form-title mw160"></div>

                                <div
                                    class="left form-element"
                                    style="
                                        text-align:right !important;
                                        height:50px;
                                    "
                                >

                                    <canvas
                                        id="captchaCanvas"
                                        width="250"
                                        height="50"
                                        class="security_captcha"
                                        style="
                                            display:block;
                                            width:250px;
                                            height:50px;
                                            margin:0;
                                            padding:0;
                                            border:0;
                                            user-select:none;
                                        "
                                    ></canvas>

                                </div>

                                <div class="clear"></div>

                            </div>


                            <!-- ========================== -->
                            <!-- REGISTER BUTTON -->
                            <!-- ========================== -->

                            <div class="mt15">

                                <div class="left form-title mw160"></div>

                                <div
                                    class="left form-element"
                                    style="text-align:right !important;"
                                >

                                    <button
                                        type="submit"
                                        class="form-button action-button"
                                    >
                                        ثبت نام
                                    </button>

                                </div>

                                <div class="clear"></div>

                            </div>


                        </div>

                    </div>

                {form_close()}

            </section>

        </div>

    </div>

</div>