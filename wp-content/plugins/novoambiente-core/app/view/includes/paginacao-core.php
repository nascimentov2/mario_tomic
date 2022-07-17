<?php /** Paginação padrão no formato mais atual e otimizado Wordpress

@parametros
$url_redirect_page = Página que está sendo paginada;
$total = Total de registros na tabela
$pagina = $_GET["p"] ? $_GET["p"] : $pagina = 1; //Só incluir esta linha
$porpagina = Quantidade de registros por página
$inicio = ($pagina-1)*$porpagina; //Só incluir esta linha
$numeropaginas = ceil($total/$porpagina); //Só incluir esta linha
$readOnly = ( $numeropaginas == 1 ) ? "readonly=\"readonly\"" : ""; //Só incluir esta linha
$consulta = Consulta principal com parametros de paginação
$until = mysql_num_rows($consulta); //Número de resultado da consulta atual

*/ ?>

<?php 	$vars = '';
		foreach($_GET as $kGet => $vGet):
			$vars .= "&".$kGet."=".$vGet;
		endforeach;
?>

<?php $url_redirect_page = (isset($url_redirect_page))?$url_redirect_page:$_GET['page']; ?>

<div class="tablenav-pages">
	<form action="admin.php">
		<input type="hidden" name="page" value="<?php echo $url_redirect_page; ?>" />
		<span class="displaying-num"><?php echo $total ?> registros</span>
		<span class="pagination-links">
		<?php if(isset($_GET["p"]) && $pagina != 1): ?>
			<a class="first-page" title="Go to the first page" href="?page=<?php echo $url_redirect_page; ?><?=$vars?>">«</a>
			<a class="prev-page" title="Go to the previous page" href="?page=<?php echo $url_redirect_page; ?>&p=<?=($_GET["p"]-1);?><?=$vars?>">‹</a>
		<?php endif; ?>
		<?php $next = isset($_GET["p"]) ? $_GET["p"]+1 : 2 ?>
		<span class="paging-input"><span class="displaying-num">página &raquo;</span><input class="current-page" <?php echo $readOnly; ?>type="text" name="p" size="1" value="<?php echo ( isset($_GET["p"]) ) ? $_GET["p"] : "1" ; ?>" /> de <?php echo $numeropaginas; ?></span>
		<?php if ($inicio+$until != $total): echo "<a class=\"next-page\" title=\"Go to the next page\" href=\"?page=".$url_redirect_page."&p=".$next.$vars."\">›</a>"; endif; ?>
		<?php if ($inicio+$until != $total): echo "<a class=\"last-page\" title=\"Go to the last page\" href=\"?page=".$url_redirect_page."&p=".$numeropaginas.$vars."\">»</a>"; endif;  ?>
		</span>
	</form>
</div><!-- End tablenav-pages -->
