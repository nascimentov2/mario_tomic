
<div class="wrap">
	
	<h2>
		Editar valores
	</h2>

	<ul class="subsubsub">
		<li class="all"><a href="<?=get_bloginfo('url')?>/wp-admin/admin.php?page=novo_ambiente_fabricas">Fábricas </a> ></li>
		<li class="all"><a href="<?=get_bloginfo('url')?>/wp-admin/admin.php?page=novo_ambiente_fabricas&view=produtos&value=<?=$fabrica?>">Tabela de produtos </a> ></li>
		<li class="active" class="current"> Valores do produto  </li>
	</ul>

	<br><br><br><br>

	<?php if(isset($_SESSION['retorno'])): ?>
         		<div id="message" class="<?=$_SESSION['retorno']['classe']?>' updated below-h2">
					<p><?=$_SESSION['retorno']['mensagem']?></p>
					<?php if(isset($_SESSION['retorno']['detalhes'])): ?>
						<div class="detalhes">
							<p><?=$_SESSION['retorno']['detalhes']?></p>
						</div>
					<?php endif;?>
				</div>
    <?php unset($_SESSION['retorno']); endif; ?>


	<?php foreach ($dataProdutos as $keyValue => $prodValue): ?>

			<form action="<?=get_bloginfo('url')?>/action/registro/setValores" method="post">
			
				<table class="widefat">
					<thead>
						<tr>
							<th width="500">
								
								<h3><?=$prodValue['nome']?></h3>

							</th>
							<?php foreach ($dataTipos as $keyTipo => $keyValue): ?>
									<th width="200"><?=$keyValue['nome']?></th>
							<?php endforeach; ?>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>

					<?php 	$variacoes = $registros->getRegistros(DB_VARIACOES.' WHERE id_produto = '.$prodValue['id'], array('inicio' => 0, 'limite' => 999), array('campo'=>'id_variacao', 'tipo'=>'ASC'));
							if($variacoes["total"]>0):
								while ($objVariacoes = mysql_fetch_object($variacoes['result'])):	?>

									<tr>
										<td><?=$objVariacoes->altura.'x'.$objVariacoes->comprimento.'x'.$objVariacoes->profundidade?></td>

										<?php 	$tiposOutro = $registros->getTiposByFabrica($prodValue['id_fabrica']);
												while ($objTipos = mysql_fetch_object($tiposOutro)): ?>

												<?php 	//print "array_push($valores, array( ".$objVariacoes->id_variacao." => array( ".$objTipos->id_tipo." => '88.35'))) <br>";
														if(!empty($valores[$objVariacoes->id_variacao][$objTipos->id_tipo])): ?>

															<td class="variacao_<?=$objVariacoes->id_variacao?>-tipo_<?=$objTipos->id_tipo?>">
																<input type="text" name="<?=$valores[$objVariacoes->id_variacao][$objTipos->id_tipo]['id']?>" value="<?=$valores[$objVariacoes->id_variacao][$objTipos->id_tipo]['valor']?>" />
															</td>

												<?php 	else: ?>

															<td>
																<input type="text" name="<?=$objVariacoes->id_variacao?>-<?=$objTipos->id_tipo?>" value="0" />
															</td>

												<?php 	endif; ?>

										<?php endwhile; ?>
										<td>
											&nbsp;
										</td>
									</tr>	
					<?php 		endwhile;
							endif; ?>
					
					</tbody>
				</table>

			<br><br>

			<a class="button action" href="<?=get_bloginfo('url')?>/wp-admin/admin.php?page=novo_ambiente_fabricas&view=produtos&value=<?=$fabrica?>"> Voltar </a>
			<input name="save" type="submit" class="right button button-primary button-large" id="publish" accesskey="p" value=" Salvar ">
			<input type="hidden" value="<?=$fabrica?>" name="fabrica" />
			<input type="hidden" value="<?=$prodValue['id']?>" name="produto" />
			
			</form>

			<br><br><br>

	<?php endforeach; ?>

</div>