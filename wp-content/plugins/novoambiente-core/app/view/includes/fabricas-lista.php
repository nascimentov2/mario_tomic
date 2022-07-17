<div class="wrap">
	
	<h2>
		Fábricas
		<a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=novo_ambiente_importar" class="add-new-h2">Importar xls</a>
	</h2>
	
	<?php if($consulta['total'] > 0):?>
	<div class="tablenav">
		<?php include("paginacao-core.php"); ?>
	</div><!-- End tablenav -->
	<?php endif; ?>
	
	<table class="widefat">
		<thead>
			<tr>
				<th>Id</th>
				<th>Nome</th>
				<th>Quantidade de produtos</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php while ($o = mysql_fetch_object($consulta['result'])):
			
			$consulta_d = $registros->getRegistros('na_sys_produtos WHERE id_fabrica = '.$o->id_fabrica, $paginate, array('campo'=>'id_produto', 'tipo'=>'ASC'));
			
			$qtd = mysql_num_rows($consulta_d['result']);
?>
			<tr>
				<td><?php echo $o->id_fabrica?></td>
				<td>
					<a class="row-title" href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=novo_ambiente_fabricas&view=produtos&value=<?=$o->id_fabrica?>" title="Clique para visualizar os produtos da fábrica <?=$o->label?>"><?php echo $o->label; ?></a></td>
				<td><?php echo $qtd; ?></a></td>
				<td>
					<form action="#" method="post">
						<input name="idp" value="<?=$o->id_produto?>" type="hidden" />
						<input type="submit" value="x"/>
					</form>
				</td>
			</tr>	
		<?php endwhile;	?>
		
		</tbody>
	</table>
	
	<div class="tablenav bottom">
		<?php if($consulta['total'] > 0):?>
			<?php include("paginacao-core.php"); ?>
		<?php endif; ?>
	</div>

</div>