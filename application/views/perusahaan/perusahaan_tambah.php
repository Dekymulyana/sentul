<?php echo form_open('perusahaan/tambah', array('id' => 'FormTambahPerusahaan')); ?>
<table class='table table-bordered' id='TabelTambahPerusahaan'>
	<thead>
		<tr>
			<th>#</th>

			<th>Nama Perusahaan</th>
			<th>NPWP</th>
			<th>Provinsi</th>
			<th>Kab / Kota</th>
			<th>Alamat</th>

			<th>Batal</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
<?php echo form_close(); ?>

<button id='BarisBaru' class='btn btn-default'>Baris Baru</button>
<div id='ResponseInput'></div>

<script>
$(document).ready(function(){
	var Tombol = "<button type='button' class='btn btn-primary' id='SimpanTambahPerusahaan'>Simpan Data</button>";
	Tombol += "<button type='button' class='btn btn-default' data-dismiss='modal'>Tutup</button>";
	$('#ModalFooter').html(Tombol);

	BarisBaru();

	$('#BarisBaru').click(function(){
		BarisBaru();
	});

	$('#SimpanTambahPerusahaan').click(function(e){
		e.preventDefault();

		if($(this).hasClass('disabled'))
		{
			return false;
		}
		else
		{
			if($('#FormTambahPerusahaan').serialize() !== '')
			{
				$.ajax({
					url: $('#FormTambahPerusahaan').attr('action'),
					type: "POST",
					cache: false,
					data: $('#FormTambahPerusahaan').serialize(),
					dataType:'json',
					beforeSend:function(){
						$('#SimpanTambahPerusahaan').html("Menyimpan Data, harap tunggu ...");
					},
					success: function(json){
						if(json.status == 1){
							$('.modal-dialog').removeClass('modal-lg');
							$('.modal-dialog').addClass('modal-sm');
							$('#ModalHeader').html('Sukses !');
							$('#ModalContent').html(json.pesan);
							$('#ModalFooter').html("<button type='button' class='btn btn-primary' data-dismiss='modal'>Ok</button>");
							$('#ModalGue').modal('show');
							$('#my-grid').DataTable().ajax.reload( null, false );
						}
						else {
							$('#ResponseInput').html(json.pesan);
						}

						$('#SimpanTambahPerusahaan').html('Simpan Data');
					}
				});
			}
			else
			{
				$('#ResponseInput').html('');
			}
		}
	});

	$("#FormTambahPerusahaan").find('input[type=text],textarea,select').filter(':visible:first').focus();
});

$(document).on('click', '#HapusBaris', function(e){
	e.preventDefault();
	$(this).parent().parent().remove();

	var Nomor = 1;
	$('#TabelTambahPerusahaan tbody tr').each(function(){
		$(this).find('td:nth-child(1)').html(Nomor);
		Nomor++;
	});

	$('#SimpanTambahPerusahaan').removeClass('disabled');
});

function BarisBaru()
{
	var Nomor = $('#TabelTambahPerusahaan tbody tr').length + 1;
	var Baris = "<tr>";
	Baris += "<td>"+Nomor+"</td>";

	Baris += "<td><input type='text' name='nama_perusahaan[]' class='form-control input-sm'></td>";
	Baris += "<td><input type='text' name='npwp[]' class='form-control input-sm'></td>";
	Baris += "<td><input type='text' name='provinsi[]' class='form-control input-sm'></td>";
	Baris += "<td><input type='text' name='kab_kota[]' class='form-control input-sm'></td>";
	Baris += "<td><textarea name='alamat[]' class='form-control input-sm'></textarea></td>";

	Baris += "<td align='center'><a href='#' id='HapusBaris'><i class='fa fa-times' style='color:red;'></i></a></td>";
	Baris += "</tr>";

	$('#TabelTambahPerusahaan tbody').append(Baris);
}

function check_int(evt) {
	var charCode = ( evt.which ) ? evt.which : event.keyCode;
	return ( charCode >= 48 && charCode <= 57 || charCode == 8 );
}
</script>
