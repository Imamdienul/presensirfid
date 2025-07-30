<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	public function __construct() {
        parent::__construct();
        $this->load->model('m_data');
        $this->load->model('m_api');
        date_default_timezone_set("asia/jakarta");
    }

	public function index()
	{
		$this->load->view('i_api');
	}

	private function send_whatsapp_notification($phone_number, $message) {
		
		$phone_number = preg_replace('/[^0-9]/', '', $phone_number);
		if (substr($phone_number, 0, 1) == '0') {
			$phone_number = '62' . substr($phone_number, 1);
		}
		
		$body = array(
			"api_key" => "839d1e368496506ae5f4080b1a60cfb7dd18f00e", 
			"receiver" => $phone_number,
			"data" => array("message" => $message)
		);
		
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => "https://whatsapp.gisaka.media/api/send-message",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($body),
			CURLOPT_HTTPHEADER => [
				"Accept: */*",
				"Content-Type: application/json",
			],
		]);
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		
		if ($err) {
			error_log("WhatsApp notification error: " . $err);
			return false;
		} else {
			return $response;
		}
	}

	private function create_whatsapp_message($nama_siswa, $keterangan, $waktu, $is_manual = false) {
		$waktu_formatted = date('d/m/Y H:i:s', $waktu);
		$hari = date('l', $waktu);
		$hari_indonesia = array(
			'Sunday' => 'Minggu',
			'Monday' => 'Senin',
			'Tuesday' => 'Selasa',
			'Wednesday' => 'Rabu',
			'Thursday' => 'Kamis',
			'Friday' => 'Jumat',
			'Saturday' => 'Sabtu'
		);
		$hari_id = $hari_indonesia[$hari];
		
		$status = ($keterangan == 'masuk') ? 'MASUK' : 'PULANG';
		
		$message = "🏫 *NOTIFIKASI ABSENSI SEKOLAH*\n\n";
		$message .= "Kepada Yth. Orang Tua/Wali,\n\n";
		$message .= "Kami informasikan bahwa putra/putri Anda:\n";
		$message .= "👤 *Nama*: {$nama_siswa}\n";
		$message .= "📅 *Hari/Tanggal*: {$hari_id}, {$waktu_formatted}\n";
		$message .= "⏰ *Status*: {$status}\n\n";
		
		if ($is_manual) {
			$message .= "⚠️ *PERHATIAN KHUSUS*\n";
			$message .= "Absensi dilakukan secara *MANUAL* (tanpa kartu RFID)\n\n";
			$message .= "Mohon untuk:\n";
			$message .= "• Mengingatkan anak membawa kartu RFID\n";
			$message .= "• Memastikan kartu dalam kondisi baik\n";
			$message .= "• Mengajarkan kedisiplinan dalam menggunakan fasilitas sekolah\n\n";
			$message .= "Kedisiplinan adalah kunci kesuksesan! 💪\n\n";
		} else {
			$message .= "✅ Absensi berhasil menggunakan kartu RFID\n\n";
		}
		
		$message .= "Terima kasih atas perhatian dan kerjasamanya.\n\n";
		$message .= "Salam hangat,\n";
		$message .= "Tim Sekolah 🎓\n\n";
		$message .= "_Pesan otomatis - Mohon tidak membalas_";
		
		return $message;
	}

	public function manualabsensijson() {
		if (isset($_GET['key']) && isset($_GET['iddev']) && isset($_GET['id_siswa'])) {
			$key = $this->input->get('key');
			$cekkey = $this->m_api->getkey();
	
			if ($cekkey[0]->key == $key) {
				$iddev = $this->input->get('iddev');
				$id_siswa = $this->input->get('id_siswa');
	
				$ceksiswa = $this->m_api->get_siswa_by_id($id_siswa);
				if (!$ceksiswa) {
					$notif = array('status' => 'failed', 'ket' => 'ID SISWA TIDAK DITEMUKAN');
					echo json_encode($notif);
					return;
				}
	
				$device = $this->m_api->getdevice($iddev);
				$count = count($device);
	
				if ($count > 0) {
					$hariIni = date('l');
	
					if ($hariIni == 'Sunday') {
						$notif = array('status' => 'failed', 'ket' => 'ABSENSI TIDAK TERSEDIA PADA HARI MINGGU');
						echo json_encode($notif);
						return;
					}
	
					$waktu = $this->m_api->get_waktu_by_day($hariIni);
	
					if ($waktu) {
						foreach ($waktu as $key => $value) {
							if ($value->keterangan == 'masuk') {
								$masuk = $value->waktu_operasional;
							}
							if ($value->keterangan == 'keluar') {
								$keluar = $value->waktu_operasional;
							}
						}
					} else {
						$notif = array('status' => 'failed', 'ket' => 'error waktu operasional');
						echo json_encode($notif);
						return;
					}
	
					if (isset($masuk) && isset($keluar)) {
						$masuk = explode("-", $masuk);
						$keluar = explode("-", $keluar);
	
						if (isset($masuk[0]) && isset($masuk[1]) && isset($keluar[0]) && isset($keluar[1])) {
							$masuk1 = strtotime($masuk[0]);
							$masuk2 = strtotime($masuk[1]);
							$keluar1 = strtotime($keluar[0]);
							$keluar2 = strtotime($keluar[1]);
	
							$currentTime = time();
							$absen = false;
							$ket = "";
							$respon = "";
	
							if ($masuk1 > $masuk2) {
								if (($currentTime >= $masuk1 && $currentTime <= strtotime('23:59')) || 
									($currentTime >= strtotime('00:00') && $currentTime <= $masuk2)) {
									$absen = true;
									$ket = "masuk";
									$respon = "MASUK BERHASIL";
								} else {
									$notif = array('status' => 'failed', 'ket' => 'DILUAR WAKTU');
									echo json_encode($notif);
									return;
								}
							} else {
								if ($currentTime >= $masuk1 && $currentTime <= $masuk2) {
									$absen = true;
									$ket = "masuk";
									$respon = "MASUK BERHASIL";
								} else if ($currentTime >= $keluar1 && $currentTime <= $keluar2) {
									$absen = true;
									$ket = "keluar";
									$respon = "KELUAR";
								} else {
									$notif = array('status' => 'failed', 'ket' => 'DILUAR WAKTU');
									echo json_encode($notif);
									return;
								}
							}
	
							if ($absen) {
								$today = strtotime("today");
								$tomorrow = strtotime("tomorrow");
	
								$datamasuk = $this->m_api->get_absensi("masuk", $today, $tomorrow);
								$datakeluar = $this->m_api->get_absensi("keluar", $today, $tomorrow);
								$datamasuk = $datamasuk ?: [];
								$datakeluar = $datakeluar ?: [];
	
								$datamskKeluar = array_merge($datamasuk, $datakeluar);
	
								$duplicate = 0;
								foreach ($datamskKeluar as $value) {
									if ($value->id_siswa == $id_siswa && $value->keterangan == $ket) {
										$duplicate++;
									}
								}
	
								if ($duplicate == 0) {
									$data = array(
										'id_devices' => $iddev,
										'id_siswa' => $id_siswa,
										'keterangan' => $ket,
										'created_at' => time()
									);
	
									if ($this->m_api->insert_absensi($data)) {
										$histori = array(
											'id_siswa' => $id_siswa,
											'keterangan' => $ket,
											'waktu' => time(),
											'id_devices' => $iddev
										);
										$this->m_api->insert_histori($histori);

										$siswa_data = $ceksiswa[0]; 
										if (!empty($siswa_data->telp)) {
											$message = $this->create_whatsapp_message(
												$siswa_data->nama, 
												$ket, 
												time(), 
												true 
											);
											$this->send_whatsapp_notification($siswa_data->telp, $message);
										}
	
										$notif = array('status' => 'success', 'ket' => $respon);
										echo json_encode($notif);
									} else {
										$notif = array('status' => 'failed', 'ket' => 'gagal insert absensi');
										echo json_encode($notif);
									}
								} else {
									$notif = array('status' => 'failed', 'ket' => 'SUDAH ABSENSI                .');
									echo json_encode($notif);
								}
							} else {
								$notif = array('status' => 'failed', 'ket' => 'error waktu operasional');
								echo json_encode($notif);
							}
						}
					} else {
						$notif = array('status' => 'failed', 'ket' => 'HUBUNGI STAFF                .');
						echo json_encode($notif);
					}
				} else {
					$notif = array('status' => 'failed', 'ket' => 'HUBUNGI STAFF                .');
					echo json_encode($notif);
				}
			} else {
				$notif = array('status' => 'failed', 'ket' => 'salah secret key');
				echo json_encode($notif);
			}
		} else {
			$notif = array('status' => 'failed', 'ket' => 'salah parameter');
			echo json_encode($notif);
		}
	}
	
	public function getmodejson(){
		if (isset($_GET['key']) && isset($_GET['iddev'])) {
			$key = $this->input->get('key');
			$cekkey = $this->m_api->getkey();
			if($cekkey[0]->key == $key){
				$iddev = $this->input->get('iddev');

				$data = $this->m_api->getmode($iddev);
				if (isset($data)) {
					$mode = "-";
					foreach ($data as $key => $value) {
						$mode = $value->mode;
					}
					if ($mode == "-") {
						$array = array('status' => 'warning', 'mode' => $mode, 'ket' => 'HUBUNGI STAFF                .');
						echo json_encode($array);
					}else{
						$array = array('status' => 'success', 'mode' => $mode, 'ket' => 'berhasil');
						echo json_encode($array);
					}
				}else{
					$array = array('status' => 'warning', 'mode' => $mode, 'ket' => 'HUBUNGI STAFF                .');
					echo json_encode($array);
				}
			}else{
				$array = array('status' => 'failed', 'ket' => 'salah secret key');
				echo json_encode($array);
			}
		}else{
			$array = array('status' => 'failed', 'ket' => 'salah parameter');
			echo json_encode($array);
		}
	}


	public function absensijson() {
		if (isset($_GET['key']) && isset($_GET['iddev']) && isset($_GET['siswa'])) {
			$key = $this->input->get('key');
			$cekkey = $this->m_api->getkey();
	
			if ($cekkey[0]->key == $key) {
				$iddev = $this->input->get('iddev');
				$siswa = $this->input->get('siswa');
	
				$ceksiswa = $this->m_api->checksiswa($siswa);
				$countsiswa = 0;
				$idsiswa = 0;
				foreach ($ceksiswa as $key => $value) {
					$countsiswa++;
					$idsiswa = $value->id_siswa;
				}
	
				$device = $this->m_api->getdevice($iddev);
				$count = 0;
				foreach ($device as $key => $value) {
					$count++;
				}
	
				if ($count > 0) {
					if ($countsiswa > 0) {
						$hariIni = date('l');
	
						if ($hariIni == 'Sunday') { 
							$notif = array('status' => 'failed', 'ket' => 'ABSENSI TIDAK TERSEDIA PADA HARI MINGGU');
							echo json_encode($notif);
							return;
						}
	
						
						$waktu = $this->m_api->get_waktu_by_day($hariIni);
	
						if ($waktu) {
							foreach ($waktu as $key => $value) {
								if ($value->keterangan == 'masuk') {
									$masuk = $value->waktu_operasional;
								}
								if ($value->keterangan == 'keluar') {
									$keluar = $value->waktu_operasional;
								}
							}
						} else {
							$notif = array('status' => 'failed', 'ket' => 'error waktu operasional');
							echo json_encode($notif);
							return;
						}
	
						if (isset($masuk) && isset($keluar)) {
							$masuk = explode("-", $masuk);
							$keluar = explode("-", $keluar);
							if (isset($masuk[0]) && isset($masuk[1]) && isset($keluar[0]) && isset($keluar[1])) {
								$masuk1 = strtotime($masuk[0]);
								$masuk2 = strtotime($masuk[1]);
								$keluar1 = strtotime($keluar[0]);
								$keluar2 = strtotime($keluar[1]);
	
								$currentTime = time();
								$absen = false;
								$ket = "";
								$respon = "";
	
								
								if ($masuk1 > $masuk2) { 
								
									if (($currentTime >= $masuk1 && $currentTime <= strtotime('23:59')) || 
										($currentTime >= strtotime('00:00') && $currentTime <= $masuk2)) {
										
										$absen = true;
										$ket = "masuk";
										$respon = "MASUK BERHASIL                        .";
									} else {
										$notif = array('status' => 'failed', 'ket' => 'DILUAR WAKTU                          .');
										echo json_encode($notif);
										return;
									}
								} else {
								
									if ($currentTime >= $masuk1 && $currentTime <= $masuk2) {
										$absen = true;
										$ket = "masuk";
										$respon = "MASUK BERHASIL                        .";
									} else if ($currentTime >= $keluar1 && $currentTime <= $keluar2) {
										$absen = true;
										$ket = "keluar";
										$respon = "KELUAR                             .";
									} else {
										$notif = array('status' => 'failed', 'ket' => 'DILUAR WAKTU                          .');
										echo json_encode($notif);
										return;
									}
								}
	
							
								if ($absen) {
									$today = strtotime("today");
									$tomorrow = strtotime("tomorrow");
	
									$datamasuk = $this->m_api->get_absensi("masuk", $today, $tomorrow);
									$datakeluar = $this->m_api->get_absensi("keluar", $today, $tomorrow);
	
									$duplicate = 0;
	
									if ($datamasuk) {
										foreach ($datamasuk as $key => $value) {
											if ($value->id_siswa == $idsiswa && $value->keterangan == $ket) {
												$duplicate++;
											}
										}
									}
	
									if ($datakeluar) {
										foreach ($datakeluar as $key => $value) {
											if ($value->id_siswa == $idsiswa && $value->keterangan == $ket) {
												$duplicate++;
											}
										}
									}
	
									if ($duplicate == 0) {
										$data = array(
											'id_devices' => $iddev,
											'id_siswa' => $idsiswa,
											'keterangan' => $ket,
											'created_at' => time()
										);
										if ($this->m_api->insert_absensi($data)) {
											$histori = array(
												'id_siswa' => $idsiswa,
												'keterangan' => $ket,
												'waktu' => time(),
												'id_devices' => $iddev
											);
											$this->m_api->insert_histori($histori);
											$siswa_data = $ceksiswa[0];
											if (!empty($siswa_data->telp)) {
												$message = $this->create_whatsapp_message(
													$siswa_data->nama, 
													$ket, 
													time(), 
													false 
												);
												$this->send_whatsapp_notification($siswa_data->telp, $message);
											}

											$notif = array('status' => 'success', 'ket' => $respon);
											echo json_encode($notif);
										} else {
											$notif = array('status' => 'failed', 'ket' => 'gagal insert absensi');
											echo json_encode($notif);
										}
									} else {
										$notif = array('status' => 'failed', 'ket' => 'SUDAH ABSENSI                .');
										echo json_encode($notif);
									}
								} else {
									$notif = array('status' => 'failed', 'ket' => 'error waktu operasional');
									echo json_encode($notif);
								}
							}
						} else {
							$notif = array('status' => 'failed', 'ket' => 'HUBUNGI STAFF                ..');
							echo json_encode($notif);
						}
					} else {
						$notif = array('status' => 'failed', 'ket' => 'HUBUNGI STAFF                .');
						echo json_encode($notif);
					}
				} else {
					$notif = array('status' => 'failed', 'ket' => 'salah secret key');
					echo json_encode($notif);
				}
			}
		}
	}
	
	
}