<?php

namespace App\Models;

use CodeIgniter\Model;

/*
 CREATE TABLE `users` (
  `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_fullname` varchar(100) UNIQUE NOT NULL,
  `user_email` varchar(100) UNIQUE NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_role` enum('admin','user') DEFAULT 'user',
  `user_is_active` tinyint(1) DEFAULT 1,
  `user_created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `user_updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) 
 */
class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = true;
    protected $returnType       = \App\Entities\UserEntity::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_fullname',
        'user_email',
        'user_password',
        'user_role',
        'user_is_active',
        'user_last_login'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'user_created_at';
    protected $updatedField  = 'user_updated_at';
    protected $deletedField  = 'user_deleted_at';

    // Validation
    protected $validationRules      = [
        'user_fullname' => 'required|alpha_space|min_length[3]|max_length[100]|is_unique[users.user_fullname]',
        'user_email'    => 'required|valid_email|is_unique[users.user_email]',
        'user_password' => 'required|min_length[8]',
        'user_role'     => 'required|in_list[admin,user]',
        'user_is_active' => 'required|boolean',
        'user_last_login' => 'permit_empty|valid_date'
    ];
    protected $validationMessages   = [
        'user_fullname' => [
            'required' => 'Fullname is required',
            'alpha_space' => 'Fullname must contain only alphabetic characters and spaces',
            'min_length' => 'Fullname must be at least 3 characters long',
            'max_length' => 'Fullname must not exceed 100 characters',
            'is_unique' => 'Fullname already exists'
        ],
        'user_email' => [
            'required' => 'Email is required',
            'valid_email' => 'Email must be a valid email address',
            'is_unique' => 'Email already exists'
        ],
        'user_password' => [
            'required' => 'Password is required',
            'min_length' => 'Password must be at least 8 characters long'
        ],
        'user_role' => [
            'required' => 'Role is required',
            'in_list' => 'Role must be either admin or user'
        ],
        'user_is_active' => [
            'required' => 'Active status is required',
            'boolean' => 'Active status must be a boolean value'
        ],
        'user_last_login' => [
            'permit_empty' => 'Last login date is not a valid date',
            'valid_date' => 'Last login date is not a valid date'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
