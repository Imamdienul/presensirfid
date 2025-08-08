<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	public function __construct() {
        parent::__construct();
        $this->load->model('m_data');
        $this->load->model('m_api');
        date_default_timezone_set("asia/jakarta");
    }

	public function index() {
		$this->load->view('i_api');
	}

	private function get_unique_seed($student_id, $additional_factor = '') {
		$base_seed = date('Ymd') . $student_id . date('H') . $additional_factor;
		return md5($base_seed . microtime(true));
	}

	private function get_message_variants() {
    return [
        'headers' => [
            "🏫 *NOTIFIKASI ABSENSI*\n\n",
            "📚 *INFO KEHADIRAN*\n\n", 
            "🎓 *LAPORAN ABSENSI*\n\n",
            "📋 *PEMBERITAHUAN SEKOLAH*\n\n",
            "🔔 *NOTIF ABSENSI*\n\n",
            "📊 *MONITORING KEHADIRAN*\n\n"
        ],
        'greetings' => [
            "Kepada Yth. Orang Tua/Wali,\n\n",
            "Yth. Bapak/Ibu Orang Tua,\n\n",
            "Assalamu'alaikum, Orang Tua/Wali,\n\n",
            "Selamat pagi/siang/sore,\n\n",
            "Salam hormat,\n\n"
        ],
        'info_prefixes' => [
            "Kami informasikan kehadiran putra/putri Anda",
            "Berikut laporan absensi anak Bapak/Ibu",
            "Data kehadiran siswa hari ini",
            "Informasi absensi terbaru",
            "Update kehadiran siswa"
        ],
        'name_formats' => [
            "👤 *Nama*: {nama}\n",
            "🧑‍🎓 *Siswa*: {nama}\n", 
            "📛 *Nama Lengkap*: {nama}\n"
        ],
        'time_formats' => [
            "📅 *Waktu*: {hari}, {waktu}\n",
            "🗓️ *Tanggal*: {hari}, {waktu}\n",
            "⏰ *Jam*: {hari}, {waktu}\n"
        ],
        'status_masuk' => [
            "✅ *Status*: MASUK SEKOLAH",
            "🏫 *Kehadiran*: SUDAH TIBA",
            "📚 *Status*: SIAP BELAJAR",
            "🌅 *Kondisi*: MEMULAI HARI SEKOLAH"
        ],
        'status_keluar' => [
            "🏠 *Status*: PULANG SEKOLAH",
            "✅ *Kehadiran*: SELESAI BELAJAR",
            "🌇 *Kondisi*: AKHIR HARI SEKOLAH",
            "🎒 *Status*: PERJALANAN PULANG"
        ],
        'rfid_success' => [
            "✅ Menggunakan kartu RFID",
            "✅ Sistem otomatis aktif",
            "✅ Teknologi digital berfungsi baik",
            "✅ Contactless system berhasil"
        ],
        'closings' => [
            "Terima kasih atas perhatiannya",
            "Kami ucapkan terima kasih",
            "Terima kasih atas kerjasamanya",
            "Salam hormat"
        ],
        'signatures' => [
            "Salam hangat,\nTim Sekolah 🎓",
            "Hormat kami,\nManajemen Sekolah 📚", 
            "Salam pendidikan,\nStaf Sekolah 🏫"
        ],
        'random_emojis' => [
            "🌟", "⭐", "✨", "💫", "🎉", "🎊", "🏆", "🎯"
        ]
    ];
}
	private function select_variant($variants, $category, $seed, $student_id = null) {
		if (!isset($variants[$category])) {
			return '';
		}
		
		$items = $variants[$category];
		$count = count($items);
		
		$hash1 = md5($seed . $category . $student_id);
		$hash2 = sha1($seed . $category . microtime());
		$hash3 = crc32($student_id . $category . date('His'));
		$index1 = hexdec(substr($hash1, 0, 8)) % $count;
		$index2 = hexdec(substr($hash2, 0, 8)) % $count;
		$index3 = abs($hash3) % $count;
		
		$final_index = ($index1 + $index2 + $index3) % $count;
		
		return $items[$final_index];
	}

	private function add_random_formatting($text, $seed) {
		$variations = [
			function($t) { return $t; }, 
			function($t) { return $t . "\n"; }, 
			function($t) { return "\n" . $t; }, 
			function($t) { return " " . $t; },
			function($t) { return $t . " "; }, 
		];
		
		$index = hexdec(substr(md5($seed), 0, 2)) % count($variations);
		return $variations[$index]($text);
	}

	private function maybe_add_decoration($message, $variants, $seed) {
		if (hexdec(substr(md5($seed . 'decoration'), 0, 2)) % 100 < 30) {
			$decoration = $this->select_variant($variants, 'decorative_elements', $seed);
			return $decoration . "\n" . $message . "\n" . $decoration . "\n";
		}
		return $message;
	}

	private function create_whatsapp_message($nama_siswa, $keterangan, $waktu, $is_manual = false, $student_id = null) {
    $unique_seed = $this->get_unique_seed($student_id ?: $nama_siswa, $keterangan);
    $variants = $this->get_message_variants();
    
    // Format waktu yang konsisten
    $waktu_formatted = date('d/m/Y H:i:s', $waktu);
    $hari = date('l', $waktu);
    $hari_indonesia = array(
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
    );
    $hari_id = $hari_indonesia[$hari];
    
    // Pilih komponen pesan
    $header = $this->select_variant($variants, 'headers', $unique_seed, $student_id);
    $greeting = $this->select_variant($variants, 'greetings', $unique_seed, $student_id);
    $info_prefix = $this->select_variant($variants, 'info_prefixes', $unique_seed, $student_id);
    $name_format = $this->select_variant($variants, 'name_formats', $unique_seed, $student_id);
    $time_format = $this->select_variant($variants, 'time_formats', $unique_seed, $student_id);
    
    // Format nama dan waktu
    $name_line = str_replace('{nama}', $nama_siswa, $name_format);
    $time_line = str_replace(['{hari}', '{waktu}'], [$hari_id, $waktu_formatted], $time_format);
    
    // Pilih status
    $status_key = ($keterangan == 'masuk') ? 'status_masuk' : 'status_keluar';
    $status = $this->select_variant($variants, $status_key, $unique_seed, $student_id);
    
    // Emoji random
    $random_emoji = $this->select_variant($variants, 'random_emojis', $unique_seed, $student_id);
    
    // Komponen penutup
    $closing = $this->select_variant($variants, 'closings', $unique_seed, $student_id);
    $signature = $this->select_variant($variants, 'signatures', $unique_seed, $student_id);
    $footer = $this->select_variant($variants, 'footers', $unique_seed, $student_id);
    
    // Susun pesan dengan format yang rapi dan konsisten
    $message = "";
    
    // 1. Header
    $message .= $header;
    
    // 2. Greeting
    $message .= $greeting;
    
    // 3. Info prefix
    $message .= $info_prefix . ":\n\n";
    
    // 4. Detail informasi (dalam box)
    $message .= "┏━━━━━━━━━━━━━━━━━━━━━━━━┓\n";
    $message .= $name_line;
    $message .= $time_line;
    $message .= $status;
    $message .= "┗━━━━━━━━━━━━━━━━━━━━━━━━┛\n\n";
    
    // 5. Pesan semangat dengan emoji
    $message .= "$random_emoji *Semangat belajar!* $random_emoji\n\n";
    
    // 6. Info tambahan berdasarkan manual/RFID
    if ($is_manual) {
        $manual_warning = $this->select_variant($variants, 'manual_warnings', $unique_seed, $student_id);
        $message .= "⚠️ *CATATAN KHUSUS*\n";
        $message .= "Absensi dilakukan secara manual\n";
        $message .= "Mohon pastikan kartu RFID dibawa besok\n\n";
    } else {
        $rfid_success = $this->select_variant($variants, 'rfid_success', $unique_seed, $student_id);
        $message .= $rfid_success . "\n\n";
    }
    
    // 7. Penutup
    $message .= $closing . "\n";
    $message .= $signature . "\n\n";
    
    // 8. Footer (tanpa variasi yang membingungkan)
    $message .= "📱 *Sistem Absensi Digital*\n";
    $message .= "_Pesan otomatis dari sistem sekolah_";
    
    // Bersihkan pesan untuk WhatsApp
    $message = $this->clean_message_for_whatsapp($message);
    
    return $message;
}

	private function apply_final_variations($message, $seed) {
    $variations = [
        function($msg, $s) { 
            $lines = explode("\n", $msg);
            if (count($lines) > 2) {
                $pos = (hexdec(substr($s, 0, 2)) % (count($lines) - 1)) + 1;
                array_splice($lines, $pos, 0, "");
                return implode("\n", $lines);
            }
            return $msg;
        },
        function($msg, $s) { 
            $positions = [': ', '* ', '- ', '. '];
            $target = $positions[hexdec(substr($s, 2, 2)) % count($positions)];
            return str_replace($target, $target . ' ', $msg);
        },
        function($msg, $s) { 
            return str_replace(' - ', ' — ', $msg);
        },
        function($msg, $s) { 
            return $msg; 
        }
    ];
    
    $index = hexdec(substr(md5($seed), 0, 2)) % count($variations);
    return $variations[$index]($message, $seed);
}
    private function clean_message_for_whatsapp($message) {
    // Hapus karakter tidak terlihat
    $message = str_replace([
        '\u{200B}',  // Zero Width Space
        '\u{00A0}',  // Non-Breaking Space
        '\\u{200B}', 
        '\\u{00A0}'  
    ], [
        '',
        ' ',
        '',
        ' '
    ], $message);
    
    // Normalisasi spasi berlebihan dalam satu baris
    $message = preg_replace('/[^\S\n]+/', ' ', $message);
    
    // Normalisasi baris kosong berlebihan (max 2 baris kosong berturut-turut)
    $message = preg_replace('/\n{3,}/', "\n\n", $message);
    
    // Hapus spasi di awal dan akhir setiap baris
    $lines = explode("\n", $message);
    $lines = array_map('trim', $lines);
    $message = implode("\n", $lines);
    
    // Trim keseluruhan
    return trim($message);
}

	private function send_whatsapp_notification($phone_number, $message) {
		$wa_config = $this->m_api->get_whatsapp_config();
		
		if (!$wa_config || empty($wa_config->api_key)) {
			error_log("WhatsApp configuration not found or API key is empty");
			return false;
		}
		
		$phone_number = preg_replace('/[^0-9]/', '', $phone_number);
		if (substr($phone_number, 0, 1) == '0') {
			$phone_number = '62' . substr($phone_number, 1);
		}
		
		$body = array(
			"api_key" => $wa_config->api_key, 
			"receiver" => $phone_number,
			"data" => array("message" => $message)
		);
		
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => $wa_config->api_url,
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

	
	public function get_message_stats() {
		$variants = $this->get_message_variants();
		$stats = [];
		
		foreach ($variants as $category => $items) {
			$stats[$category] = count($items);
		}
		$base_combinations = 1;
		$core_categories = ['headers', 'greetings', 'info_prefixes', 'name_formats', 'time_formats', 'status_masuk', 'closings', 'signatures', 'footers'];
		
		foreach ($core_categories as $category) {
			if (isset($stats[$category])) {
				$base_combinations *= $stats[$category];
			}
		}
		
		$formatting_variations = 5; 
		$decoration_chance = 2; 
		$final_variations = 4; 
		$emoji_variations = count($variants['random_emojis']);
		$time_variants = count($variants['time_variants']);
		
		$total_combinations = $base_combinations * $formatting_variations * $decoration_chance * $final_variations * $emoji_variations * $time_variants;
		
		$stats['total_possible_combinations'] = $total_combinations;
		$stats['estimated_unique_messages_per_student'] = $total_combinations;
		
		return $stats;
	}

	public function test_whatsapp($phone = null, $nama = null, $student_id = null) {
		if (!$phone || !$nama) {
			echo json_encode([
				'status' => 'error', 
				'message' => 'Parameter phone dan nama diperlukan'
			]);
			return;
		}

		$message = $this->create_whatsapp_message(
			$nama, 
			'masuk', 
			time(), 
			false,
			$student_id ?: uniqid()
		);

		$result = $this->send_whatsapp_notification($phone, $message);
		
		echo json_encode([
			'status' => 'success',
			'phone' => $phone,
			'message_sent' => $message,
			'api_response' => $result,
			'unique_seed_used' => $this->get_unique_seed($student_id ?: $nama)
		]);
	}

	public function generate_message_for_date($date = null, $nama = 'Test Siswa', $status = 'masuk', $manual = false, $student_id = null) {
		$student_id = $student_id ?: uniqid();
		
		if ($date) {
			$original_date = date('Ymd');
		}

		$message = $this->create_whatsapp_message(
			$nama, 
			$status, 
			time(), 
			$manual,
			$student_id
		);
		
		echo "<h3>Pesan untuk tanggal: " . ($date ?: 'hari ini') . " (Student ID: $student_id)</h3>";
		echo "<pre>" . htmlspecialchars($message) . "</pre>";
		echo "<h3>Pesan kedua untuk student yang sama (menunjukkan variasi):</h3>";
		$message2 = $this->create_whatsapp_message(
			$nama, 
			$status, 
			time() + 1,
			$manual,
			$student_id
		);
		echo "<pre>" . htmlspecialchars($message2) . "</pre>";
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
												true, 
												$id_siswa 
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

	public function getmodejson() {
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
													false, 
													$idsiswa 
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