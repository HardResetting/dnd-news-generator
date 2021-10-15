<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    public static function getByIdOrRandom(int $id=null){
        if (!is_null($id)) {
            $template = Template::find($id);
        } else {
            $template = Template::all()->random();
        }

        return $template;
    }
}
