<?php
    if( !isset($usuarios) ) { $usuarios = new usuario; } 
    if( !isset($registros) ) { $registros = new registros; } 

    if(isset($_COOKIE['uid'])):
        $usuarios = new usuario;
        $objUsuario = $usuarios->getUsuario($_COOKIE['uid']);
    endif;
?>
      <footer id="footer">
            <div class="wrap">
                <div class="footer-links">
                    <div class="footer-section">
                        <h3>Catálogo</h3>
                        <ul>
                            <li><a href="<?=BASE_URL?>">Departamentos</a></li>
                            <li><a href="">Produtos</a></li>
                            <li><a href="">Designer</a></li>
                        </ul>
                    </div>
                    <div class="footer-section">
                        <h3>Minha conta</h3>
                        <ul>
                            <li><a href="<?=bloginfo('url')?>/sys/minha-lista">Minha lista</a></li>
                            <?php  /* if (! $usuarios->estaLogado() ): ?>
                            <li><a href="<?=bloginfo('url')?>/sys/login">Login ou Cadastro</a></li>
                            <?php else: ?>
                            <li><a href="<?=bloginfo('url')?>/sys/login">Minha conta</a></li>
                            <?php endif;*/ ?>
                        </ul>
                    </div>
                    <div class="footer-section">
                        <h3>Institucional</h3>
                        <ul>
                            <li><a href="http://<?=$_SERVER['SERVER_NAME']?>">Novo Ambiente</a></li>
                            <li><a href="http://www.novoambiente.com">Visite a loja virtual</a></li>
                            <li><a href="http://www.novoambiente.com/solucoesparaempresas">Projetos corporativos</a></li>
                            <li><a href="http://www.novoambiente.com/faleconosco">Fale conosco</a></li>
                        </ul>
                    </div>
                    <div class="footer-section omega">
                        <h3>Novo ambiente no Facebook</h3>
                        <iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2FLojaNovoAmbiente&amp;width=300&amp;height=200&amp;show_faces=true&amp;colorscheme=dark&amp;stream=false&amp;border_color=%233b3d3b&amp;header=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:300px; height:200px;" allowTransparency="true"></iframe>
                    </div>
                </div>
            </div>
        </footer>
        <!-- / #footer -->
    </div>
    <!-- / #global -->

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="<?php echo get_template_directory_uri(); ?>/lib/jquery-1.8.3.min.js"><\/script>')</script>
<script src="<?php echo get_template_directory_uri(); ?>/lib/js-webshim/minified/polyfiller.js"></script>
<!--script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/lib/jquerypp.js"></script-->

<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/lib/jquery.touchSwipe.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/lib/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/lib/jquery.jscroll.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/lib/jquery.carouFredSel-6.2.0-packed.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/lib/jquery.tipsy.js"></script>
<?php /*

<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/lib/elastislide/js/modernizr.custom.17475.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/lib/elastislide/js/jquerypp.custom.js"></script>
<!--script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/lib/elastislide/js/jquery.elastislide.js"></script-->

<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/lib/elastislide/js/jquery.touchcarousel.js"></script>


*/
?>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/lib/jquery.nivo.slider.pack.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/lib/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/lib/jquery.lazyload.js"></script>

<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/lib/main.js"></script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-28006347-2', 'novoambiente.com');
  ga('send', 'pageview');

</script>

<script type="text/javascript">
adroll_adv_id = "J4UJOSOEX5HHTBMLBAB3TW";
adroll_pix_id = "GPUGQV27ONFBJE3O7FB72V";
(function () {
var oldonload = window.onload;
window.onload = function(){
   __adroll_loaded=true;
   var scr = document.createElement("script");
   var host = (("https:" == document.location.protocol) ? "https://s.adroll.com" : "http://a.adroll.com");
   scr.setAttribute('async', 'true');
   scr.type = "text/javascript";
   scr.src = host + "/j/roundtrip.js";
   ((document.getElementsByTagName('head') || [null])[0] ||
    document.getElementsByTagName('script')[0].parentNode).appendChild(scr);
   if(oldonload){oldonload()}};
}());
</script>
<!-- Performance: Total de <?php echo get_num_queries(); ?> consultas ao banco em <?php timer_stop(1); ?> segundos. -->
<?php wp_footer(); ?>
</body>
</html>
