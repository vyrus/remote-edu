<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251"/>
   
	<title><?php echo (isset($this->title) ? $this->title : 'Главная') ?> :: Дистанционное обучение РЦИО</title>
   
    <link rel="stylesheet" href="/css/index.css" type="text/css">
	<!--[if lte IE 7]>
    <link href="/css/patches/patch_my_layout.css" rel="stylesheet" type="text/css" />
    <![endif]-->
    <meta name="keywords" content="ключевые слова" />
    <meta name="description" content="Описание" />
    <meta name="robots" content="All, Index" />
	
    <script type="text/javascript" src="/js/prototype.js"></script>
    <script type="text/javascript" src="/js/effects.js"></script>
	
  </head>
  
  <body>
    <div id="minwidth">                  
      <div id="container">
        <!-- Header -->		
        <div id="topheader">
          <div id="topmenu">
            <a href="123#" class="tophome"><img src="/images/icon_home.gif" width="11" height="10"/></a>
            <a href="##">Запись на курсы</a> 	<img src="/images/line_topmenu.gif" alt=""/>
			<a href="##">Стоимость</a> 			<img src="/images/line_topmenu.gif" alt=""/>
			<a href="##">О центре</a> 			<img src="/images/line_topmenu.gif" alt=""/>
			<a href="##">Контакты</a>			
          </div>
          <div id="topsearch">
            <form name="" action="" method="post" enctype="multipart/form-data">
              <input name="" class="txtfld" value="" type="text" />
			  <input name="" src="/images/icon_search.gif" class="button" type="image"/>
            </form>
          </div>
        </div>
        <div id="header">
          <div id="logo">
            <a href="###"><img src="/images/logo.jpg" alt="" width="287"
            height="113" /></a>
          </div>
          <div class="clr"></div>
          <div id="topcart">
            <strong>Наш адрес:</strong><br />
            <span>г. Орел, Наугорское шоссе 40</span><br />
            <strong>Телефон:</strong><br />
            <span>(4862) 34-55-98</span>
          </div>
        </div><!-- #Header --><!-- Body -->
        <div id="wrapper">
          <div id="main">
            <div id="main-container">
              <div id="navigation">
                <div class="inner">
				<ul class="menu">
                  <!-- <li class="first"><a href="/">Главная</a></li>
				  <li><a href="####">Каталог курсов</a> </li>
				  <li><a href="####">О центре</a> </li>
				  <li><a href="####">Форум</a> </li>
				  <li><a href="####">Расписание</a> </li>
				  <li><a href="####">Ссылки</a> </li>				  
				  <li><a href="####">Новости</a></li>
				  <li>|||</li> -->
				  <?php $this->renderElement('top-menu') ?>
				  </ul>
                </div>
              </div>
              <div id="content">
                <div class="clr"></div>
				<div id="this-content"> 
					<?php echo $this->content ?>
				</div>
				
				<br>
				
				  <!-- <div id="col1_content" class="clearfix">
                        <ul>                             
                            <?php  
                               // $ctrl = $this -> _request -> _router[REQUEST_URI] ;//$this->_request->_router['handler']['controller'];
                               // $element = $ctrl . '-actions';								
                               // $this->renderElement($element);								
                            ?>
                        </ul>
                    </div>  -->
              </div>
            </div>
          </div>  
		  
		  		  
          <!-- Menu -->
		  
		  <div id="leftcolumn">
			<div class="inner">
			
			
			<div class="leftblock">		

<!-- 			LOGIN PASS	
Куды вставлять, барин? =) Прям тут

			
 -->				
					<?php
						
						 /* Инициализируем обработчик формы */
						$action = '/users/login/';
						$form = Form_Profile_Login::create($action);
					
					?>
					<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
					<div class="form">
						<?php if (isset($this->error)): /* Как проверять ошибки я хз =) Потому что мне не понятно, куда данные будут отправляться */ ?>
						<div class="error"><?php echo $this->error ?></div>
						<?php endif; ?>
						
						<div class="field">
						  <label for="login">Имя пользователя:</label>
						  <input name="<?php echo $form->login->name ?>" type="text" id="login" value="<?php echo $form->login->value ?>" />
						</div>
						
						<?php if (isset($form->login->error)): ?>
						<div class="error"><?php echo $form->login->error ?></div>
						<?php endif; ?>
						
						<div class="field">
						  <label for="passwd">Пароль:</label>
						  <input name="<?php echo $form->passwd->name ?>" type="password" id="passwd" value="<?php echo $form->passwd->value ?>" />
						</div>
						
						<?php if (isset($form->passwd->error)): ?>
						<div class="error"><?php echo $form->passwd->error ?></div>
						<?php endif; ?>

						<div class="field">
							<input type="submit" value="Войти" />
						</div>
					</div>
					</form>
					<div class="topic-leftblock">
					<span><b>Д</b>ействия</span>
                </div>
				
                <div class="cntnt-leftblock">
				
                  <ul class="user-menu">
				  
				  <?php $this->renderElement($this->_request->_router['handler']['controller'] . '-actions') ?> 
				  
                   <li class="headli">
                      <a href="#">Привет, Йован!</a>
                    </li>
                  </ul>
                </div>
              </div>
			
			
			
              <div class="leftblock">
                <div class="topic-leftblock">
                  <span><b>О</b>бучение</span>
                </div>
                <div class="cntnt-leftblock">
                  <div>
                    <ul id="accordion">
                      <li class="headli">
                        <a href="#">Пользователь ПК</a>
                        <ul>
                          <li class='subli'>
                            <a href="#">Подссылка 1.1</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 1.2</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 1.3</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 1.4</a>
                          </li>
                        </ul>
                      </li>
                      <li class="headli">
                        <a href="#">Компьютер для школьника</a>
                        <ul>
                          <li class='subli'>
                            <a href="#">Подссылка 2.1</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 2.2</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 2.3</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 2.4</a>
                          </li>
                        </ul>
                      </li>
                      <li class="headli">
                        <a href="#">Компьютерная школа для старшеклассников</a>
                        <ul>
                          <li class='subli'>
                            <a href="#">Подссылка 3.1</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 3.2</a>
                          </li>
                        </ul>
                      </li>
                      <li class="headli">
                        <a href="#">Компьютерная графика и дизайн</a>
                        <ul>
                          <li class='subli'>
                            <a href="#">Подссылка 4.1</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 4.2</a>
                          </li>
                        </ul>
                      </li>
                      <li class="headli">
                        <a href="#">Сайтостроение</a>
                        <ul>
                          <li class='subli'>
                            <a href="#">Подссылка 5.1</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 5.2</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 5.3</a>
                          </li>
                        </ul>
                      </li>
                      <li class="headli">
                        <a href="#">Программирование на языках высокого
                        уровня</a>
                        <ul>
                          <li class='subli'>
                            <a href="#">Подссылка 6.1</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 6.2</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 6.3</a>
                          </li>
                        </ul>
                      </li>
                      <li class="headli">
                        <a href="#">Информационные технологии для
                        школьников</a>
                        <ul>
                          <li class='subli'>
                            <a href="#">Подссылка 7.1</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 7.2</a>
                          </li>
                        </ul>
                      </li>
                      <li class="headli">
                        <a href="#">Начальная профессиональная подготовка
                        (Оператор ЭВМ)</a>
                        <ul>
                          <li class='subli'>
                            <a href="#">Подссылка 8.1</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 8.2</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 8.3</a>
                          </li>
                        </ul>
                      </li>
                      <li class="headli">
                        <a href="#">Дополнительное Профессиональное Образование
                        (ДПО)</a>
                        <ul>
                          <li class='subli'>
                            <a href="#">Подссылка 9.1</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 9.2</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 9.3</a>
                          </li>
                        </ul>
                      </li>
                      <li class="headli">
                        <a href="#">Интернет-технологии в учебном процессе</a>
                        <ul>
                          <li class='subli'>
                            <a href="#">Подссылка 10.1</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 10.2</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 10.3</a>
                          </li>
                        </ul>
                      </li>
                      <li class="headli">
                        <a href="#">Операционная система Linux</a>
                        <ul>
                          <li class='subli'>
                            <a href="#">Подссылка 11.1</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 11.2</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 11.3</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 11.4</a>
                          </li>
                          <li class='subli'>
                            <a href="#">Подссылка 11.5</a>
                          </li>
                        </ul>
                      </li>
                    </ul><!-- Menu -->
					
					<script type="text/javascript">
						$$('#accordion > li:not([class="active"]) ul').invoke('setStyle', { display : 'none' }).invoke('addClassName', 'collapsed');
						$$('#accordion > li[class="active"] ul').invoke('addClassName', 'expanded');
						$$('#accordion > li > a').invoke(
							'observe', 
							'click', 
							function(e)
							{
								e.stop();
								var el = e.findElement('a');
								var ul = el.up('li').down('ul');
								if (ul) {
									if (ul.hasClassName('collapsed')) {
										var c = $$('#accordion ul:not([class="collapsed"])')[0];
										if (c) {
											new Effect.BlindUp(c.toggleClassName('collapsed').toggleClassName('expanded'));
										}
									}
									
									new Effect.BlindDown(ul.toggleClassName('collapsed').toggleClassName('expanded'));
								}
							}
						);
					</script>
					
                  </div>
                </div>
              </div>
              <div class="leftblock">
                <div class="topic-leftblock">
                  <span><b>К</b>онтакты</span>
                </div>
                <div class="cntnt-leftblock">
                  <div id="contacts">
                    <strong>Наш адрес:</strong><br />
                    г. Орел, Наугорское шоссе, 40<br />
                    <strong>Телефон</strong><br />
                    (4862) 40-67-83<br />
                    <strong>e-mail</strong><br />
                    <a href="mailto:sale@uchimvas.ru">sale@uchimvas.ru</a>
                  </div>
                </div>
              </div>
              <div class="leftblock">
                <div class="topic-leftblock">
                  <span><b>П</b>артнеры</span>
                </div>
                <div class="cntnt-leftblock">
                  <ul class="top-ten">
                    <li>
                      <a href="#">Орел ГТУ</a>
                    </li>
                    <li>
                      <a href="#">Linux</a>
                    </li>
                    <li>
                      <a href="#">CISCO</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="clr"></div>
        </div><!-- footer -->
        <div id="footer">
          <div id="main-footer">
            <div class="inner">
              <div id="bottom-footer">
                <div id="anotation">
                  <p align="justify">
                    Пара предложений про ИЦ с ключевыми словами
                  </p>
                </div>
                <div id="copyryght">
                  <div class="left">
                    <strong>© 2001-2009</strong>
                  </div>
                </div>
                <div class="break" align="right">
                  <br />
                  счетчик
                </div>
              </div>
            </div>
          </div>
          <div id="left-footer"></div>
          <div class="clr"></div>
		  
		  <div class="debug">
		  </div> 
		  
		  
        </div><!-- #footer -->
        <!-- #Body -->
      </div>
    </div>
  </body>
</html>
