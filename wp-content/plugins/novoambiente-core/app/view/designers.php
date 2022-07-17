<?php

	$registros = new registros;
	$url_redirect_page = $_GET["page"];
										
	$pagina = isset($_GET["p"]) ? $_GET["p"] : $pagina = 1;
	$porpagina = 100;
	$inicio = ($pagina-1)*$porpagina;
	$paginate = array('inicio' => $inicio, 'limite' => $porpagina);
	
	$consulta = $registros->getRegistros('na_sys_designers', $paginate, array('campo'=>'nome', 'tipo'=>'ASC'));
	
	$numeropaginas = ceil($consulta['total']/$porpagina);
	$readOnly = ( $numeropaginas == 1 ) ? "readonly=\"readonly\"" : "";
	
	$until = mysql_num_rows($consulta['result']);
	$total = $consulta['total'];
	
?>

<div class="wrap">
	
	<h2>
		Designers
		<a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=novo_ambiente_importar" class="add-new-h2">Importar xls</a>
	</h2>
	
	<?php 	if(isset($_SESSION['retorno'])):
                
                print '<div id="message" class="'.$_SESSION['retorno']['classe'].' updated below-h2">
									<p>'.$_SESSION['retorno']['mensagem'].'</p>
								</div>';
                unset($_SESSION['retorno']);

    		endif; ?>

	<?php if($consulta['total'] > 0):?>
	<div class="tablenav">
		<?php include("includes/paginacao-core.php"); ?>
	</div><!-- End tablenav -->
	<?php endif; ?>
	
	<table class="widefat">
		<thead>
			<tr>
				<th>Id</th>
				<!--th>Id Post</th-->
				<th>Nome</th>
				<th>Quantidade de produtos</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php while ($o = mysql_fetch_object($consulta['result'])):

			$consulta_d = $registros->getRegistros('na_sys_produtos WHERE id_designer = '.$o->id_designer, $paginate, array('campo'=>'id_produto', 'tipo'=>'ASC'));
			$qtd = mysql_num_rows($consulta_d['result']);

		?>
			<tr>
				<td><?php echo $o->id_designer?></td>
				<!--td><?php echo $o->id_post?></td-->
				<td>
					<?php if($o->id_post!=0): ?>
						<a href="<?=bloginfo('url')?>/wp-admin/post.php?post=<?=$o->id_post?>&action=edit"><?php echo $o->nome; ?></a>
					<?php else: ?>
						<?php echo $o->nome; ?>
					<?php endif; ?>
					</td>
				<td><?php echo $qtd; ?></a></td>
				<td>
					<?php if($o->id_post==0): ?>
						<form action="<?=bloginfo('url')?>/action/registro/createPostDesign" method="post">
							<input name="id_design" value="<?=$o->id_designer?>" type="hidden" />
							<input name="nome" value="<?=$o->nome?>" type="hidden" />
							<input name="produtos" value="<?=$qtd?>" type="hidden" />
							<input type="submit" name="publicar" id="publicar" class="button action" value="Enviar para o cat&aacute;logo">
						</form>
					<?php else: ?>
						<a rel="tipsy" href="<?=bloginfo('url')?>/wp-admin/post.php?post=<?=$o->id_post?>&action=edit" title="Conectado com o cat&aacute;logo"><img src="<?=bloginfo('url')?>/wp-content/plugins/novoambiente-core/_static/img/link.gif" /></a>
					<?php endif; ?>
				</td>
			</tr>	
		<?php endwhile;	?>
		
		</tbody>
	</table>
	
	<div class="tablenav bottom">
		<?php if($consulta['total'] > 0):?>
			<?php include("includes/paginacao-core.php"); ?>
		<?php endif; ?>
	</div>

</div>