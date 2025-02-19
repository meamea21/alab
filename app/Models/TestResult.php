<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'name',
        'value',
        'reference',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
