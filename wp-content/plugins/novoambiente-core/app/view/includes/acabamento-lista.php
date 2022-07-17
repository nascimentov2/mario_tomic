<div class="wrap">
	
	<h2>
		Lista de Acabamentos
		<a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=novo_ambiente_importar" class="add-new-h2">Importar xls</a>
	</h2>

	<?php if(isset($_SESSION['retorno'])): ?>
         		<div id="message" class="<?=$_SESSION['retorno']['classe']?>' updated below-h2">
					<p><?=$_SESSION['retorno']['mensagem']?></p>
					<?php if(isset($_SESSION['retorno']['detalhes'])): ?>
						<div class="detalhes">
							<p><?=$_SESSION['retorno']['detalhes']?></p>
						</div>
					<?php endif; ?>
				</div>
    <?php unset($_SESSION['retorno']); endif; ?>

	<?php if($consulta['total'] > 0):?>
				<div class="tablenav">
					<?php include("paginacao-core.php"); ?>
				</div><!-- End tablenav -->
	<?php endif; ?>
	
	<table class="widefat">
		<thead>
			<tr>
				<th>Id</th>
				<th>Tipo</th>
				<th>Grupo</th>
				<th>Acabamentos/Cor</th>				
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php while ($o = mysql_fetch_object($consulta['result'])): ?>
				<td><?php echo $o->id_tipo; ?></td>
				<td><?php echo $o->label; ?></td>
				<td><?php echo $o->grupo; ?></td>
				<td>
					<table cellpadding="0" cellspacing="0">
					<?php
						$acabamentosCor = $registros->getRegistros(DB_ACABAMENTOS.' WHERE id_tipo = '.$o->id_tipo, $paginate, array('campo' => 'label', 'tipo' => 'DESC'));
						
						while($acabamentoCor = mysql_fetch_object($acabamentosCor['result'])):
							
							// Amarzena a url da imagem da primeira variação para cadastrar ao produto
							
							echo "<tr style='margin:0; border:0;'>
									<td style='margin:0; border:0; width:30px;'>".$acabamentoCor->label."</td>
								</tr>";		
							
						endwhile;

					?>
					</table>
				</td>
				<td>
					<form action="<?=bloginfo('url')?>/action/registro/delAcabamento" method="post">
						<input name="itemid" value="<?=$o->id_acabamento?>" type="hidden" />
						<input class="button action" type="submit" value="Excluir" onclick="return confirm('Deseja excluir o acabamento <?=$o->label?> ?')"/>
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