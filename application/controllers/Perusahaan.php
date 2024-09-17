<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ------------------------------------------------------------------------
 * CLASS NAME : Perusahaan
 * ------------------------------------------------------------------------
 *
 * @author     Muhammad Akbar <muslim.politekniktelkom@gmail.com>
 * @copyright  2016
 * @license    http://aplikasiphp.net
 *
 */

class Perusahaan extends MY_Controller
{
	public function index()
	{
		$this->load->view('perusahaan/listperusahaan');
	}

	public function ambildata()
	{
		$this->load->model('m_perusahaan');
		$dataPerusahaan = $this->m_perusahaan->ambildata('pj_perusahaan')->result();
		echo json_encode($dataPerusahaan);
	}

	public function ajax_perusahaan()
	{
		if($this->input->is_ajax_request())
		{
			$id_perusahaan = $this->input->post('id_perusahaan');
			$this->load->model('m_perusahaan');

			$data = $this->m_perusahaan->get_baris($id_perusahaan)->row();
			$json['npwp']			= ( ! empty($data->npwp)) ? $data->npwp : "<small><i>Tidak ada</i></small>";
			$json['alamat']			= ( ! empty($data->alamat)) ? preg_replace("/\r\n|\r|\n/",'<br />', $data->alamat) : "<small><i>Tidak ada</i></small>";
			$json['provinsi']			= ( ! empty($data->provinsi)) ? $data->provinsi : "<small><i>Tidak ada</i></small>";
			echo json_encode($json);
		}
	}

	public function perusahaan_json()
	{
		$this->load->model('m_perusahaan');
		$level 			= $this->session->userdata('ap_level');

		$requestData	= $_REQUEST;
		$fetch			= $this->m_perusahaan->fetch_data_perusahaan($requestData['search']['value'], $requestData['order'][0]['column'], $requestData['order'][0]['dir'], $requestData['start'], $requestData['length']);

		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];

		$data	= array();
		foreach($query->result_array() as $row)
		{
			$nestedData = array();

			$nestedData[]	= $row['nomor'];

			$nestedData[]	= $row['nama_perusahaan'];
			$nestedData[]	= $row['npwp'];
			$nestedData[]	= $row['provinsi'];
			$nestedData[]	= $row['kab_kota'];
			$nestedData[]	= $row['alamat'];

			if($level == 'admin' OR $level == 'inventory')
			{
				$nestedData[]	= "<a href='".site_url('perusahaan/edit/'.$row['id_perusahaan'])."' id='EditPerusahaan'><i class='fa fa-pencil'></i> Edit</a>";
				$nestedData[]	= "<a href='".site_url('perusahaan/hapus/'.$row['id_perusahaan'])."' id='HapusPerusahaan'><i class='fa fa-trash-o'></i> Hapus</a>";
			}

			$data[] = $nestedData;
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),
			"recordsTotal"    => intval( $totalData ),
			"recordsFiltered" => intval( $totalFiltered ),
			"data"            => $data
			);

		echo json_encode($json_data);
	}

	public function hapus($id_perusahaan)
	{
		$level = $this->session->userdata('ap_level');
		if($level == 'admin' OR $level == 'inventory')
		{
			if($this->input->is_ajax_request())
			{
				$this->load->model('m_perusahaan');
				$hapus = $this->m_perusahaan->hapus_perusahaan($id_perusahaan);
				if($hapus)
				{
					echo json_encode(array(
						"pesan" => "<font color='green'><i class='fa fa-check'></i> Data berhasil dihapus !</font>
					"));
				}
				else
				{
					echo json_encode(array(
						"pesan" => "<font color='red'><i class='fa fa-warning'></i> Terjadi kesalahan, coba lagi !</font>
					"));
				}
			}
		}
	}

	public function tambah()
	{
		$level = $this->session->userdata('ap_level');
		if($level == 'admin' OR $level == 'inventory')
		{
			if($_POST)
			{
				$this->load->library('form_validation');

				$no = 0;
				foreach($_POST['nama_perusahaan'] as $kode)
				{

					$this->form_validation->set_rules('nama_perusahaan['.$no.']','Nama Perusahaan #'.($no + 1),'trim|required|max_length[60]');
					$this->form_validation->set_rules('npwp['.$no.']','Npwp #'.($no + 1),'trim|required|max_length[250]');
					$this->form_validation->set_rules('provinsi['.$no.']','Provinsi #'.($no + 1),'trim|required|max_length[60]');
					$this->form_validation->set_rules('kab_kota['.$no.']','Kab Kota #'.($no + 1),'trim|required|max_length[60]');
					$this->form_validation->set_rules('alamat['.$no.']','Alamat #'.($no + 1),'trim|required|max_length[255]');

					// $this->form_validation->set_rules('size['.$no.']','Size Barang #'.($no + 1),'trim|required|max_length[60]|alpha_numeric_spaces');

					$no++;
				}

				$this->form_validation->set_message('required','%s harus diisi !');
				$this->form_validation->set_message('numeric','%s harus angka !');
				$this->form_validation->set_message('exist_kode','%s sudah ada di database, pilih kode lain yang unik !');
				$this->form_validation->set_message('cek_titik','%s harus angka, tidak boleh ada titik !');
				$this->form_validation->set_message('alpha_numeric_spaces', '%s Harus huruf / angka !');
				$this->form_validation->set_message('alpha_numeric', '%s Harus huruf / angka !');
				if($this->form_validation->run() == TRUE)
				{
					$this->load->model('m_perusahaan');

					$no_array = 0;
					$inserted = 0;
					foreach($_POST['nama_perusahaan'] as $k)
					{

						$nama_perusahaan 				= $_POST['nama_perusahaan'][$no_array];
						$npwp 							= $_POST['npwp'][$no_array];
						$provinsi 							= $_POST['provinsi'][$no_array];
						$kab_kota 							= $_POST['kab_kota'][$no_array];
						$alamat 							= $_POST['alamat'][$no_array];


						$insert = $this->m_perusahaan->tambah_baru($nama_perusahaan ,$npwp ,$provinsi ,$kab_kota , $alamat);
						if($insert){
							$inserted++;
						}
						$no_array++;
					}

					if($inserted > 0)
					{
						echo json_encode(array(
							'status' => 1,
							'pesan' => "<i class='fa fa-check' style='color:green;'></i> Data perusahaan berhasil dismpan."
						));
					}
					else
					{
						$this->query_error("Oops, terjadi kesalahan, coba lagi !");
					}
				}
				else
				{
					$this->input_error();
				}
			}
			else
			{

				$this->load->view('perusahaan/perusahaan_tambah');
			}
		}
		else
		{
			exit();
		}
	}

	// public function ajax_cek_kode()
	// {
	// 	if($this->input->is_ajax_request())
	// 	{
	// 		$kode = $this->input->post('kodenya');
	// 		$this->load->model('m_perusahaan');

	// 		$cek_kode = $this->m_perusahaan->cek_kode($kode);
	// 		if($cek_kode->num_rows() > 0)
	// 		{
	// 			echo json_encode(array(
	// 				'status' => 0,
	// 				'pesan' => "<font color='red'>Kode sudah ada</font>"
	// 			));
	// 		}
	// 		else
	// 		{
	// 			echo json_encode(array(
	// 				'status' => 1,
	// 				'pesan' => ''
	// 			));
	// 		}
	// 	}
	// }

	// public function exist_kode($kode)
	// {
	// 	$this->load->model('m_perusahaan');
	// 	$cek_kode = $this->m_perusahaan->cek_kode($kode);

	// 	if($cek_kode->num_rows() > 0)
	// 	{
	// 		return FALSE;
	// 	}
	// 	return TRUE;
	// }

	public function cek_titik($angka)
	{
		$pecah = explode('.', $angka);
		if(count($pecah) > 1){
			return FALSE;
		}
		return TRUE;
	}

	public function edit($id_perusahaan = NULL)
	{
		if( ! empty($id_perusahaan))
		{
			$level = $this->session->userdata('ap_level');
			if($level == 'admin' OR $level == 'inventory')
			{
				if($this->input->is_ajax_request())
				{
					$this->load->model('m_perusahaan');

					if($_POST)
					{
						$this->load->library('form_validation');



						$this->form_validation->set_rules('nama_perusahaan','Nama Perusahaan','trim|required|max_length[60]');
						$this->form_validation->set_rules('npwp','Npwp','trim|required|max_length[250]');
						$this->form_validation->set_rules('provinsi','Provinsi','trim|required|max_length[60]');
						$this->form_validation->set_rules('kab_kota','Kab Kota','trim|required|max_length[60]');
						$this->form_validation->set_rules('alamat','Alamat','trim|required|max_length[255]');


						$this->form_validation->set_message('required','%s harus diisi !');
						$this->form_validation->set_message('numeric','%s harus angka !');
						$this->form_validation->set_message('exist_kode','%s sudah ada di database, pilih kode lain yang unik !');
						$this->form_validation->set_message('cek_titik','%s harus angka, tidak boleh ada titik !');
						$this->form_validation->set_message('alpha_numeric_spaces', '%s Harus huruf / angka !');
						$this->form_validation->set_message('alpha_numeric', '%s Harus huruf / angka !');

						if($this->form_validation->run() == TRUE)
						{
							$nama_perusahaan 				= $this->input->post('nama_perusahaan');
							$npwp 							= $this->input->post('npwp');
							$provinsi 						= $this->input->post('provinsi');
							$kab_kota 						= $this->input->post('kab_kota');
							$alamat 						= $this->input->post('alamat');


							$update = $this->m_perusahaan->update_perusahaan($id_perusahaan, $nama_perusahaan, $npwp, $provinsi, $kab_kota, $alamat);
							if($update)
							{
								echo json_encode(array(
									'status' => 1,
									'pesan' => "<div class='alert alert-success'><i class='fa fa-check'></i> Data perusahaan berhasil diupdate.</div>"
								));
							}
							else
							{
								$this->query_error();
							}
						}
						else
						{
							$this->input_error();
						}
					}
					else
					{
						$dt['perusahaan'] 	= $this->m_perusahaan->get_baris($id_perusahaan)->row();
						$this->load->view('perusahaan/perusahaan_edit', $dt);
					}
				}
			}
		}
	}




}
