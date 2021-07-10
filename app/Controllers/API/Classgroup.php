<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Models\M_Class_Group;
use CodeIgniter\API\ResponseTrait;

class Classgroup extends BaseController
{
   use ResponseTrait;
   protected $m_class_group;
   protected $rules = [
      "class" => "required|is_not_unique[tb_class.class_id]",
      "major" => "required|is_not_unique[tb_major.major_id]",
      "unit_major" => "required"
   ];
   protected $errors = [
      "class" => [
         "required" => "Kelas harus diisi.",
         "is_not_unique" => "Kelas tidak valid."
      ],
      "major" => [
         "required" => "Jurusan harus diisi.",
         "is_not_unique" => "Jurusan tidak valid."
      ],
      "unit_major" => [
         "required" => "Unit Jurusan harus diisi."
      ]
   ];

   public function __construct()
   {
      $this->m_class_group = new M_Class_Group();
   }

   public function show($class_group_code)
   {
      $result = $this->m_class_group->class_group(['class_group_code' => $class_group_code]);
      if (!$result) {
         return $this->respond([
            "message" => "Data not found!",
            "status" => 404,
            "error" => true,
            "data" => null
         ]);
      }
      return $this->respond([
         "message" => "Data found!",
         "status" => 200,
         "error" => false,
         "data" => $result
      ]);
   }

   public function create()
   {
      $validation = \Config\Services::validation();
      $validation->setRules($this->rules, $this->errors);
      if ($validation->withRequest($this->request)->run() == false) {
         return $this->respond([
            "message" => "Failed to added.",
            "status" => 400,
            "errors" => $validation->getErrors()
         ]);
      }
      parse_str(file_get_contents('php://input'), $input);
      $class_group_code = $this->m_class_group->new_class_group_code();
      $class = htmlentities($input['class'], ENT_QUOTES, 'UTF-8');
      $major = htmlentities($input['major'], ENT_QUOTES, 'UTF-8');
      $unit_major = htmlentities($input['unit_major'], ENT_QUOTES, 'UTF-8');
      $data = [
         "class_group_code" => $class_group_code,
         "class" => $class,
         "major" => $major,
         "unit_major" => $unit_major
      ];
      $result = $this->m_class_group->create_class_group($data);
      if ($result) {
         return $this->respond([
            "message" => "Added successfully.",
            "status" => 200,
            "error" => false
         ]);
      }
      return $this->respond([
         "message" => "Failed to added.",
         "status" => 400,
         "error" => true
      ]);
   }

   public function update($class_group_code)
   {
      $validation = \Config\Services::validation();
      $validation->setRules($this->rules, $this->errors);
      if ($validation->withRequest($this->request)->run() == false) {
         return $this->respond([
            "message" => "Failed to save changes.",
            "status" => 400,
            "errors" => $validation->getErrors()
         ]);
      }
      parse_str(file_get_contents('php://input'), $input);
      $class = htmlentities($input['class'], ENT_QUOTES, 'UTF-8');
      $major = htmlentities($input['major'], ENT_QUOTES, 'UTF-8');
      $unit_major = htmlentities($input['unit_major'], ENT_QUOTES, 'UTF-8');
      $data = [
         "class" => $class,
         "major" => $major,
         "unit_major" => $unit_major
      ];
      $where = [
         "class_group_code" => $class_group_code
      ];
      $result = $this->m_class_group->update_class_group($data, $where);
      if ($result) {
         return $this->respond([
            "message" => "Changes saved successfully.",
            "status" => 200,
            "error" => false
         ]);
      }
      return $this->respond([
         "message" => "Failed to save changes.",
         "status" => 400,
         "error" => true
      ]);
   }

   public function delete($class_group_code)
   {
      $where = [
         "class_group_code" => $class_group_code
      ];
      try {
         $this->m_class_group->delete_class_group($where);
         return $this->respond([
            "message" => "Successfully deleted.",
            "status" => 200,
            "error" => false
         ]);
      } catch (\Exception $e) {
         return $this->respond([
            "message" => "Failed to delete.",
            "status" => 400,
            "error" => true
         ]);
      }
   }
}
