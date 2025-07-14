<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Face_model extends CI_Model {

    public function save_face_data($name, $descriptor, $photo_base64) {
        $data = [
        'name' => $name,
        'descriptor' => json_encode($descriptor),
        'photo_base64' => $photo_base64
        ];
        return $this->db->insert('face_data', $data);
    }

    public function get_all_faces()
    {
        return $this->db->select('name, descriptor')->from('face_data')->get()->result_array();
    }

    public function get_all_faces_list() {
    return $this->db->get('face_data')->result();
}

}
