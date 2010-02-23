<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<title>Дистанционное обучение · Орловский Региональный Центр Интернет-образования</title>
<meta http-equiv="Content-Type" content="text/html; utf-8">
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="robots" content="All, Index">
<link rel="icon" href="<?php echo $this->_links->getPath('/favicon.ico') ?>" type="image/x-icon" />
<link rel="stylesheet" href="<?php echo $this->_links->getPath('/files/css/index.css') ?>" type="text/css">
<link href="<?php echo $this->_links->getPath('/css/autocomplete.css') ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->_links->getPath('/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/jquery-1.3.2.min.js') ?>"></script> 
<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/jquery-ui-1.7.2.custom.min.js') ?>"></script> 
<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/ui.datepicker-ru.js') ?>"></script> 
<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/jquery.autocomplete.js') ?>"></script> 
<script type="text/javascript">
function seltext () {
  loginbox=document.getElementById('login');
  passwdbox=document.getElementById('passwd');
  if (loginbox.value == "логин") {
    loginbox.value = "";
  }
  if (passwdbox.value == "пароль") {
    passwdbox.value = "";
  }
}

$(document).ready(function() {
    $('#accordion li.headli').each(function(e) {
        $('a', $(this)).click(function() {
            $(this).next().toggle('slow');
        });
    });
});
</script>
</head>

<body>
<div id="minwidth">
  <div id="container">
        <!-- Header -->
    <div id="topheader2">
      <div id="topmenu">
		<a href="<?php echo $this->_links->get('index') ?>" class="tophome"><img src="<?php echo $this->_links->getPath('/files/images/icon_home.gif') ?>" alt="" width="11" height="10"></a>
		<a href="http://uchimvas.ru/zapis_na_dist.php">Подать заявку</a>
		<img src="<?php echo $this->_links->getPath('/files/images/line_topmenu.gif') ?>" alt="">
		<a href="http://uchimvas.ru/pismo">Задать вопрос</a>
		<img src="<?php echo $this->_links->getPath('/files/images/line_topmenu.gif') ?>" alt="">
		<a href="http://uchimvas.ru/article967">Нормативные документы</a>
	  </div>
      <div id="topsearch">
		<?php $this->renderElement('top-login') ?>
      </div>
    </div>
    <div id="header">
      <div id="logo">
        <a href="<?php echo $this->_links->get('index') ?>"><img src="<?php echo $this->_links->getPath('/files/images/logo.jpg') ?>" alt="" width="287" height="113"></a>
      </div>			
      <div class="clr"></div>
      <div id="topcart">
        <strong>Наш адрес:</strong><br>
        <span>г. Орел, Наугорское шоссе 40</span><br>
        <strong>Телефоны:</strong><br>
        <span>(4862) 40-96-14, 43-09-44</span>
      </div>
      <div id="logoostu"></div>
    </div>
      <!-- #Header -->

      <!-- Body -->
    <div id="wrapper">
      <div id="main">
        <div id="main-container">
          <div id="navigation2">
            <div class="inner">
              <?php $this->renderElement('top-menu') ?>
            </div>
          </div>
          <div id="content">
            <?php echo $this->content ?>
          </div>
          <div class="cntnt-container">
            <table class="we-have" border="1" width="100%">
              <tbody>
                <tr>
                  <td align="right">
                    <div id="pagination"></div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <noindex>
            <div class="allpagenavi">
              <a href="javascript:history.back()">Назад</a>
              <a href="<?php echo $this->_links->get('index') ?>" title="Главная">Главная</a>
            </div>
          </noindex>
        </div>
      </div>
	  
    <div id="leftcolumn">
      <div class="inner">
<?php /* Если пользователь не авторизован, не выводим меню дистанционки */ ?>
<?php if (false !== Model_User::create()->getAuth()): ?>
        <div class="leftblock">
          <div class="topic-leftblock"><span><b>Д</b>истанционка</span></div>
            <div class="cntnt-leftblock">
              <div>
                <ul id="accordion_no">
                  <?php $this->renderElement($this->_request->_router['handler']['controller'] . '-actions') ?>
                </ul>
              </div>
            </div>
        </div>
<?php endif; ?>
        <div class="leftblock">
          <div class="topic-leftblock"><span><b>Д</b>исциплины</span></div>
          <div class="cntnt-leftblock">
            <div>
              <ul id="accordion_no">
                <li class="headli_no"><a href="http://uchimvas.ru/article986">Сетевая академия CISCO (программа CCNA)</a></li>
              </ul>
              <ul id="accordion">
                <li class="headli">
                  <a href="#">Программы профессиональной переподготовки</a>
                    <ul>
                      <li class='subli'><a href="http://uchimvas.ru/article988">Программирование</a></li>
                      <li class='subli'><a href="http://uchimvas.ru/article989">Информационные технологии в сфере профессиональных коммуникаций</a></li>
                    </ul>
                </li>
              </ul>
            </div>
          </div>
        </div>
    
        <div class="leftblock">
          <div class="topic-leftblock"><span><b>К</b>онтакты</span></div>
          <div class="cntnt-leftblock">
            <div id="contacts"><strong>Наш адрес:</strong><br>
              г. Орел, Наугорское шоссе, 40<br>
              <strong>Телефоны</strong><br>
              (4862) 40-96-14, (4862) 43-09-44<br>
              <b><a href="http://uchimvas.ru/pismo" title="Обратная связь">Обратная связь</a></b>
            </div>
          </div>
        </div>
    
        <noindex>
          <div class="leftblock">
            <div class="topic-leftblock"><span><b>У</b>чредитель</span></div>
              <div class="cntnt-leftblock">
                <ul class="top-ten">
                  <li><a href="http://ostu.ru/" title="ОрелГТУ" target="_blank">Орловский государственный технический университет</a></li>
                </ul>
              </div>
            </div>
            <div class="leftblock">
              <div class="topic-leftblock"><span><b>П</b>артнеры</span></div>
              <div class="cntnt-leftblock">
                <ul class="top-ten">
                  <li><a href="http://ostu.ru/inst/cisco/main/" title="CISCO" target="_blank">Сетевая академия CISCO</a></li>
                  <li><a href="http://ostu.ru/inst/linux/main/" title="Linux" target="_blank">Центр компетентности Linux</a></li>
                </ul>
              </div>
            </div>
            <div class="leftblock">
            <div class="topic-leftblock"><span><b>П</b>оиск</span></div>
              <div class="cntnt-leftblock">
                <form name="" action="" method="post" enctype="multipart/form-data">
                <input name="s" class="txtfld" value="" type="text">
                <input name="" src="<?php $this->_links->getPath('/files/images/icon_search.gif') ?>" class="button" type="image"></form>
              </div>
            </div>
        </noindex>	
      </div>
    </div>

    <div class="clr"></div>
    </div>
 </div>
              <!-- footer -->
    <div id="footer">
      <div id="main-footer">
        <div class="inner">
          <div id="bottom-footer">
            <div id="anotation">
              <p align="justify">Сниппет</p>
            </div>
            <div class="break" style="float: right;">
              <noindex>
    <!--LiveInternet counter-->
                <script type="text/javascript"><!--
                  document.write("<a href='http://www.liveinternet.ru/click' "+
                  "target=_blank><img src='http://counter.yadro.ru/hit?t14.3;r"+
                  escape(document.referrer)+((typeof(screen)=="undefined")?"":
                  ";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
                  screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
                  ";"+Math.random()+
                  "' alt='' title='LiveInternet: показано число просмотров за 24"+
                  " часа, посетителей за 24 часа и за сегодня' "+
                  "border='0' width='88' height='31'><\/a>")
                  //--></script><!--/LiveInternet-->
                  <!-- Yandex.Metrika -->
                  <script src="http://mc.yandex.ru/metrika/watch.js" type="text/javascript"></script>
                  <script type="text/javascript">
                  try { var yaCounter409700 = new Ya.Metrika(409700); } catch(e){}
                </script>
                <noscript>
                  <div style="position: absolute;"><img src="http://mc.yandex.ru/watch/409700" alt="" /></div>
                </noscript>
                <!-- /Yandex.Metrika -->
              </noindex>
            </div>
            <div id="copyryght">&copy; 2001-2009 АНО "Центр Интернет-образования"</div>
            <div id="license">Лицензия Серия А № 266623 выдана Департаментом социальной
              политики Орловской области 16 ноября 2007 года.<br />
              Свидетельство о госаккредитации № 1351 от 30 декабря 2008 г. выдано
              Департаментом социальной политики Орловской области
            </div>
          </div>
        </div>
      </div>
      <div id="left-footer"></div>
      <div class="clr"></div>
    </div>
	<!-- #footer -->
<!-- #Body -->
  </div>
</div>
</body>
</html>