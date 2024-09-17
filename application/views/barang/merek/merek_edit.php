<?php echo form_open('barang/edit-merek/'.$merek->id_merk_barang, array('id' => 'FormEditMerek')); ?>


<div class='form-group'>
	<?php
	echo form_input(array(
		'name' => 'merek',
		'class' => 'form-control',
		'value' => $merek->merk,
		'placeholder' => 'Resto/Toko'
	));
	?>
</div>

<div class='form-group'>
	<?php
	echo form_input(array(
		'name' => 'alamat',
		'class' => 'form-control',
		'placeholder' => 'Alamat 1',
		'value' => $alamat->alamat
	));
	?>
</div>

<div class='form-group'>
	<?php
	echo form_input(array(
		'name' => 'alamat_2',
		'class' => 'form-control',
		'placeholder' => 'Alamat 2',
		'value' => $alamat->alamat_2
	));
	?>
</div>

<div class='form-group'>
	<?php
	echo form_input(array(
		'name' => 'alamat_3',
		'class' => 'form-control',
		'placeholder' => 'Alamat 3',
		'value' => $alamat->alamat_3
	));
	?>
</div>

<div class='form-group'>
	<?php
	echo form_input(array(
		'name' => 'fax',
		'class' => 'form-control',
		'placeholder' => 'Fax',
		'value' => $alamat->fax
	));
	?>
</div>

<div class='form-group'>
	<?php
	echo form_input(array(
		'name' => 'provinsi',
		'class' => 'form-control',
		'placeholder' => 'Provinsi/Kota',
		'value' => $keterangan->provinsi
	));
	?>
</div>

<div class='form-group'>
	<?php
	echo form_input(array(
		'name' => 'no_telphone',
		'class' => 'form-control',
		'placeholder' => 'No Telphone',
		'value' => $keterangan->no_telphone
	));
	?>
</div>

<div class='form-group'>
	<?php
	echo form_textarea(array(
		'name' => 'keterangan',
		'class' => 'form-control',
		'placeholder' => 'Keterangan',
		'value' => $keterangan->keterangan
	));
	?>
</div>

<?php echo form_close(); ?>

<div id='ResponseInput'></div>


<script>
function EditMerek()
{
	$.ajax({
		url: $('#FormEditMerek').attr('action'),
		type: "POST",
		cache: false,
		data: $('#FormEditMerek').serialize(),
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
}

$(document).ready(function(){
	var Tombol = "<button type='button' class='btn btn-primary' id='SimpanEditMerek'>Update Data</button>";
	Tombol += "<button type='button' class='btn btn-default' data-dismiss='modal'>Tutup</button>";
	$('#ModalFooter').html(Tombol);

	$("#FormEditMerek").find('input[type=text],textarea,select').filter(':visible:first').focus();

	$('#SimpanEditMerek').click(function(e){
		e.preventDefault();
		EditMerek();
	});

	$('#FormEditMerek').submit(function(e){
		e.preventDefault();
		EditMerek();
	});
});
</script>
