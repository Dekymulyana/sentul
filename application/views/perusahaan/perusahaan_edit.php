<?php echo form_open('perusahaan/edit/'.$perusahaan->id_perusahaan, array('id' => 'FormEditPerusahaan')); ?>
<div class="form-horizontal">
	
	<div class="form-group">
		<label class="col-sm-3 control-label">Nama Perusahaan</label>
		<div class="col-sm-8">
			<?php 
			echo form_input(array(
				'name' => 'nama_perusahaan',
				'class' => 'form-control',
				'value' => $perusahaan->nama_perusahaan
			));
			
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">NPWP</label>
		<div class="col-sm-8">
			<?php 
			echo form_input(array(
				'name' => 'npwp',
				'class' => 'form-control',
				'value' => $perusahaan->npwp
			));
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Provinsi</label>
		<div class="col-sm-8">
			<?php 
			echo form_input(array(
				'name' => 'provinsi',
				'class' => 'form-control',
				'value' => $perusahaan->provinsi
			));
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Kab / Kota</label>
		<div class="col-sm-8">
			<?php 
			echo form_input(array(
				'name' => 'kab_kota',
				'class' => 'form-control',
				'value' => $perusahaan->kab_kota
			));
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Alamat</label>
		<div class="col-sm-8">
			<?php 
			echo form_input(array(
				'name' => 'alamat',
				'class' => 'form-control',
				'value' => $perusahaan->alamat
			));
			?>
		</div>
	</div>
	
	
	
</div>
<?php echo form_close(); ?>

<div id='ResponseInput'></div>

<script>
$(document).ready(function(){
	var Tombol = "<button type='button' class='btn btn-primary' id='SimpanEditPerusahaan'>Update Data</button>";
	Tombol += "<button type='button' class='btn btn-default' data-dismiss='modal'>Tutup</button>";
	$('#ModalFooter').html(Tombol);

	$('#SimpanEditPerusahaan').click(function(){
		$.ajax({
			url: $('#FormEditPerusahaan').attr('action'),
			type: "POST",
			cache: false,
			data: $('#FormEditPerusahaan').serialize(),
			dataType:'json',
			success: function(json){
				if(json.status == 1){ 
					$('#ResponseInput').html(json.pesan);
					setTimeout(function(){ 
				   		$('#ResponseInput').html('');
				    }, 3000);
					$('#my-grid').DataTable().ajax.reload( null, false );
				}
				else {
					$('#ResponseInput').html(json.pesan);
				}
			}
		});
	});
});
</script>