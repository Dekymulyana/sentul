<?php
/**
 *
 */
class Cetak extends MY_Controller
{
  public function index(){
    $id_perusahaan 	= $this->input->get('id_perusahaan');
    $id_toko_resto 	= $this->input->get('id_toko_resto');
    $no_invoice 	= $this->input->get('nomor_nota');
    $tanggal		= $this->input->get('tanggal');
    $id_kasir		= $this->input->get('id_kasir');
    $table		= $this->input->get('table');
    $id_pelanggan	= $this->input->get('id_pelanggan');
    $cash			= $this->input->get('cash');
    $catatan		= $this->input->get('catatan');
    $grand_total	= $this->input->get('grand_total');
    $no_telphone = $this->input->get('no_telphone');
    $pencarian_kode = $this->input->get('pencarian_kode');
    $this->load->model('m_user');
    $kasir = $this->m_user->get_baris($id_kasir)->row()->nama;

    $this->load->model('m_merk_barang');
    $this->load->model('m_pelanggan');
    $this->load->model('m_perusahaan');
    $this->load->model('m_user');
    $pelanggan = 'umum';
    if( ! empty($id_pelanggan))
    {
      $pelanggan = $this->m_pelanggan->get_baris($id_pelanggan)->row()->nama;
      $alamat_pelanggan = $this->m_pelanggan->get_baris($id_pelanggan)->row()->alamat;
    }
      $nama_perusahaan = $this->m_perusahaan->get_baris($id_perusahaan)->row()->nama_perusahaan;
      $npwp = $this->m_perusahaan->get_baris($id_perusahaan)->row()->npwp;
      $alamat = $this->m_perusahaan->get_baris($id_perusahaan)->row()->alamat;
      $kota = $this->m_perusahaan->get_baris($id_perusahaan)->row()->kab_kota;

      $nama_toko_resto = $this->m_merk_barang->get_baris($id_toko_resto)->row()->merk;
      $alamat_toko_resto = $this->m_merk_barang->get_baris($id_toko_resto)->row()->alamat;
      $nama_kasir = $this->m_user->get_baris($id_kasir)->row()->nama;
    if ($nama_toko_resto=="McDonald") {

        $day = $tanggal['mday'];
        $month = $tanggal['mon'];
        $year = $tanggal['year'];
        $hours = $tanggal['hours'];
        $minutes = $tanggal['minutes'];
        $seconds = $tanggal['seconds'];

        $date = $day.'/'.$month.'/'.$year.' '.$hours.':'.$minutes.':'.$seconds;
        $this->load->library('cfpdf');
        $pdf = new FPDF('P','mm',array(76,257));
        $pdf->AddPage();
        $pdf->AddFont('ARIALN','','ARIALN.php');
        $pdf->SetFont('ARIALN','',12);
        $pdf->SetMargins(0, -15, 0, 0);
        $pdf->Ln();
        $pdf->Cell(0, 5, $nama_toko_resto."'s ".$alamat_toko_resto, 0, 0, 'C');
        $pdf->Ln();
        $pdf->Cell(0, 5, $nama_perusahaan, 0, 0, 'C');
        $pdf->Ln();
        $pdf->Cell(0, 5, 'NPWP : '.$npwp, 0, 0, 'C');
        $pdf->Ln();
        $pdf->Cell(0, 5, 'Store #'.$no_invoice, 0, 0, 'C');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(0, 5, 'Crew id '.$id_kasir.' '.$kasir, 0, 0, 'L');
        $pdf->Ln();
        $pdf->Cell(0, 5, 'TAX INVOICE ', 0, 0, 'C');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('times','b',11);
        $pdf->Cell(0, 5, 'MFY Side 2 ', 0, 0, 'C');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('ARIALN','',11);
        $qty = 0;
        $no = 0;
        foreach($_GET['kode_barang'] as $kd)
        {
          if( ! empty($kd))
          {
            $qty =  $qty +  $_GET['jumlah_beli'][$no];
            $no++;
          }
        }
        $pdf->Cell(0, 5, 'ORD #'.$qty.' -REG #'.$qty.'- '.$tanggal, 0, 0, 'L');
        $pdf->Ln();
        $pdf->Cell(0, 5, 'QTY ITEM', 0, 0, 'L');
        $pdf->Cell(-2, 5, 'TOTAL', 0, 0, 'R');
        $pdf->Ln();
        $this->load->model('m_barang');
        $this->load->helper('text');
        $qty = 0;
        $no = 0;
        foreach($_GET['kode_barang'] as $kd)
        {
          if( ! empty($kd))
          {
            $nama_barang = $this->m_barang->get_id($kd)->row()->nama_barang;
            $nama_barang = character_limiter($nama_barang, 20, '..');

            $pdf->Cell(5, 5, $_GET['jumlah_beli'][$no], 0, 0, 'L');
            $pdf->Cell(10, 5, $nama_barang, 0, 0, 'L');
            $pdf->Cell(59, 5, $_GET['sub_total'][$no], 0, 0, 'R');
            $pdf->Ln();

            $qty =  $qty +  $_GET['jumlah_beli'][$no];
            $no++;
          }
        }
        $ppn = $grand_total/10;
    $pdf->Ln();
    $pdf->Cell(0, 5, 'Eat-In Tot( inc PAJAK )', 0, 0, 'L');
    $pdf->Cell(-2, 5,($ppn + $grand_total), 0, 0, 'R');
    $pdf->Ln();
    $pdf->Cell(0, 5, 'Cash Tendered', 0, 0, 'L');
    $pdf->Cell(-2, 5,$cash, 0, 0, 'R');
    $kembalian = $cash-($ppn+$grand_total);
    $pdf->Ln();
    $pdf->Cell(0, 5, 'Change', 0, 0, 'L');
    $pdf->Cell(-2, 5, $kembalian, 0, 0, 'R');
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Cell(0, 5, 'Net Sales', 0, 0, 'L');
    $pdf->Cell(-2, 5, $grand_total, 0, 0, 'R');
    $pdf->Ln();
    $pdf->Ln();
    $pdf->SetFont('ARIALN','',11);
    $pdf->Cell(0, 5, 'PAJAK YANG BERLAKU     10%', 0, 0, 'L');
    $pdf->Cell(-2, 5, $ppn, 0, 0, 'R');
    $pdf->Ln();
    $pdf->Cell(0, 5, 'Diskon khusus Nontunai di Pesan Antar', 0, 0, 'C');
    $pdf->Ln();
    $pdf->Cell(0, 5, 'www.mcdelivery.co.id dan McDelicery app', 0, 0, 'C');
    $pdf->Ln();
    $pdf->Cell(0, 5, 'Kartu Debit BRI dan Debit Mandiri 25%', 0, 0, 'C');
    $pdf->Ln();
    $pdf->Cell(0, 5, 'Kartu Kredit BRI 40%, Mastercard Indo Rp25 rb', 0, 0, 'C');
    $pdf->Ln();
    $pdf->Cell(0, 5, 'SK berlaku, klik www.mcdonalds.co.id', 0, 0, 'C');

    $pdf->Output();
  }elseif ($nama_toko_resto=="SPBU") {
    $day = $tanggal['mday'];
    $month = $tanggal['mon'];
    $year = $tanggal['year'];
    $hours = $tanggal['hours'];
    $minutes = $tanggal['minutes'];
    $seconds = $tanggal['seconds'];

    $day = date('D', strtotime($tanggal));
    $dayList = array(
      'Sun' => 'Minggu',
      'Mon' => 'Senin',
      'Tue' => 'Selasa',
      'Wed' => 'Rabu',
      'Thu' => 'Kamis',
      'Fri' => 'Jumat',
      'Sat' => 'Sabtu'
    );
    $image = base_url().'assets/img/logo/pertamina_logo.jpg';
    $image2 = base_url().'assets/img/logo/pertamina_tulisan.jpg';
    $image3 = base_url().'assets/img/logo/pertamina_pp.jpg';
    $date = $day.'/'.$month.'/'.$year.' '.$hours.':'.$minutes.':'.$seconds;
    $this->load->library('cfpdf');
    $pdf = new FPDF('P','mm',array(76,257));
    $pdf->AddPage();
    $pdf->AddFont('telidon','','telidon.php');
    $pdf->SetFont('telidon','',20);
    $pdf->SetMargins(0, -15, 0, 0);
    $pdf->Ln();
    $pdf->cell(12);
    $pdf->Image($image, 0,0,15);$pdf->Image($image2, 16,2,30);
    $pdf->Image($image3, 48,0,15);
    $pdf->Ln();
    $pdf->Cell(0, 13, $nama_toko_resto." ".$npwp, 0, 0, 'L');
    $pdf->Ln();
    $pdf->SetFont('telidon','',11);
    $pdf->Cell(0, 5, $alamat_toko_resto, 0, 0, 'L');
    $pdf->Ln();
    $pdf->Cell(0, 5,$alamat.' - '.$kota, 0, 0, 'l');
    $pdf->Ln();
    $pdf->Cell(0, 5, 'Telp: '.$no_telphone, 0, 0, 'L');
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Cell(0, 5,$dayList[$day].', '.date('d-m-Y', strtotime($tanggal)).' '.date('H:i:s', strtotime($tanggal)), 0, 0, 'L');
    $pdf->Ln();
    $pdf->Cell(0, 5, '--------------------------------------', 0, 0, 'L');
    $pdf->Ln();
    $pdf->Cell(23, 5, 'No. Nota', 0, 0, 'L');$pdf->Cell(5, 5, ':', 0, 0, 'L');$pdf->Cell(0, 5, $no_invoice, 0, 0, 'L');
    $pdf->Ln();
    $this->load->model('m_barang');
    $this->load->helper('text');
$qty = 0;
$no = 0;
    foreach($_GET['kode_barang'] as $kd)
    {
        $nama_barang = $this->m_barang->get_id($kd)->row()->nama_barang;
    $pdf->Cell(23, 5, 'Jenis BBM', 0, 0, 'L');$pdf->Cell(5, 5, ':', 0, 0, 'L');$pdf->Cell(50, 5, $nama_barang, 0, 0, 'L');
  }
  $pdf->Ln();
  foreach($_GET['kode_barang'] as $kd)
  {
      $nama_barang = $this->m_barang->get_id($kd)->row()->nama_barang;
  $pdf->Cell(23, 5, 'Liter', 0, 0, 'L');$pdf->Cell(25, 5, ':', 0, 0, 'L');$pdf->Cell(30, 5, $_GET['jumlah_beli'][0], 0, 0, 'L');
}
$pdf->Ln();
foreach($_GET['kode_barang'] as $kd)
{
    $nama_barang = $this->m_barang->get_id($kd)->row()->nama_barang;
$pdf->Cell(23, 5, 'Harga/Liter', 0, 0, 'L');$pdf->Cell(25 , 5, ':  Rp.', 0, 0, 'L');$pdf->Cell(50, 5,str_replace(',', '.', number_format($_GET['harga_satuan'][0])), 0, 0, 'L');
}
$pdf->Ln();
foreach($_GET['kode_barang'] as $kd)
{
  $pdf->SetFont('telidon','',15);
    $nama_barang = $this->m_barang->get_id($kd)->row()->nama_barang;
$pdf->Cell(23, 5, 'Total', 0, 0, 'L');$pdf->Cell(25, 5, ': Rp.', 0, 0, 'L');$pdf->Cell(50, 5,str_replace(',', '.', number_format($grand_total)), 0, 0, 'L');
}
$pdf->SetFont('telidon','',11);
$pdf->Ln();
$pdf->Cell(0, 7, '======================================', 0, 0, 'L');
$pdf->Ln();
$pdf->Cell(0, 10, 'Terimakasih dan Selamat Jalan', 0, 0, 'L');
$pdf->Output();
}elseif ($nama_toko_resto="PSS") {

          $day = $tanggal['mday'];
          $month = $tanggal['mon'];
          $year = $tanggal['year'];
          $hours = $tanggal['hours'];
          $minutes = $tanggal['minutes'];
          $seconds = $tanggal['seconds'];

          $date = $day.'/'.$month.'/'.$year.' '.$hours.':'.$minutes.':'.$seconds;
          $this->load->library('cfpdf');
          $pdf = new FPDF('P','mm',array(76,350));
          $pdf->AddPage();
          //$pdf->AddFont('arial','','arial.php');
          $pdf->SetFont('arial','b',14);
          $pdf->SetMargins(0, 0, 0, 0);
          $pdf->Ln();
          $pdf->Cell(0, 5, 'Pagi Sore Sudirman', 0, 0, 'C');
          $pdf->Ln();
          $pdf->SetFont('arial','',12);
          $pdf->Cell(0, 5, $alamat_toko_resto, 0, 0, 'C');
          $pdf->Ln();
          $pdf->Cell(0, 5, 'Tel: 323000 Fax: 361210', 0, 0, 'C');
          $pdf->Ln();
          $pdf->SetFont('arial','b',14);
          $pdf->Cell(0, 10, 'RECEIPT', 0, 0, 'C');
          $pdf->Ln();
          $pdf->SetFont('arial','',12);
          $pdf->Cell(0, 5, '----------------------------------------------------', 0, 0, 'C');
          $pdf->Ln();
          $pdf->SetFont('arial','b',16);
          $pdf->Cell(0, 5, 'Table: '.$table.'     Check : '.$no_invoice, 0, 0, 'L');
          $pdf->Ln();
          $pdf->SetFont('arial','',10);
          $pdf->Cell(0, 5, 'Open: Ali Y. Cashier : '.$kasir, 0, 0, 'L');
          $pdf->Ln();
          $pdf->Cell(0, 5, 'Time: '.date('d/m/y', strtotime($tanggal)).' '.date('H:i', strtotime($tanggal)), 0, 0, 'L');
          $pdf->Ln();
          $pdf->SetFont('arial','',11);
          $this->load->model('m_barang');
          $this->load->helper('text');
          $qty = 0;
          $no = 0;
          foreach($_GET['kode_barang'] as $kd)
          {
            if( ! empty($kd))
            {
              $nama_barang = $this->m_barang->get_id($kd)->row()->nama_barang;
              $nama_barang = character_limiter($nama_barang, 20, '..');

              $pdf->Cell(5, 5, $_GET['jumlah_beli'][$no], 0, 0, 'L');
              $pdf->Cell(10, 5, $nama_barang. ' (VIP)', 0, 0, 'L');
              $pdf->Cell(59, 5, str_replace('.', ',', number_format($_GET['sub_total'][$no])), 0, 0, 'R');
              $pdf->Ln();

              $qty =  $qty +  $_GET['jumlah_beli'][$no];
              $no++;
            }
          }
          $ppn = $grand_total/10;
          $pdf->Ln();
          $pdf->Cell(50, 5, 'Item Total(Rp) :', 0, 0, 'R');
          $pdf->Cell(24, 5,str_replace('.', ',', number_format($grand_total)), 0, 0, 'R');
          $pdf->Ln();
          $pdf->Cell(50, 5, 'Tax 10%(Rp) :', 0, 0, 'R');
          $pdf->Cell(24, 5,str_replace('.', ',', number_format($ppn)), 0, 0, 'R');
          $pdf->Ln();
          $pdf->Cell(50, 5, 'Total(Rp) :', 0, 0, 'R');
          $pdf->Cell(24, 5,str_replace('.', ',', number_format($ppn + $grand_total)), 0, 0, 'R');
          $pdf->Ln();
          $pdf->Ln();
          $pdf->Cell(0, 5, '----------------------------------------------------------------', 0, 0, 'R');
          $pdf->Ln();
          $pdf->SetFont('arial','b',14);
          $pdf->Cell(45, 5, 'Pay Amount : ', 0, 0, 'R');
          $pdf->Cell(29, 5,str_replace('.', ',', number_format($ppn + $grand_total)), 0, 0, 'R');
          $pdf->Ln();
          $pdf->SetFont('arial','',11);
          $pdf->Cell(0, 5, '----------------------------------------------------------', 0, 0, 'R');
          $pdf->Ln();
          $pdf->Cell(0, 5, 'No. of Print : '.$no, 0, 0, 'L');
          $pdf->Ln();
          $pdf->Cell(5, 5, ' ', 0, 0, 'L');
          $pdf->Cell(5, 5, 'Pay Meth Amount', 0, 0, 'L');
          $pdf->Ln();
          $pdf->Cell(30, 5, 'CASH', 0, 0, 'L');
          $pdf->Cell(5, 5, str_replace('.', ',', number_format($ppn + $grand_total)), 0, 0, 'L');
          $pdf->Ln();
          $pdf->Cell(0, 5, '----------------------------------------------------------', 0, 0, 'R');
          $pdf->Ln();
          $pdf->Cell(30, 5,'TOTAL ' , 0, 0, 'L');
          $pdf->Cell(20, 5, $grand_total, 0, 0, 'L');
          $pdf->Cell(0, 5, $cash, 0, 0, 'L');
          $kembalian = $cash-($ppn+$grand_total);
          $pdf->Ln();
          $pdf->Cell(30, 5,'CHANGES ' , 0, 0, 'L');
          $pdf->Cell(0, 5, $kembalian, 0, 0, 'L');
          $pdf->Ln();
          $pdf->Cell(0, 5, '----------------------------------------------------------', 0, 0, 'R');
          $pdf->Output();
}elseif ($nama_toko_resto="Racha Suki") {

        $day = $tanggal['mday'];
        $month = $tanggal['mon'];
        $year = $tanggal['year'];
        $hours = $tanggal['hours'];
        $minutes = $tanggal['minutes'];
        $seconds = $tanggal['seconds'];

        $date = $day.'/'.$month.'/'.$year.' '.$hours.':'.$minutes.':'.$seconds;
        $this->load->library('cfpdf');
        $pdf = new FPDF('P','mm',array(76,257));
        $pdf->AddPage();
        $pdf->AddFont('ARIALN','','ARIALN.php');
        $pdf->SetFont('ARIALN','',11);
        $pdf->SetMargins(0, -15, 0, 0);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(58, 5, '------------------------------------------------------------------------------------', 0, 0, 'L');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(58, 5, '------------------------------------------------------------------------------------', 0, 0, 'L');
        $pdf->Ln();

         $pdf->SetFont('ARIALN','',14);
        $pdf->Cell(18, 7, 'MEJA ', 0, 0, 'L');
        $pdf->Cell(0, 7, ': 001 (RWL)', 0, 0, 'L');
        $pdf->Ln();
         $pdf->SetFont('ARIALN','',11);
        $pdf->Cell(18, 7, 'Tanggal', 0, 0, 'L');
        $pdf->Cell(0, 7, ': Senin , 13 Agustus 2018', 0, 0, 'L');
        $pdf->Ln();
        $pdf->Cell(0, 5, 'RWL k4svsasa33l  13:03:32 ', 0, 0, 'L');
        $pdf->Ln();

        $pdf->Cell(0, 5, '---------------------------------------------------------------------------------', 0, 0, 'L');


        $qty = 0;
        $no = 0;
        foreach($_GET['kode_barang'] as $kd)
        {
          if( ! empty($kd))
          {
            $qty =  $qty +  $_GET['jumlah_beli'][$no];
            $no++;
          }
        }


        // $pdf->Cell(0, 5, 'QTY ITEM', 0, 0, 'L');
        // $pdf->Cell(-2, 5, 'TOTAL', 0, 0, 'R');
        $pdf->Ln();
        $this->load->model('m_barang');
        $this->load->helper('text');
        $qty = 0;
        $no = 0;
        foreach($_GET['kode_barang'] as $kd)
        {
          if( ! empty($kd))
          {
            $nama_barang = $this->m_barang->get_id($kd)->row()->nama_barang;
            $nama_barang = character_limiter($nama_barang, 20, '..');

            $pdf->Cell(5, 5, $nama_barang, 0, 0, 'L');
            $pdf->Ln();
            $pdf->Cell(20, 5, '(I)', 0, 0, 'L');
            $pdf->Cell(15, 5, $_GET['harga_satuan'][$no], 0, 0, 'L');
            $pdf->Cell(10, 5, 'x', 0, 0, 'L');
            $pdf->Cell(10, 5, $_GET['jumlah_beli'][$no], 0, 0, 'L');
            $pdf->Cell(20, 5, '=', 0, 0, 'L');
            $pdf->Cell(-2, 5, $_GET['sub_total'][$no], 0, 0, 'R');
            $pdf->Ln();

            $qty =  $qty +  $_GET['jumlah_beli'][$no];
            $no++;
          }
        }
        $ppn = $grand_total/10;
     $pdf->Cell(130, 5, '---------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
     $pdf->Ln();
    $pdf->Ln();
    $pdf->Cell(20, 5, '', 0, 0, 'L');
    $pdf->Cell(58, 5, 'Sub Total', 0, 0, 'L');
    $pdf->Cell(-5, 5, $grand_total, 0, 0, 'R');
    $pdf->Ln();
    $pdf->Cell(20, 5, '', 0, 0, 'L');
     $pdf->Cell(58, 5, 'PB1 10%', 0, 0, 'L');
    $pdf->Cell(-5, 5, $ppn, 0, 0, 'R');
     $pdf->Ln();
     $pdf->Cell(20, 5, '', 0, 0, 'L');
    $pdf->Cell(58, 5, 'Pembulatan', 0, 0, 'L');
    $pdf->Cell(-5, 5,'0', 0, 0, 'R');
    $pdf->Ln();
    // $pdf->Cell(20, 5, '', 0, 0, 'L');
    // $pdf->Cell(58, 5, 'Eat-In Tot( inc PAJAK )', 0, 0, 'L');
    // $pdf->Cell(-5, 5,($ppn + $grand_total), 0, 0, 'R');
    // $pdf->Ln();
    // $pdf->Cell(20, 5, '', 0, 0, 'L');
    // $pdf->Cell(58, 5, 'Cash Tendered', 0, 0, 'L');
    // $pdf->Cell(-5, 5,$cash, 0, 0, 'R');
    // $kembalian = $cash-($ppn+$grand_total);
    // $pdf->Ln();
    // $pdf->Cell(20, 5, '', 0, 0, 'L');
    // $pdf->Cell(58, 5, 'Change', 0, 0, 'L');
    // $pdf->Cell(-5, 5,$kembalian, 0, 0, 'R');
    $pdf->Ln();
    $pdf->Cell(20, 5, '', 0, 0, 'L');
    $pdf->Cell(58, 5, '------------------------------------------------------------------------------------', 0, 0, 'L');
    $pdf->Ln();


    $pdf->SetFont('ARIALN','',11);



    $pdf->Output();
}
}

}

 ?>
