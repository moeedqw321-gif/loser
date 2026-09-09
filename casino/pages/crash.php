<?php
global $main_connection, $_System_Options_Variebles;
check_session();

$id = isset($_COOKIE['always_id_for_casino']) ? $_COOKIE['always_id_for_casino'] : 0;
$q = $main_connection->query("SELECT * FROM `users` WHERE `id`='".$main_connection->real_escape_string($id)."'");
$user = $q ? $q->fetch_array(MYSQLI_ASSOC) : null;
if(!$user) die('کاربر یافت نشد');

$username = trim(($user['first_name'] ?? '').' '.($user['last_name'] ?? ''));
if($username === '') $username = $user['username'] ?? 'کاربر';
$cash = (float)($user['cash'] ?? 0);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $action = isset($_POST['action']) ? safe_codes($_POST['action']) : '';
    if($action === 'new_chat'){
        $txt = safe_codes($_POST['message_text'] ?? '');
        if($txt === ''){ echo 'متن خالی است'; exit; }
        $main_connection->query("INSERT INTO `crash_chats` (`id`,`time`,`user`,`username`,`message`) VALUES (NULL,'".time()."','".$main_connection->real_escape_string($id)."','".$main_connection->real_escape_string($username)."','".$main_connection->real_escape_string($txt)."')");
        $_SESSION['message_'.$main_connection->insert_id] = 'true';
        echo 'ok'; exit;
    }
    if($action === 'get_new_chats'){
        $t = time() - 40;
        $out = array();
        $qq = $main_connection->query("SELECT * FROM `crash_chats` WHERE `time` >= '$t' ORDER BY `id` DESC LIMIT 30");
        if($qq) while($r = $qq->fetch_array(MYSQLI_ASSOC)){
            if(isset($_SESSION['message_'.$r['id']])) continue;
            $_SESSION['message_'.$r['id']] = 'true';
            $out[$r['id']] = '<div class="chat-row"><div><span>'.htmlspecialchars($r['username']).'</span></div><div class="txt">'.htmlspecialchars($r['message']).'</div></div>';
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('data'=>$out), JSON_UNESCAPED_UNICODE); exit;
    }
    echo 'invalid'; exit;
}
?>
<!DOCTYPE html>
<html lang="fa">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
<title>انفجار</title>
<link rel="shortcut icon" href="/default/modules/games/crash/assets/favicon.png">
<link rel="stylesheet" href="/default/modules/games/crash/css/reset.css">
<link rel="stylesheet" href="/default/modules/games/crash/css/rangeslider.css">
<link rel="stylesheet" href="/default/modules/games/crash/css/style.css">
<style>
/* فیکس فونت خراب */
@font-face{font-family:IRANSans;src:local('Tahoma')}
body,input,table,a{font-family:IRANSans,Tahoma,sans-serif!important}

/* پس‌زمینه لابی مثل اصلی */
.background{
  background:url('/default/modules/games/crash/assets/background.jpg') #000 center/cover no-repeat fixed!important;
}

/* ضریب وسط گراف */
#odd_display{
  position:absolute;left:0;right:0;top:38%;
  text-align:center;font-size:54px;font-weight:700;
  color:#52fca2;z-index:6;pointer-events:none;
  text-shadow:0 0 14px rgba(82,252,162,.4);
}
#odd_display.dead{color:#ff3b3b;text-shadow:0 0 14px rgba(255,59,59,.45)}

/* دکمه ثبت/برداشت با اسپرایت قرمز اصلی */
.place-bet,.cashout-now{
  display:block;text-align:center;padding:12px 8px;margin-top:8px;
  color:#fff!important;text-decoration:none;cursor:pointer;font-size:15px;
  background:url('/default/modules/games/crash/assets/button_red.png') center/100% 100% no-repeat;
}
.cashout-now{background:#c0392b!important;background-image:none!important}
.seconds-line{text-align:center;color:#e58929;font-size:13px;min-height:18px;margin:6px 0}

/* ارتفاع‌های لازم چون game.min.js نیست */
#game_screen{background:#fff!important}
#game_screen .left-container{float:left;width:calc(65% - 10px);height:calc(100% - 50px);margin:5px}
#game_screen .right-container{float:left;width:calc(35% - 10px);height:calc(100% - 50px);margin:5px 5px 5px 0;border:1px solid #ccc}
#game_screen .game-graph{width:100%;height:42%;position:relative;background:#f7f7f7;border:1px solid #ddd}
#game_screen .game-controls{width:100%;height:auto;padding-bottom:8px}
#game_screen .game-bottom{width:100%;height:calc(58% - 160px);min-height:180px}
#graph-container,#graph{width:100%;height:100%}
.usr-row{border-bottom:1px solid #eee;padding:4px 0}
.usr-row .col{float:left;font-size:13px;line-height:24px;padding-left:6px}
</style>
<script src="/default/modules/games/crash/js/jquery.js"></script>
</head>
<body class="body-tag" oncontextmenu="return false">

<div class="background"></div>
<div id="main_container" class="main_container">

  <!-- Lobby -->
  <div id="lobby_screen" class="screen">
    <div class="top_left_panel">
      <div class="left height_100">
        <img src="/default/modules/games/crash/assets/placeholder.png" class="profile_photo" height="100%">
      </div>
      <div class="left margin_l10">
        <div class="top_left_name"><?php echo htmlspecialchars($username); ?></div>
        <div class="top_left_chips"><?php echo number_format($cash); ?> تومان</div>
      </div>
      <div class="clear"></div>
    </div>
    <div class="top_right_panel">
      <div class="right margin_r10 height_100">
        <img src="/default/modules/games/crash/assets/home.png" height="100%" style="cursor:pointer" onclick="location.href='/users/casino/'">
      </div>
      <div class="clear"></div>
    </div>
    <div class="center_panel">
      <table class="middle"><tr><td>
        <center>
          <a href="javascript:;" id="play_button" class="myButton margin_t15 medium_font">شروع بازی</a>
        </center>
      </td></tr></table>
    </div>
  </div>

  <!-- Game (ساختار هات‌بت) -->
  <div id="game_screen" class="screen hidden">
    <div class="top-bar">
      <div class="top-link" style="float:left;margin-left:16px;"><?php echo htmlspecialchars($username); ?></div>
      <div class="top-link"><a href="javascript:;" id="exit_btn" class="active">خروج</a></div>
      <div class="top-link" id="cash_label"><?php echo number_format($cash); ?> تومان</div>
      <div class="clear"></div>
    </div>

    <div class="left-container">
      <div class="game-graph">
        <div id="graph-container" class="graph-container">
          <canvas id="graph"></canvas>
          <div id="odd_display">0.00</div>
        </div>
      </div>
      <div class="clear"></div>

      <div class="game-controls">
        <div class="desktop"><div class="tab-link">پنل شرط</div><div class="clear"></div></div>
        <div class="bet-widget">
          <div class="title">مبلغ</div>
          <div class="text-box game-amount-box">
            <div class="box-1">
              <input type="text" class="game-amount" id="bet_amount" value="1000">
              <a href="javascript:;" class="make-double" id="btn_2x" style="display:block;float:right;margin-right:5px;margin-top:-24px;width:26px;height:20px;line-height:20px;border-radius:10px;text-align:center;background:#ddd;color:#666;font-size:10px;text-decoration:none;">2X</a>
              <div class="clear"></div>
            </div>
            <div class="box-1-text">تومان</div>
            <div class="clear"></div>
          </div>

          <div class="title">برداشت اتوماتیک</div>
          <div class="text-box cashout-amount-box">
            <div class="box-2" style="width:80px">
              <input type="text" class="cashout-amount" id="auto_amount" value="2.00">
            </div>
            <div class="box-2-text" style="width:calc(100% - 80px);background:#fff">
              <div style="padding:10px 5px">
                <input type="range" class="range-slide" id="auto_range" value="2" min="1.01" max="20" step="0.01">
              </div>
            </div>
            <div class="clear"></div>
          </div>

          <div class="seconds-line" id="timer_line"></div>
          <a href="javascript:;" class="place-bet" id="bet_btn">ثبت شرط</a>
          <a href="javascript:;" class="cashout-now" id="cash_btn" style="display:none">برداشت</a>
        </div>
      </div>

      <div class="game-bottom">
        <div class="tab-container desktop">
          <a href="javascript:;" class="tab-div-2 tab-active"><div class="tab-inner-div">چت</div></a>
        </div>
        <div class="chat-panel">
          <div class="container" style="position:relative">
            <div class="chat-container" id="chat_box"></div>
          </div>
          <div class="chat-text">
            <form onsubmit="return false">
              <input type="text" class="chat-input" id="chat_input" placeholder="پیام...">
              <a href="javascript:;" class="chat-send" id="chat_send">
                <img src="/default/modules/games/crash/assets/send.png" height="24">
              </a>
            </form>
          </div>
        </div>
        <div class="clear"></div>
      </div>
    </div>

    <div class="right-container desktop">
      <div class="table-header">
        <div class="col col-1">کاربر</div>
        <div class="col col-2">@</div>
        <div class="col col-3">مبلغ</div>
        <div class="col col-5">سود</div>
        <div class="clear"></div>
      </div>
      <div id="desktop-users" class="table-body users-list-container"></div>
      <div class="table-footer">
        <div class="left red-bar" style="width:20%"></div>
        <div class="left yellow-bar" style="width:30%"></div>
        <div class="left green-bar" style="width:50%"></div>
        <div class="clear"></div>
      </div>
    </div>
    <div class="clear"></div>
  </div>
</div>

<script>
(function($){
  var uid = '<?php echo $id; ?>';
  var cash = <?php echo $cash; ?>;
  var gid = 0, price = 0, odd = 0, crashAt = 0;
  var playing = false, betted = false, canBet = false, busy = false;
  var tLeft = 7, tInt = null, runInt = null, listInt = null;
  var pts = [];
  var sock = null;
  var canvas = document.getElementById('graph');
  var ctx = canvas.getContext('2d');

  function resize(){
    var box = document.getElementById('graph-container').getBoundingClientRect();
    canvas.width = Math.max(300, box.width || 600);
    canvas.height = Math.max(160, box.height || 260);
    draw();
  }
  window.addEventListener('resize', resize);

  function draw(){
    var w = canvas.width, h = canvas.height;
    ctx.clearRect(0,0,w,h);
    if(pts.length < 2) return;
    ctx.beginPath();
    ctx.lineWidth = 3;
    ctx.strokeStyle = (!playing && odd >= crashAt) ? '#ff3b3b' : '#52fca2';
    for(var i=0;i<pts.length;i++){
      var x = (i / Math.max(pts.length-1,1)) * w;
      var y = h - (Math.min(pts[i], 20) / 20) * (h * 0.8) - 16;
      if(i===0) ctx.moveTo(x,y); else ctx.lineTo(x,y);
    }
    ctx.stroke();
  }

  function connect(){
    sock = new WebSocket('ws://127.0.0.1:2222');
    sock.onopen = function(){
      // فقط یک‌بار در اتصال
      sock.send('crash_new_round|' + uid);
    };
    // هیچ reload خودکاری نیست
    sock.onclose = function(){
      setTimeout(connect, 4000); // فقط reconnect نرم، بدون رفرش صفحه
    };
    sock.onmessage = onMsg;
  }

  function onMsg(e){
    var p = String(e.data || '').split('|');
    if(p[0] === 'round_start'){
      if(busy) return; // وسط راند، راند جدید را دور بریز
      clearTimeout(runInt); clearInterval(tInt); clearInterval(listInt);
      gid = p[1] || 0;
      crashAt = parseFloat(p[2]) || 10;
      odd = 0; pts = []; playing = false; canBet = true; betted = false; busy = true; tLeft = 7;
      $('#odd_display').text('0.00').removeClass('dead');
      $('#bet_btn').show().text('ثبت شرط');
      $('#cash_btn').hide();
      $('#bet_amount,#auto_amount,#auto_range').prop('disabled', false);
      $('#desktop-users').empty();
      $('#timer_line').text('شروع تا ' + tLeft + ' ثانیه');
      tInt = setInterval(tick, 100);
      listInt = setInterval(function(){
        if(sock && sock.readyState === 1) sock.send('check_crash_users|' + gid);
      }, 2000);
      draw();
    }
    else if(p[0] === 'list_users' || p[0] === 'new_crash_number'){
      var list = [];
      try { list = JSON.parse(p[1] || p[2] || '[]'); } catch(err){}
      if(!$.isArray(list)) return;
      var html = '';
      $.each(list, function(i,u){
        var color = (u.win == 1) ? '#408609' : (u.win == 2 ? '#c00' : '#e58929');
        var o = (!u.odd || u.odd == 0) ? '-' : u.odd;
        var pr = (u.win == 1) ? nf(Math.floor(u.price * u.odd - u.price)) : '-';
        html += '<div class="usr-row" style="color:'+color+'"><div class="col col-1">'+esc(u.username||'')+'</div><div class="col col-2">'+o+'</div><div class="col col-3">'+nf(u.price||0)+'</div><div class="col col-5">'+pr+'</div><div class="clear"></div></div>';
      });
      $('#desktop-users').html(html);
    }
  }

  $('#play_button').on('click', function(){
    $('#lobby_screen').hide();
    $('#game_screen').removeClass('hidden').show();
    setTimeout(resize, 60);
  });
  $('#exit_btn').on('click', function(){ location.href = '/users/casino/'; });

  $('#bet_btn').on('click', function(){
    if(betted){
      betted = false;
      $(this).text('ثبت شرط');
      $('#bet_amount,#auto_amount,#auto_range').prop('disabled', false);
      if(sock && sock.readyState === 1) sock.send('crash_add_user|'+gid+'|'+price+'|'+uid);
      return;
    }
    if(!canBet) return alert('الان نمی‌توانید شرط ببندید');
    price = parseInt(String($('#bet_amount').val()).replace(/,/g,''), 10) || 0;
    if(price < 100) return alert('حداقل ۱۰۰ تومان');
    if(price > cash) return alert('موجودی کافی نیست');
    betted = true;
    $(this).text('لغو شرط');
    $('#bet_amount,#auto_amount,#auto_range').prop('disabled', true);
    if(sock && sock.readyState === 1) sock.send('crash_add_user|'+gid+'|'+price+'|'+uid);
  });

  $('#cash_btn').on('click', function(){
    if(!playing || !betted) return;
    var win = odd * price;
    cash += win;
    $('#cash_label').text(nf(cash) + ' تومان');
    betted = false;
    $('#bet_btn').show().text('ثبت شرط');
    $('#cash_btn').hide();
    $('#bet_amount,#auto_amount,#auto_range').prop('disabled', false);
    if(sock && sock.readyState === 1) sock.send('crash_add_wallet|'+win+'|'+uid+'|'+odd+'|'+gid);
  });

  $('#btn_2x').on('click', function(){
    var v = parseInt(String($('#bet_amount').val()).replace(/,/g,''),10) || 0;
    $('#bet_amount').val(nf(v * 2));
  });
  $('#auto_range').on('input change', function(){ $('#auto_amount').val(parseFloat(this.value).toFixed(2)); });
  $('#auto_amount').on('change', function(){ $('#auto_range').val(this.value); });

  function tick(){
    tLeft = (parseFloat(tLeft) - 0.1).toFixed(1);
    if(tLeft <= 0){
      clearInterval(tInt);
      canBet = false; playing = true;
      $('#timer_line').text('');
      if(betted){
        cash -= price;
        $('#cash_label').text(nf(cash) + ' تومان');
        $('#bet_btn').hide();
        $('#cash_btn').show().text('برداشت');
        if(sock && sock.readyState === 1) sock.send('crash_reduce_wallet|'+price+'|'+uid+'|'+odd+'|'+gid);
      } else {
        $('#bet_btn').text('شرطی بسته نشد');
      }
      run(); return;
    }
    $('#timer_line').text('شروع تا ' + tLeft + ' ثانیه');
  }

  function run(){
    odd = parseFloat(odd) || 0;
    var plus = odd < 27 ? 0.01 : 0.1;
    odd = Math.max(1, parseFloat((odd + plus).toFixed(2)));
    pts.push(odd); if(pts.length > 140) pts.shift();
    $('#odd_display').text(odd.toFixed(2));
    $('#cash_btn').text('برداشت ' + nf(Math.floor(odd * price)) + ' تومان');
    draw();

    var auto = parseFloat($('#auto_amount').val()) || 0;
    if(odd >= crashAt){
      $('#odd_display').addClass('dead');
      playing = false; betted = false; busy = false;
      clearInterval(listInt);
      $('#bet_btn').show().text('ثبت شرط');
      $('#cash_btn').hide();
      if(sock && sock.readyState === 1){
        sock.send('crash_close_round|'+gid+'|'+uid);
        // راند بعدی فقط یک‌بار و با فاصله کافی (نه ۲ ثانیه)
        setTimeout(function(){
          if(sock && sock.readyState === 1) sock.send('crash_new_round|' + uid);
        }, 5000);
      }
      return;
    }
    if(playing && betted && auto > 0 && odd >= auto) $('#cash_btn').click();
    var sp = odd < 2 ? 80 : (odd < 5 ? 50 : (odd < 10 ? 28 : 14));
    runInt = setTimeout(run, sp);
  }

  function loadChat(){
    $.post('', {action:'get_new_chats'}, function(r){
      try{
        var j = (typeof r === 'string') ? JSON.parse(r) : r;
        $.each(j.data || {}, function(i,h){ $('#chat_box').prepend(h); });
      }catch(e){}
    });
  }
  setInterval(loadChat, 3000);
  $('#chat_send').on('click', function(){
    var t = $.trim($('#chat_input').val());
    if(!t) return;
    $('#chat_input').val('');
    $.post('', {action:'new_chat', message_text:t}, function(){ loadChat(); });
  });
  $('#chat_input').on('keypress', function(e){ if(e.which === 13) $('#chat_send').click(); });

  function nf(n){ return Math.floor(Number(n)||0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','); }
  function esc(s){ return String(s).replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]; }); }

  connect();
})(jQuery);
</script>
</body>
</html>