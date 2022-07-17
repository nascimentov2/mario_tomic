
<div class="wrap">
	
	<h2>
		Valores por produto
	</h2>

	<?php //$arr = get_defined_vars(); print "<pre>"; print_r($arr); ?>

	<ul class="subsubsub">
		<li class="all"><a href="<?=get_bloginfo('url')?>/wp-admin/admin.php?page=novo_ambiente_fabricas">Fábricas </a> ></li>
		<li class="active"> Tabela de produtos </li>
	</ul>

	<br>

	<?php if(isset($_SESSION['retorno'])): ?>
         		<div id="message" class="<?=$_SESSION['retorno']['classe']?>' updated below-h2">
					<p><?=$_SESSION['retorno']['mensagem']?></p>
					<div class="detalhes">
						<p><?=$_SESSION['retorno']['detalhes']?></p>
					</div>
				</div>
    <?php unset($_SESSION['retorno']); endif;?>
	
	<?php if($produtos['total'] > 0):?>
			<div class="tablenav">
				<?php include("paginacao-core.php"); ?>
			</div><!-- End tablenav -->
	<?php endif; ?>
	
	<?php foreach ($dataProdutos as $keyValue => $prodValue): ?>

			<?php 	$variacoes = $registros->getRegistros(DB_VARIACOES.' WHERE id_produto = '.$prodValue['id'], array('inicio' => 0, 'limite' => 999), array('campo'=>'id_variacao', 'tipo'=>'ASC'));
					if($variacoes["total"]>0):	?>
						
						<table class="widefat">
							<thead>
								<tr>
									<th width="500">
										<?php if($prodValue['id_post']!=0): ?>
											<a class="row-title" href="<?=bloginfo('url')?>/wp-admin/post.php?post=<?=$prodValue['id_post']?>&action=edit" title="Detalhes do protudo"><?=$prodValue['nome']?></a>
										<?php else: ?>
											<h3><?=$prodValue['nome']?></h3>
										<?php endif; ?>
									</th>
									<?php foreach ($dataTipos as $keyTipo => $keyValue): ?>
											<th width="200"><?=$keyValue['nome']?></th>
									<?php endforeach; ?>
									<th>
										<a class="button button-primary button-large" href="<?=get_bloginfo('url')?>/wp-admin/admin.php?page=novo_ambiente_fabricas&view=editar&fab=<?=$_GET['value']?>&value=<?=$prodValue['id']?>" > 
											Editar 
										</a>
									</th>
								</tr>
							</thead>
							<tbody>

							<?php 	while ($objVariacoes = mysql_fetch_object($variacoes['result'])):	?>

											<tr>
												<td><?=$objVariacoes->altura.'x'.$objVariacoes->comprimento.'x'.$objVariacoes->profundidade?></td>

												<?php 	$tiposOutro = $registros->getTiposByFabrica($prodValue['id_fabrica']);
														while ($objTipos = mysql_fetch_object($tiposOutro)): ?>

														<?php 	//print "array_push($valores, array( ".$objVariacoes->id_variacao." => array( ".$objTipos->id_tipo." => '88.35'))) <br>";
																if(!empty($valores[$objVariacoes->id_variacao][$objTipos->id_tipo])): ?>

																<td class="variacao_<?=$objVariacoes->id_variacao?>-tipo_<?=$objTipos->id_tipo?>"><?=$valores[$objVariacoes->id_variacao][$objTipos->id_tipo]['valor']?></td>

														<?php else: ?>

																<td>-</td>

														<?php endif; ?>

												<?php endwhile; ?>
												<td>
													&nbsp;
												</td>
											</tr>	
							<?php endwhile; ?>
							
								</tbody>
							</table>
							<br><br><br>

			<?php 	endif;	?>

	<?php endforeach; ?>
	
	<div class="tablenav bottom">
		<?php if($produtos['total'] > 0):?>
			<?php include("paginacao-core.php"); ?>
		<?php endif; ?>
	</div>

</div>