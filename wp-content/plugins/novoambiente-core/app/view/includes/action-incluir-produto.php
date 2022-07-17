<h2>Importação de arquivos</h2>
<span class="description">Os arquivos deve estar no formato XLS</span>
<form action="<?php echo get_bloginfo('url'); ?>/wp-content/plugins/novoambiente-core/app/controller/import-produtos.php" method="post" enctype="multipart/form-data">
	<table class="form-table">
		<tr>
			<th scope="row">
				<label>Selecione a planilha de produtos que deseja importar</label>
			</th>
			<td>
				<input type="file" name="produtos" />
			<td>
		</tr>
	</table>
	<p class="submit">
		<input type="submit" value="Importar produtos" class="button button-primary" />
	</p>
</form>

<form action="<?php echo get_bloginfo('url'); ?>/wp-content/plugins/novoambiente-core/app/controller/import-acabamentos.php" method="post" enctype="multipart/form-data">
	<table class="form-table">
		<tr>
			<th scope="row">
				<label>Selecione a planilha de acabamentos que deseja importar</label>
			</th>
			<td>
				<input type="file" name="acabamentos" />
			<td>
		</tr>
	</table>
	<p class="submit">
		<input type="submit" value="Importar acabamentos" class="button button-primary" />
	</p>
</form>