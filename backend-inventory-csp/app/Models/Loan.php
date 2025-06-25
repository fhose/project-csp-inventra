<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'user_id',
        'loan_date',
        'due_date',
        'return_date',
        'status',
        'quantity',
        'purpose',
        'is_extended',
        'extension_requested',
        'extension_approved',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'loan_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
        'is_extended' => 'boolean',
        'extension_requested' => 'boolean',
        'extension_approved' => 'boolean',
    ];

    /**
     * Mendapatkan data barang (item) yang dipinjam.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Mendapatkan data pengguna (user) yang meminjam.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}