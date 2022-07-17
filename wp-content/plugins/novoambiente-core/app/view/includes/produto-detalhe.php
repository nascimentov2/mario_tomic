<div class="wrap">
	
	<h2>
		Detalhes do Produto
	</h2>

	<ul class="subsubsub">
		<li class="all"><a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=novo_ambiente_core">Lista de Produtos</a> ></li>
		<li class="all"> <?php print ucwords($consulta['registro']->label); ?> </li>
	</ul>

	<?php if(isset($_SESSION['retorno'])): ?>
			<?php if ($_SESSION['retorno']['classe']=='sucesso'): ?>
				<div id="message" class="updated" style="clear:both;">
					<p><?php print $_SESSION['retorno']['mensagem']; ?></p>
				</div>
			<?php endif; ?>
			<?php if ($_SESSION['retorno']['classe']=='erro'): ?>
				<div class="error" style="clear:both;">
						<p><?php print $_SESSION['retorno']['mensagem']; ?></p>
				</div>
			<?php endif; ?>
	<?php unset($_SESSION['retorno']); endif; ?>
	
	<?php if($consulta['value']==0): ?>
		<h3><?php echo $consulta['registro']; ?></h3>
		<input type="button" value="Voltar" name="Voltar" class="button action" onClick="javascript:history.back(1)">
		<?php die(); ?>
	<?php endif; ?>

	<?php 
		if(empty($consulta['registro'])):
			echo '<h3>Produto existe no banco de dados. Erro na exibição do objeto retornado.</h3>
			<input type="button" value="Voltar" name="Voltar" class="button action" onClick="javascript:history.back(1)">';
			die();
		endif;
	?>

	<form id="editar-produto" action="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=novo_ambiente_core&view=detalhe&value=<?php echo $consulta['registro']->id_produto; ?>&action=editProd" method="post">

	<table class="form-table">
		<tbody>
			<tr>
				<th><h3>Informações do produto</h3></th>
				<td></td>
			</tr>
			<tr>
				<th><label for="codigo_produto">Código do produto</label></th>
				<td><input type="text" class="regular-text" readonly="readonly" value="<?php print $consulta['registro']->cod_produto; ?>" id="cod_produto" name="cod_produto"></td>
			</tr>
			<tr>
				<th><label for="label">Título</label></th>
				<td><input type="text" class="regular-text" value="<?php print $consulta['registro']->label; ?>" id="label" name="label"></td>
			</tr>
			<tr>
				<th><label for="role">Status</label></th>
				<td>

					<select id="status" name="status">
						<option value="draft" <?php if($consulta['registro']->status=='draft') print 'selected="selected"'; ?>>Rascunho</option>
						<option value="publish" <?php if($consulta['registro']->status=='publish') print 'selected="selected"'; ?>>Publicado</option>
					</select>

				</td>
			</tr>
			<tr>
				<th></th>
				<td>
					<input type="submit" name="editar" id="editar" class="button button-primary button-large" value="Salvar">
					<input type="hidden" name="id_produto" value="<?php print $consulta['registro']->id_produto; ?>">
					</form>
				</td>
			</tr>
			<tr>
				<th><h3>Variações</h3></th>
				<td>
					<table cellpadding="0" cellspacing="0">
						<?php
							while($ov = mysql_fetch_object($c_variacao['result'])):
								
								echo "<tr>
										<td>
											<a href='".get_bloginfo('url')."/wp-admin/admin.php?page=novo_ambiente_core&view=detalheVariacao&value=".$ov->id_variacao."&idProd=".$consulta['registro']->id_produto."' target='edit-variacao' id='edit-variacao'>
												<img src='".$ov->foto1."' width='60' height='60' />
											</a>
										</td>
										<td>".$ov->altura."</td>
										<td>".$ov->comprimento."</td>
										<td>".$ov->profundidade."</td>
										<td><a class='row-title' href='".get_bloginfo('url')."/wp-admin/admin.php?page=novo_ambiente_core&view=detalheVariacao&value=".$ov->id_variacao."&idProd=".$consulta['registro']->id_produto."'>Editar</a></td>
									</tr>";		
								
							endwhile;
						?>
					</table>
				</td>
			</tr>
		</tbody>
	</table>

	

</div>