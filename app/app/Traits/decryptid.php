<?php 

namespace App\Traits;

trait decryptId
{
public function decryptId($id){
    $id = decrypt($id);
    return $id;
}
}








