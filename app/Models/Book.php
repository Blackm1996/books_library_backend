<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $table ="book";
    protected $connection = 'mysql';

    protected $fillable=["book_code","book_name","author",
    "category","owner","owner_phone","state","description","added_by","nbPages","coverURL", "active"];
    public $timestamps = false;
    public function setCsetConnection($connection)
    {
        $this->$connection =$connection ;
    }
}
