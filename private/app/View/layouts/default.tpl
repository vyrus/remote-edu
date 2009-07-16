<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php echo (isset($this->title) ? $this->title : 'Главная') ?> :: Дистанционное обучение РЦИО</title>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <link href="/css/my_layout.css" rel="stylesheet" type="text/css" />
    <!--[if lte IE 7]>
    <link href="/css/patches/patch_my_layout.css" rel="stylesheet" type="text/css" />
    <![endif]-->
</head>

<body>
    <div class="page_margins">
        <div id="border-top">
            <div id="edge-tl"></div>
            <div id="edge-tr"></div>
        </div>
        
        <div class="page">
            <div id="nav">
                <a id="navigation" name="navigation"></a>
                <div class="hlist">
                    <ul>
                        <?php $this->renderElement('top-menu') ?>
                    </ul>
                </div>
            </div>
            
            <div id="main">
                <div id="col1">
                    <div id="col1_content" class="clearfix">
                        <ul>                             
                            <?php
                                $ctrl = $this->_request->_router['handler']['controller'];
                                $element = $ctrl . '-actions';
                                $this->renderElement($element);
                            ?>
                        </ul>
                    </div>
                </div>
                
                <div id="col3">
                    <div id="col3_content" class="clearfix">
                        <?php echo $this->content ?>
                    </div>
                    
                    <!-- IE Column Clearing -->
                    <div id="ie_clearing"> &#160; </div>
                </div>
            </div>
        </div>
        
        <div id="border-bottom">
            <div id="edge-bl"></div>
            <div id="edge-br"></div>
        </div>
    </div>
</body>
</html>