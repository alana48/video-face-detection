<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Face extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->database();
        $this->load->model('Face_model');
    }

    public function index()
    {
        $this->load->view('video_detection_view');
    }

    public function register()
    {
        $this->load->view('register_view');
    }
    public function list_faces()
    {
        $data['faces'] = $this->Face_model->get_all_faces_list();
        $this->load->view('face_list_view', $data);
    }

    public function match_faces()
    {
        $data['faces'] = $this->Face_model->get_all_faces();
        $this->load->view('match_face_view', $data);
    }

    public function save() {
        $input = json_decode(trim(file_get_contents('php://input')), true);

        if (empty($input['name']) || empty($input['descriptor']) || empty($input['photo_base64'])) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        return;
        }

        $saved = $this->Face_model->save_face_data($input['name'], $input['descriptor'], $input['photo_base64']);

        if ($saved) {
        echo json_encode(['status' => 'success']);
        } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data']);
        }
    }

    public function get_faces() {
        $faces = $this->Face_model->get_all_faces();
        echo json_encode($faces);
    }

    public function get_all_faces()
    {
        $this->load->model('Face_model');
        $faces = $this->Face_model->get_all_faces();

        header('Content-Type: application/json');
        echo json_encode($faces);
    }

    
}
