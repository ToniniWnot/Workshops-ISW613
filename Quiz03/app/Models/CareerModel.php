<?php

namespace App\Models;

use CodeIgniter\Model;

class CareerModel extends Model
{
    protected $table         = 'careers';
    protected $returnType    = 'array';
    protected $allowedFields = ['name'];
    protected $useTimestamps = true;
}