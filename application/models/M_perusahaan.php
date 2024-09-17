<?php
class M_perusahaan extends CI_Model
{
	function get_all()
	{
		return $this->db
			->select('id_perusahaan, nama_perusahaan, npwp, provinsi, kab_kota, alamat')
			->where('dihapus', 'tidak')
			->order_by('nama_perusahaan','asc')
			->get('pj_perusahaan');
	}

	function get_baris($id_perusahaan)
	{
		return $this->db
			->select('id_perusahaan, nama_perusahaan, npwp, provinsi, kab_kota, alamat')
			->where('id_perusahaan', $id_perusahaan)
			->limit(1)
			->get('pj_perusahaan');
	}

	function fetch_data_perusahaan($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				id_perusahaan,
				nama_perusahaan,
				npwp,
				provinsi,
				kab_kota,
				alamat
			FROM
				`pj_perusahaan`, (SELECT @row := 0) r WHERE 1=1
				AND dihapus = 'tidak'
		";

		$data['totalData'] = $this->db->query($sql)->num_rows();

		if( ! empty($like_value))
		{
			$sql .= " AND ( ";
			$sql .= "
				nama_perusahaan LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR npwp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR provinsi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR kab_kota LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR alamat LIKE '%".$this->db->escape_like_str($like_value)."%'
			";
			$sql .= " ) ";
		}

		$data['totalFiltered']	= $this->db->query($sql)->num_rows();

		$columns_order_by = array(
			0 => 'nomor',

			1 => 'nama_perusahaan',
			2 => 'npwp',
			3 => 'provinsi',
			4 => 'kab_kota',
			5 => 'alamat',
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir.", nomor ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	function hapus_perusahaan($id_perusahaan)
	{
		$dt['dihapus'] = 'ya';
		return $this->db
				->where('id_perusahaan', $id_perusahaan)
				->update('pj_perusahaan', $dt);
	}

	function tambah_baru($nama_perusahaan ,$npwp ,$provinsi ,$kab_kota , $alamat)
	{
		$dt = array(

			'nama_perusahaan' => $nama_perusahaan,
			'npwp' => $npwp,
			'provinsi' => $provinsi,
			'kab_kota' => $kab_kota,
			'alamat' => $alamat,

		);

		return $this->db->insert('pj_perusahaan', $dt);
	}

	function cek_kode($nama_perusahaan)
	{
		return $this->db
			->select('id_perusahaan')
			->where('nama_perusahaan', $nama_perusahaan)

			->limit(1)
			->get('pj_perusahaan');
	}



	function update_perusahaan($id_perusahaan, $nama_perusahaan, $npwp, $provinsi, $kab_kota, $alamat)
	{
		$dt = array(

			'nama_perusahaan' => $nama_perusahaan,
			'npwp' => $npwp,
			'provinsi' => $provinsi,
			'kab_kota' => $kab_kota,
			'alamat' => $alamat,

		);

		return $this->db
			->where('id_perusahaan', $id_perusahaan)
			->update('pj_perusahaan', $dt);
	}

	function cari_kode($keyword, $registered)
	{
		$not_in = '';

		$koma = explode(',', $registered);
		if(count($koma) > 1)
		{
			$not_in .= " AND `kode_perusahaan` NOT IN (";
			foreach($koma as $k)
			{
				$not_in .= " '".$k."', ";
			}
			$not_in = rtrim(trim($not_in), ',');
			$not_in = $not_in.")";
		}
		if(count($koma) == 1)
		{
			$not_in .= " AND `kode_perusahaan` != '".$registered."' ";
		}

		$sql = "
			SELECT
				`kode_perusahaan`, `nama_perusahaan`
			FROM
				`pj_perusahaan`
			WHERE
				(
					`kode_perusahaan` LIKE '%".$this->db->escape_like_str($keyword)."%'
					OR `nama_perusahaan` LIKE '%".$this->db->escape_like_str($keyword)."%'
				)
				".$not_in."
		";

		return $this->db->query($sql);
	}



}
