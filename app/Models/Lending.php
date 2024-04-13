<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lending extends Model
{
    use HasFactory;
    protected $table ="lending";
    protected $connection = 'mysql';

    protected $fillable=["book","loaner","loaner_phone","date_lending","returned","date_return","date_returned","lended_by","returned_to"];
    public $timestamps = false;
    public function setCsetConnection($connection)
    {
        $this->$connection =$connection ;
    }
}
