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
                            <div class="alert {$alert_type}" style="display:block !important;">
                                {$message_details->message}
                            </div>
                        </div>
                    </div>
                </div>

            {/if}
        {/if}


        <div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">

            <section class="signupbox">

                <h1>وارد حساب کاربری خود شوید</h1>

                {form_open()}

                    <div class="page-area container inline form-container">

                        <div class="p30 inline"
                             align="right"
                             style="
                                margin-top:-30px !important;
                                text-align:right !important;
                                width:100% !important;
                             ">


                            <!-- ========================= -->
                            <!-- EMAIL -->
                            <!-- ========================= -->

                            <div class="mt15">

                                <div class="left form-title mw160">
                                    ایمیل
                                </div>

                                <div class="left form-element">

                                    <input
                                        type="text"
                                        name="email"
                                        class="form-input"
                                        autocomplete="username"
                                        style="
                                            height:40px;
                                            text-align:right !important;
                                        "
                                    >

                                </div>

                                <div class="clear"></div>

                            </div>


                            <!-- ========================= -->
                            <!-- PASSWORD -->
                            <!-- ========================= -->

                            <div class="mt15">

                                <div class="left form-title mw160">
                                    کلمه عبور
                                </div>

                                <div class="left form-element">

                                    <input
                                        type="password"
                                        name="password"
                                        class="form-input"
                                        autocomplete="current-password"
                                        style="
                                            height:40px;
                                            text-align:right !important;
                                        "
                                    >

                                </div>

                                <div class="clear"></div>

                            </div>


                            <!-- ========================= -->
                            <!-- CAPTCHA INPUT -->
                            <!-- ========================= -->

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
                                        inputmode="text"
                                        spellcheck="false"
                                        style="
                                            height:40px;
                                            text-align:right !important;
                                            direction:ltr;
                                            text-transform:uppercase;
                                        "
                                    >

                                </div>

                                <div class="clear"></div>

                            </div>


                            <!-- ========================= -->
                            <!-- CAPTCHA IMAGE -->
                            <!-- ========================= -->

                            <div class="mt15">

                                <div class="left form-title mw160"></div>

                                <div class="left form-element"
                                     style="
                                        text-align:right !important;
                                        height:52px;
                                        line-height:52px;
                                     ">

                                    <canvas
                                        id="captchaCanvas"
                                        width="250"
                                        height="50"
                                        class="security_captcha"
                                        title="کد امنیتی"
                                        aria-label="کد امنیتی"
                                        style="
                                            display:block;
                                            width:250px;
                                            height:50px;
                                            cursor:default;
                                            user-select:none;
                                            border:0;
                                            margin:0;
                                            padding:0;
                                        "
                                    ></canvas>

                                </div>

                                <div class="clear"></div>

                            </div>


                            <!-- ========================= -->
                            <!-- LOGIN -->
                            <!-- ========================= -->

                            <div class="mt15">

                                <div class="left form-title mw160"></div>

                                <div class="left form-element"
                                     style="text-align:right !important;">

                                    <button
                                        type="submit"
                                        class="form-button action-button"
                                        id="loginButton"
                                    >
                                        ورود
                                    </button>

                                    <div class="pull-right"
                                         style="margin-top:8px;">

                                        <input
                                            type="checkbox"
                                            id="RememberMe"
                                            name="remember_me"
                                            value="1"
                                        />

                                        مرا به خاطر بسپار

                                    </div>

                                </div>

                                <div class="clear"></div>

                            </div>


                            <!-- ========================= -->
                            <!-- LINKS -->
                            <!-- ========================= -->

                            <div class="mt15">

                                <div class="left form-title mw160"></div>

                                <div class="left form-element"
                                     style="text-align:right !important;">

                                    <a
                                        class="btn form-button btn-block"
                                        href="{site_url}users/resetPassword"
                                    >
                                        کلمه عبور خود را فراموش کرده ام !
                                    </a>

                                    <a
                                        class="btn form-button btn-block"
                                        href="{site_url}users/register"
                                    >
                                        ساخت حساب کاربری جدید
                                    </a>

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


<!-- ====================================================== -->
<!-- CAPTCHA JAVASCRIPT -->
<!-- ====================================================== -->

<script>

(function () {

    'use strict';

    var canvas = document.getElementById('captchaCanvas');
    var input  = document.getElementById('captcha');

    if (!canvas || !input) {
        return;
    }

    var ctx = canvas.getContext('2d');

    /*
     * فقط حروف و اعداد
     * کاراکترهای گیج‌کننده حذف شده‌اند:
     * O / 0 / I / 1
     */
    var characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    var captchaCode = '';


    function random(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }


    function getRandomChar() {
        return characters.charAt(
            random(0, characters.length - 1)
        );
    }


    function generateCaptcha() {

        captchaCode = '';

        for (var i = 0; i < 5; i++) {
            captchaCode += getRandomChar();
        }

        drawCaptcha();

    }


    function drawCaptcha() {

        /*
         * پاک کردن Canvas
         */
        ctx.clearRect(
            0,
            0,
            canvas.width,
            canvas.height
        );


        /*
         * Background
         */
        ctx.fillStyle = '#f7f7f7';

        ctx.fillRect(
            0,
            0,
            canvas.width,
            canvas.height
        );


        /*
         * نویز نقطه‌ای
         */

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
                random(80, 180) + ',' +
                random(80, 180) + ',' +
                random(80, 180) + ',' +
                '0.35)';

            ctx.fill();

        }


        /*
         * خطوط مزاحم
         */

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
                random(60, 140) + ',' +
                random(60, 140) + ',' +
                random(60, 140) + ',' +
                '0.55)';

            ctx.lineWidth = random(1, 2);

            ctx.stroke();

        }


        /*
         * متن CAPTCHA
         */

        var startX = 25;

        for (var k = 0; k < captchaCode.length; k++) {

            ctx.save();

            var char = captchaCode.charAt(k);

            var x = startX + (k * 43);

            var y = random(32, 39);

            /*
             * چرخش هر کاراکتر
             */

            var angle =
                random(-18, 18) *
                Math.PI / 180;

            ctx.translate(x, y);

            ctx.rotate(angle);

            /*
             * فونت
             */

            ctx.font =
                'bold ' +
                random(28, 34) +
                'px Arial';

            ctx.textAlign = 'center';

            ctx.textBaseline = 'middle';

            /*
             * رنگ تصادفی
             */

            var colorList = [
                '#111111',
                '#222222',
                '#333333',
                '#444444',
                '#555555',
                '#8b0000',
                '#003366'
            ];

            ctx.fillStyle =
                colorList[
                    random(0, colorList.length - 1)
                ];

            /*
             * سایه بسیار کم
             */

            ctx.shadowColor =
                'rgba(0,0,0,0.25)';

            ctx.shadowBlur = 1;

            ctx.fillText(
                char,
                0,
                0
            );

            ctx.restore();

        }


        /*
         * خطوط نهایی روی نوشته
         */

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


    /*
     * فقط حروف و اعداد وارد شود
     */

    input.addEventListener(
        'input',
        function () {

            this.value =
                this.value
                    .replace(/[^a-zA-Z0-9]/g, '')
                    .toUpperCase()
                    .substring(0, 5);

        }
    );


    /*
     * تبدیل ورودی به حروف بزرگ
     */

    input.addEventListener(
        'keyup',
        function () {

            this.value =
                this.value.toUpperCase();

        }
    );


    /*
     * جلوگیری از Paste کاراکتر غیرمجاز
     */

    input.addEventListener(
        'paste',
        function (e) {

            e.preventDefault();

            var text =
                (e.clipboardData ||
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


    /*
     * ساخت اولیه کپچا
     */

    generateCaptcha();


    /*
     * اعتبارسنجی هنگام Submit
     */

    var form = input.closest('form');

    if (form) {

        form.addEventListener(
            'submit',
            function (e) {

                var entered =
                    input.value
                        .trim()
                        .toUpperCase();

                if (entered.length !== 5) {

                    e.preventDefault();

                    alert(
                        'لطفاً کد امنیتی ۵ رقمی را وارد کنید.'
                    );

                    input.focus();

                    return false;

                }


                if (entered !== captchaCode) {

                    e.preventDefault();

                    alert(
                        'کد امنیتی اشتباه است.'
                    );

                    input.value = '';

                    input.focus();

                    /*
                     * کپچای جدید
                     */

                    generateCaptcha();

                    return false;

                }

            }
        );

    }

})();

</script>