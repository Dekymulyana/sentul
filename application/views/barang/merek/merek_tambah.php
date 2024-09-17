<?php echo form_open('barang/tambah-merek', array('id' => 'FormTambahMerek')); ?>
<div class='form-group'>
	<input type='text' name='merek' class='form-control' placeholder="Resto/Toko">
	<br>
	<input type='text' name='alamat' class='form-control' placeholder="Alamat 1">
	<br>
	<input type='text' name='alamat_2' class='form-control' placeholder="Alamat 2">
	<br>
	<input type='text' name='alamat_3' class='form-control' placeholder="Alamat 3">
	<br>
	<input type='text' name='provinsi' class='form-control' placeholder="Provinsi/Kota">
	<br>
	<input type='text' name='no_telphone' class='form-control' placeholder="No Telphone">
	<br>
	<input type='text' name='fax' class='form-control' placeholder="Fax">
	<br>
	<textarea class="form-control" name="keterangan" placeholder="Keterangan"></textarea>
</div>
<?php echo form_close(); ?>

<div id='ResponseInput'></div>

<script>
function TambahMerek()
{
	$.ajax({
		url: $('#FormTambahMerek').attr('action'),
		type: "POST",
		cache: false,
		data: $('#FormTambahMerek').serialize(),
		dataType:'json',
		success: function(json){
			if(json.status == 1){
				$('#ResponseInput').html(json.pesan);
				setTimeout(function(){
			   		$('#ResponseInput').html('');
			    }, 3000);
				$('#my-grid').DataTable().ajax.reload( null, false );

				$('#FormTambahMerek').each(function(){
					this.reset();
				});
			}
			else {
				$('#ResponseInput').html(json.pesan);
			}
		}
	});
}

$(document).ready(function(){
	var Tombol = "<button type='button' class='btn btn-primary' id='SimpanTambahMerek'>Simpan Data</button>";
	Tombol += "<button type='button' class='btn btn-default' data-dismiss='modal'>Tutup</button>";
	$('#ModalFooter').html(Tombol);

	$("#FormTambahMerek").find('input[type=text],textarea,select').filter(':visible:first').focus();

	$('#SimpanTambahMerek').click(function(e){
		e.preventDefault();
		TambahMerek();
	});

	$('#FormTambahMerek').submit(function(e){
		e.preventDefault();
		TambahMerek();
	});
});
</script>
