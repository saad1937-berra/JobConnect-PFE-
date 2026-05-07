<?php
// ===== Cv.php =====
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    protected $table = 'cvs';

    protected $fillable = [
        'particulier_id',
        'cv_path',
    ];

    public function particulier()
    {
        return $this->belongsTo(Particulier::class, 'particulier_id');
    }
}
