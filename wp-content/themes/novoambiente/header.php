<?php 
if( !isset($usuarios) ) { $usuarios = new usuario; } 
if( !isset($registros) ) { $registros = new registros; } 

if(isset($_COOKIE['uid'])):
    $usuarios = new usuario;
    $objUsuario = $usuarios->getUsuario($_COOKIE['uid']);
endif;

if(isset($_SESSION['login_email'])):
    $emailUsuario = $_SESSION['login_email'];
elseif(isset($objUsuario->email)):
    $emailUsuario = $objUsuario->email;
else:
    $emailUsuario = 'E-mail não encontrado.';
endif;

$template_url = get_bloginfo('template_url');
$bloginfo_url = get_bloginfo('url');

?>

<?php //print "<pre>"; print_r($_COOKIE); print "</pre>"; ?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php wp_title('Novo Ambiente | '); ?></title>
    
    <!--<link href="<?php echo $template_url ?>/css/custom.css" rel="stylesheet"/>-->
    <link href="<?php echo $template_url ?>/css/menu.init.css" rel="stylesheet"/>
    <link href="<?php echo $template_url ?>/css/menu.mobile.css" rel="stylesheet"/>
    
    <link rel="icon" href="<?php echo $template_url ?>/favicon.png">

    <!--<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">-->
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1" />
    <meta http-equiv="expires" content="0">
    <!-- <link rel="stylesheet" href="lib/elastislide/css/elastislide.css"> -->
    <link rel="stylesheet" href="<?php bloginfo("stylesheet_url");?>" />

    <link rel="stylesheet" href="<?php echo $template_url ?>/lib/nivo-slider.css" />
    <link rel="stylesheet" href="<?php echo $template_url ?>/lib/themes/bar/bar.css" />
    <script src="<?php echo $template_url ?>/lib/modernizr-2.6.2.min.js"></script>
    <meta name="google-site-verification" content="O_Luq4QKxD3jzzGmx3SvvhaF1nVbEjc4J557ehz-P1s" />
<?php wp_head(); ?>
<script src="http://cdn.jquerytools.org/1.2.7/full/jquery.tools.min.js"></script>
        <script>var __adobewebfontsappname__ = "code"</script>
        <script src="http://use.edgefonts.net/open-sans:n7,i7,n8,i8,i4,n3,i3,n4,n6,i6:all.js"></script>
        <!--[if lt IE 9]>
	       <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
        <![endif]-->
</head>
<body base="<?php echo $bloginfo_url ?>" <?php if(is_home()): body_class("home"); endif; ?>>
    
    <div id="global">
        <div id="header">
        <div id="topo-hm">
        <header class="tophead">
            <div class="wrap">
                <nav class="topmenu">
                    <ul class="top">
                       <li class="hide"><a href="http://www.novoambiente.com">Página Inicial</a></li>
                        <li><a href="http://www.novoambiente.com/faleconosco">Fale Conosco</a></li>
                        <li><a href="http://www.novoambiente.com/lojas"><span class="hide">Encontre a </span><span class="loja-string">loja</span> mais próxima</a></li>
                        <li class="last"><a href="http://www.novoambiente.com/quemsomos">Sobre Nós</a></li>
                    </ul>

                    <ul class="socialnet">
                        <li class="blog"><a href="http://www.novoambiente.com/blog/"  target="_blank">Blog</a></li>
                        <li class="fb icon"><a href="https://www.facebook.com/LojaNovoAmbiente" target="_blank">facebook</a></li>
                        <li class="insta icon"><a href="http://instagram.com/novoambiente" target="_blank">instagram</a></li>
                        <li class="gplus icon"><a href="https://plus.google.com/100484796129271603819?prsrc=5" target="_blank" >googleplus</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        
        <header class="mainhead">
            <div class="wrap">
                <h1 id="logo"><a href="http://design.novoambiente.com/"><img src="<?php echo $template_url ?>/img/logo.png" title="logo" alt="Novo Ambiente" /></a></h1>

                <nav role='mainmenu' class="mainmenu">
                    
                    <? 
                        $isDesigner = (preg_match('/\/designer/', get_permalink())) ? true : false ;
                    ?>
                    <input type="checkbox" id="mbt" />
                    
                    <label for="mbt" id="mobile_btn">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </label>
                    
                    <ul>

                        <li class="hide-mobile"><a href="http://www.novoambiente.com" rel='nofollow'>Página Inicial</a></li>
                        <li><a href="http://www.novoambiente.com/loja">Loja Virtual</a></li>
                        <li <? if (!$isDesigner): echo 'class="selected"'; endif; ?>><a href="http://design.novoambiente.com">Produtos</a></li>
                        <li <? if ($isDesigner): echo 'class="selected"'; endif; ?>><a href="http://design.novoambiente.com/designer/">Designers</a></li>
                        <li><a href="http://www.novoambiente.com/solucoesparaempresas">Soluções Corporativas</a></li>
                        <li class="hide-mobile"><a href="http://www.novoambiente.com/blog/" rel='nofollow'>Blog</a></li>
                    </ul>

                    <form id="main-search" action="http://design.novoambiente.com" method="get">
                        <input type="text" name="s" placeholder="Procure produtos..." id="search-box"/>
                        <input type="submit" value="" id="search-btn"/>
                    </form>
                </nav>
            </div>
        </header>
        </div>
           
            <div class='nav' id="main-navigation">
                <ul class="wrap">
                    <li><a href="<?= $bloginfo_url ?>/departamento">Departamentos<span class="dropdown"></span></a>
                        <ul>
                         <?
                            $args = array(
                                "orderby" => 'name',
                                "show_count" => 0,
                                "taxonomy" => 'departamento',
                                "title_li" => 0,
                                'hierarchical' => 1,
                                "hide_empty" => 0,
                                "number" => 0
                            );

                            wp_list_categories( $args ); ?>
                            
                            <div class="clearfix"></div>
                    
                     </ul>
                    
                    </li>
                    <li><a href="<?= bloginfo('url'); ?>/tipo-de-produto">Produtos<span class="dropdown"></span></a>
                        <ul>
                            <?php 
                            $args = array(
                                "orderby" => 'name',
                                "show_count" => 0,
                                "taxonomy" => 'tipo-de-produto',
                                "title_li" => 0,
                                "hide_empty" => 0,
                                "number" => 0
                            );

                            wp_list_categories( $args ); ?>
                            
                            <div class="clearfix"></div>
                    
                         
                        </ul>
                    </li>
                    <li><a href="<?= bloginfo('url'); ?>/designer/">Designers</a>
                        <? /* <ul>
                        <?php 
                            $args = array(
                                'posts_per_page'  => 0,
                                'numberposts'     => 12,
                                'orderby'         => 'post_name',
                                'order'           => 'ASC',
                                'post_type'       => 'designer',
                                'post_status'     => 'publish' );
                            $arrDesigners = get_posts( $args );
                            
                            foreach ($arrDesigners as $key => $obj):
                                echo "<li><a href=\"".$obj->guid."\">".$obj->post_title."</a></li>";
                            endforeach;
                        ?>
                        </ul> */ ?>
                    </li>
                    <li class="my-list">
                        <?php  // verifica se o cookie existe e soma a quantidade de produtos já inseridos na lista
                               
                               if(isset($_COOKIE["arrml"])):
                                    $arrValores = json_decode(stripslashes($_COOKIE["arrml"]));
                               
                                    $numListaCookie = count((array)$arrValores);
                               else:
                                    $numListaCookie = 0;
                               endif; ?>
                               

                            
                        <?php /* if ( $usuarios->estaLogado() ): ?>
                            <li><?=$emailUsuario?></li>
                            <li><a href="<?=bloginfo('url')?>/sys/minha-lista/"><span class="minha-lista-black"></span>minha lista (<em id="numMinhaLista"><?=$numListaCookie?></em>)</a></li>
                            <li><a href="<?=bloginfo('url')?>/sys/cadastro/"><span class="login-black"></span>minha conta</a></li>
                            <li><a href="<?=bloginfo('url')?>/action/usuario/logOut"><span class="login-black"></span>logout</a></li>
                        <?php else: */ ?>
                            <a href="<?=$bloginfo_url?>/sys/minha-lista/"><span class="minha-lista-black"></span>minha lista (<em id="numMinhaLista"><?=$numListaCookie?></em>)</a>
                        <?php /* <li><a href="<?=bloginfo('url')?>/sys/login/" class="button">entrar</a></li>
                        <?php endif; */ ?>
                    </li>
                </ul>
            </div>
        </div>
        <!-- / #header -->
        </div>
        <script src="<?php echo $template_url ?>/lib/search.js"></script>
</body>
</html>
