<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicCertificate extends Model
{
    protected $fillable = [

        'teacher_id',
        'file_path',
        'description'
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class,'teacher_id');
    }
}
