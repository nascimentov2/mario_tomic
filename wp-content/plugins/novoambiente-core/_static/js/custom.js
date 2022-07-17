(function($){
	$(function(){

		$('*[rel=tipsy]').tipsy({gravity: 's'});

		function marcardesmarcar(){
		   if ($("#todosCheck").attr("checked")){
		      $('.markCheckbox').each(
		         function(){
		            $(this).attr("checked", true);
		         }
		      );
		   }else{
		      $('.markCheckbox').each(
		         function(){
		            $(this).attr("checked", false);
		         }
		      );
		   }
		}
		
		$('form.formProdutos').submit(function(){

			var $list = $("table tr th input.markCheckbox[type=checkbox]:checked");
			var idsProdutos = '';
			$.each($list, function(){

				idsProdutos += $(this).val()+',';

			});
			
			$('#idsProdutos').val(idsProdutos);
		});

		$('.form-change-flag').change(function(){
		
			var $this = $(this);
			var numFLag = $this.attr('rel');
			var valueflag = $this.val();
			var id_produto = $this.parent().parent().attr('id').replace('produto-', '');

			// alert('Flag numero: '+numFLag+' - valor: '+valueflag+' - id_produto: '+id_produto);
			
			$this.attr('disabled', 'disabled');
			
			$.ajax({
				url: '../action/registro/editarFlag.json',
				type: 'POST',
				data: { nmf: numFLag, vlf: valueflag, idp: id_produto },
				success: function(data){
					//alert(data);
				}
			}).done(function(){
				//esconder loading;
				$this.removeAttr('disabled');		
			});

		});

	});
})(jQuery);