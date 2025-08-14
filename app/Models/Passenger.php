<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Passenger extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'surname',
        'pnr',
        'email',
        'phone',
        'document_number',
        'age',
        'contingency_id',
        'form_response_id',
    ];

    /**
     * Relación con Contingencia
     */
    public function contingency()
    {
        return $this->belongsTo(Contingency::class);
    }

    /**
     * Relación con FormResponse
     */
    public function formResponse()
    {
        return $this->belongsTo(FormResponse::class);
    }
}    
