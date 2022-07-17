<div class="wrap">

	<h2>
		<?php print $consulta !== false ? 'Produto '.ucwords($infProduto['registro']->label) : 'Adicionar produto'; ?>
	</h2>

	<ul class="subsubsub">
		<li class="all"><a href="<?php echo get_bloginfo('url'); ?>/wp-admin/edit.php?post_type=produto">Lista de Produtos</a> ></li>
		<li class="all"><a href="<?php echo get_bloginfo('url'); ?>/wp-admin/post.php?post=<?=$infProduto['registro']->id_post?>&action=edit"> <?php print ucwords($infProduto['registro']->label); ?> </a> ></li>
		<li class="all"> Variação </li>
	</ul>

	<?php if($consulta['value']==0 && $consulta !== false ): ?>
		<h3><?php print $consulta['registro']; ?></h3>
		<input type="button" value="Voltar" name="Voltar" class="button action" onClick="javascript:history.back(1)">
		<?php die(); ?>
	<?php endif; ?>

	<div style="clear:both"></div> 

	<?php if(isset($_SESSION['retorno'])): ?>
    		<div id="message" class="<?=$_SESSION['retorno']['classe']?>' updated below-h2">
				<p><?=$_SESSION['retorno']['mensagem']?></p>
			</div>
    <?php unset($_SESSION['retorno']); endif; ?>

	<?php 
		if(empty($consulta['registro']) && $consulta !== false):
			print '<h3>Produto existe no banco de dados. Erro na exibição do objeto retornado.</h3>
			<input type="button" value="Voltar" name="Voltar" class="button action" onClick="javascript:history.back(1)">';
			die();
		endif;
	?>
	<form id="editar-produtoVariacao" action="<?=bloginfo('url')?>/action/registro/<?php echo $consulta != false ? 'setVariacaoProd' : 'addVariacaoProduto' ; ?>" method="post">

		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="codigo_produto">Comprimento</label></th>
					<td><input type="text" class="regular-text" value="<?php print $consulta['registro']->comprimento; ?>" name="comprimento"></td>
				</tr>
				<tr>
					<th><label for="codigo_produto">Profundidade</label></th>
					<td><input type="text" class="regular-text" value="<?php print $consulta['registro']->profundidade; ?>" name="profundidade"></td>
				</tr>
				<tr>
					<th><label for="codigo_produto">Altura</label></th>
					<td><input type="text" class="regular-text" value="<?php print $consulta['registro']->altura; ?>" name="altura"></td>
				</tr>
				<tr>
					<th><label for="codigo_produto">Descrição</label></th>
					<td><textarea name="descricao" rows="4" cols="33" class="regular-text"><?php print $consulta['registro']->descricao; ?></textarea>
				</tr>
				<tr>
					<th><label for="role"></label></th>
					<td>
						<input type="hidden" name="id_variacao" value="<?=$consulta['registro']->id_variacao?>">
						<input type="hidden" name="id_produto" value="<?=isset($consulta['registro']->id_produto) ? $consulta['registro']->id_produto : $idProd?>">
						<input type="hidden" name="id_post" value="<?=$infProduto['registro']->id_post?>">
						<input type="submit" id="salvar" class="button button-primary button-large" value="Salvar">
					</td>
				</tr>
				
			</tbody>
		</table>

	</form>

</div>