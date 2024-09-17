<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ------------------------------------------------------------------------
 * CLASS NAME : Secure
 * ------------------------------------------------------------------------
 *
 * @author     Muhammad Akbar <muslim.politekniktelkom@gmail.com>
 * @copyright  2016
 * @license    http://aplikasiphp.net
 *
 */

class Secure extends MY_Controller 
{
	public function index()
	{
		$this->load->helper('captcha');
			$random_number = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
        	// setting up captcha config
        	$vals = array(
            	'word'       => $random_number,
            	'img_path'   => './captcha/',
            	'img_url'    => base_url() . 'captcha/',
            	'img_width'  => 140,
            	'img_height' => 32,
            	'expiration' => 7200,
            	'pool'       => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            	'colors'     => array(
                	'background' => array(255, 255, 255),
                	'border'     => array(255, 255, 255),
                	'text'       => array(111, 34, 45),
                	'grid'       => array(255, 40, 40),
            	),
        	);
        	$data            = array();
        	$data['captcha'] = create_captcha($vals);
        	$this->session->set_userdata('captcha', $data['captcha']['word']);
			
		if($this->input->is_ajax_request())
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('username','Username','trim|required|min_length[3]|max_length[40]');
			$this->form_validation->set_rules('password','Password','trim|required|min_length[3]|max_length[40]');
			$this->form_validation->set_rules('captcha','Captcha','trim|required');
			$this->form_validation->set_message('required','%s harus diisi !');
			
			if($this->form_validation->run() == TRUE)
			{
				$username 	= $this->input->post('username');
				$password	= $this->input->post('password');
				$inputCaptcha = preg_replace("/[^a-zA-Z]/", "", $this->input->post('captcha'));
				$sessCaptcha = preg_replace("/[^a-zA-Z]/", "", $this->session->userdata('captcha'));

				if($inputCaptcha === $sessCaptcha) {
					$this->load->model('m_user');
					$validasi_login = $this->m_user->validasi_login($username, $password);

					if($validasi_login->num_rows() > 0) {
					$data_user = $validasi_login->row();

					$session = array(
						'ap_id_user' => $data_user->id_user,
						'ap_password' => $data_user->password,
						'ap_nama' => $data_user->nama,
						'ap_level' => $data_user->level,
						'ap_level_caption' => $data_user->level_caption 
					);
					$this->session->set_userdata($session);	

					$URL_home = site_url('penjualan');
					if($data_user->level == 'inventory')
					{
						$URL_home = site_url('barang');
					}
					if($data_user->level == 'keuangan')
					{
						$URL_home = site_url('penjualan/history');
					}

					$json['status']		= 1;
					$json['url_home'] 	= $URL_home;
					echo json_encode($json);
					}
					else
					{
						$this->query_error("Login Gagal, Cek Kombinasi Username & Password !");
					}
				} else{
					$this->query_error("Login Gagal, Cek Kombinasi Captcha !");
				}
			}
			else
			{
				$this->input_error();
			}
		}
		else
		{
			$data['captcha'] = $data['captcha']['filename'];
			$this->load->view('secure/login_page',$data);
		}
	}

	public function actionLogin()
	{
		$this->load->library('form_validation');
			$this->form_validation->set_rules('username','Username','trim|required|min_length[3]|max_length[40]');
			$this->form_validation->set_rules('password','Password','trim|required|min_length[3]|max_length[40]');
			$this->form_validation->set_rules('captcha','Captcha','trim|required');
			$this->form_validation->set_message('required','%s harus diisi !');
			
			if($this->form_validation->run() == TRUE)
			{
				$username 	= $this->input->post('username');
				$password	= $this->input->post('password');
				$inputCaptcha = $this->input->post('captcha');
				$sessCaptcha = $this->session->userdata('captcha');

				if($inputCaptcha === $sessCaptcha) {
					$this->load->model('m_user');
					$validasi_login = $this->m_user->validasi_login($username, $password);

					if($validasi_login->num_rows() > 0) {
					$data_user = $validasi_login->row();

					$session = array(
						'ap_id_user' => $data_user->id_user,
						'ap_password' => $data_user->password,
						'ap_nama' => $data_user->nama,
						'ap_level' => $data_user->level,
						'ap_level_caption' => $data_user->level_caption 
					);
					$this->session->set_userdata($session);	

					$URL_home = site_url('penjualan');
					if($data_user->level == 'inventory')
					{
						$URL_home = site_url('barang');
					}
					if($data_user->level == 'keuangan')
					{
						$URL_home = site_url('penjualan/history');
					}

					$json['status']		= 1;
					$json['url_home'] 	= $URL_home;
					echo json_encode($json);
					}
					else
					{
						$this->query_error("Login Gagal, Cek Kombinasi Username & Password !");
					}
				} else{
					$this->query_error("Login Gagal, Cek Kombinasi Captcha !");
				}
			}
			else
			{
				$this->input_error();
			}
	}

	public function generate_captcha()
	{
		$this->load->helper('captcha');

		$random_number = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
        // setting up captcha config
        $vals = array(
            'word'       => $random_number,
            'img_path'   => './captcha/',
            'img_url'    => base_url() . 'captcha/',
            'img_width'  => 140,
            'img_height' => 32,
            'expiration' => 7200,
            'pool'       => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'colors'     => array(
                'background' => array(255, 255, 255),
                'border'     => array(255, 255, 255),
                'text'       => array(111, 34, 45),
                'grid'       => array(255, 40, 40),
            ),
        );
        $data            = array();
        $data['captcha'] = create_captcha($vals);
        $this->session->set_userdata('captchaWord', $data['captcha']['word']);
        $data['captcha'] = $data['captcha']['filename'];
		$this->load->view('secure/login_page',$data);
	}


	function logout()
	{
		$this->session->unset_userdata('ap_id_user');
		$this->session->unset_userdata('ap_password');
		$this->session->unset_userdata('ap_nama');
		$this->session->unset_userdata('ap_level');
		$this->session->unset_userdata('ap_level_caption');
		$this->session->unset_userdata('captcha');
		redirect();
	}
}
