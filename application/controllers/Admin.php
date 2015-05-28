<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
	}

	// Index controller
	public function index() {
		$this->load->view('admin/index');
	}


	// bullshit login
	public function login() {
		$username = $_POST['username'];
		$password = $_POST['password'];
		if (($username === ADMIN_PAGE_DEFAULT_USERNAME) && ($password === ADMIN_PAGE_DEFAULT_PASSWORD)) {
			$data['loginStat'] = true;
		} else {
			$data['loginStat'] = false;
		}
		$this->load->view('admin/index', $data);
	}


	/// Document Manager
	public function addDocument() {
		$this->load->helper('form');
		$this->load->view('admin/addDocument');
	}

	public function editDocument($documentID) {
		$this->load->helper('form');
		$this->load->Model('Mdocument');

		$data['document'] = $this->Mdocument->getDocumentByIDWithCategory($documentID);
		$this->load->view('admin/editDocument', $data);
	}

	public function documentList() {
		$this->load->Model('Mdocument');

		$arr_column_to_get = array(TAI_LIEU_COL_MA_TAI_LIEU,
			TAI_LIEU_COL_TEN_TAI_LIEU,
			TAI_LIEU_COL_TEN_TAI_LIEU_TIENG_NHAT,
			DANH_MUC_COL_TEN_DANH_MUC,
			TAI_LIEU_COL_HINH_ANH,
			//TAI_LIEU_COL_MO_TA,
			//TAI_LIEU_COL_CHI_TIET_BAI_VIET,
			TAI_LIEU_COL_NGAY_DANG,
			TAI_LIEU_COL_LUOT_VIEW,
			TAI_LIEU_COL_GHI_CHU,
			);
		$data['list_document'] = $this->Mdocument->getAllDocumentWithCategory($arr_column_to_get);
		$this->load->view('admin/showDocumentList', $data);
	}

	public function executeAddNewDocument() {

		$config['upload_path']          = DOCUMENT_PAGE_RESOURCE_IMAGE_PATH;
		$config['allowed_types']        = 'gif|jpg|png';


        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload()) {
        	echo "Thêm ảnh thất bại";
        	$error = array('error' => $this->upload->display_errors());
        	echo "<pre>";
        	print_r($error);
        	echo "</pre>";
        	echo '<h1><a href="admin/documentList">Về trang danh sách tài liệu</a></h1>';
        } else {
            $data[TAI_LIEU_COL_HINH_ANH] = DOCUMENT_PAGE_RESOURCE_IMAGE_PATH.$this->upload->data('file_name');
            $data[TAI_LIEU_COL_TEN_TAI_LIEU] = $_POST['tenTaiLieu'];
			$data[TAI_LIEU_COL_TEN_TAI_LIEU_TIENG_NHAT] = $_POST['tenTaiLieuTiengNhat'];
			
			$data[TAI_LIEU_COL_MO_TA] = $_POST['moTaTaiLieu'];
			$data[TAI_LIEU_COL_MA_DANH_MUC] = $_POST['danhMuc'];
			$data[TAI_LIEU_COL_CHI_TIET_BAI_VIET] = $_POST['chiTiet'];
			$data[TAI_LIEU_COL_NGAY_DANG] = $_POST['ngayDang'];
			$data[TAI_LIEU_COL_GHI_CHU] = $_POST['ghiChu'];
			$data[TAI_LIEU_COL_DUONG_DAN] = $this->utf8_to_ascii_url($data[TAI_LIEU_COL_TEN_TAI_LIEU].'-'.$data[TAI_LIEU_COL_NGAY_DANG]);
	
			$this->load->model('Mdocument');
			$kq = $this->Mdocument->insertDocument($data);

			if ($kq) {
				echo "Thêm dữ liệu thành công";
				echo '<h1><a href="admin/documentList">Về trang danh sách tài liệu</a></h1>';
			} else {
				echo "Thêm dữ liệu thất bại";
				echo '<h1><a href="admin/documentList">Về trang danh sách tài liệu</a></h1>';
			}
        }
	}

	public function executeUpdateDocument($documentID, $changeImage = false) {

		if ($_FILES['userfile']['name'] !== "") {
			$config['upload_path']          = DOCUMENT_PAGE_RESOURCE_IMAGE_PATH;
			$config['allowed_types']        = 'gif|jpg|png';
			$changeImage = true;
	        $this->load->library('upload', $config);
		}
		
		echo $changeImage;




        if ( ($changeImage)&&(! $this->upload->do_upload())) {
        	echo "Thêm ảnh thất bại";
        	$error = array('error' => $this->upload->display_errors());
        	echo "<pre>";
        	print_r($error);
        	echo "</pre>";
        	echo '<h1><a href="admin/documentList">Về trang danh sách tài liệu</a></h1>';
        } else {
            if ($changeImage) $data[TAI_LIEU_COL_HINH_ANH] = DOCUMENT_PAGE_RESOURCE_IMAGE_PATH.$this->upload->data('file_name');
            $data[TAI_LIEU_COL_TEN_TAI_LIEU] = $_POST['tenTaiLieu'];
			$data[TAI_LIEU_COL_TEN_TAI_LIEU_TIENG_NHAT] = $_POST['tenTaiLieuTiengNhat'];
			
			$data[TAI_LIEU_COL_MO_TA] = $_POST['moTaTaiLieu'];
			$data[TAI_LIEU_COL_MA_DANH_MUC] = $_POST['danhMuc'];
			$data[TAI_LIEU_COL_CHI_TIET_BAI_VIET] = $_POST['chiTiet'];
			$data[TAI_LIEU_COL_NGAY_DANG] = $_POST['ngayDang'];
			$data[TAI_LIEU_COL_GHI_CHU] = $_POST['ghiChu'];
			$data[TAI_LIEU_COL_DUONG_DAN] = $this->utf8_to_ascii_url($data[TAI_LIEU_COL_TEN_TAI_LIEU].'-'.$data[TAI_LIEU_COL_NGAY_DANG]);
	
			//print_r($data);
			$this->load->model('Mdocument');
			$kq = $this->Mdocument->updateDocument($documentID, $data);

			if ($kq) {
				echo "Sửa dữ liệu thành công";
				echo '<h1><a href="admin/documentList">Về trang danh sách tài liệu</a></h1>';
			} else {
				echo "Sửa dữ liệu thất bại";
				echo '<h1><a href="admin/documentList">Về trang danh sách tài liệu</a></h1>';
			}
        }
	}

	private function utf8_to_ascii_url($temp) {
		$str = $temp;
		if(!$str) return false;
		$utf8 = array(
	            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
	            'd'=>'đ|Đ',
	            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
	            'i'=>'í|ì|ỉ|ĩ|ị|Í|Ì|Ỉ|Ĩ|Ị',
	            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
	            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
	            'y'=>'ý|ỳ|ỷ|ỹ|ỵ|Ý|Ỳ|Ỷ|Ỹ|Ỵ',
	            'ss' => 'ß',
	            '' => '%',
	            '-' => '--|---|----',

				);
		foreach($utf8 as $ascii=>$uni) {
			$str = preg_replace("/($uni)/i",$ascii,$str);
		}

		$str = strtolower($str);
		$str = preg_replace("/[^_a-zA-Z0-9 -]/", "",$str);
		$str = str_replace(array('%20', ' '), '-', $str);
		return $str;
	}
}


?>