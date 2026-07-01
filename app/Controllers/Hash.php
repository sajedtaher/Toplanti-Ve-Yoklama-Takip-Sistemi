<?php
namespace App\Controllers;

class Hash extends BaseController
{
    public function index()
    {
        echo password_hash("123456", PASSWORD_DEFAULT);
    }
}
//http://localhost:8080/hash.php <HASH>'i bu linkten aldık