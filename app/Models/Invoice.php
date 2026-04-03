<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'client_name',
        'client_email',
        'client_phone',
        'due_date',
        'status',
        'tax',
        'discount',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function calculateTotal()
    {
        $subtotal = $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        $afterDiscount = $subtotal - ($subtotal * $this->discount / 100);
        $total = $afterDiscount + ($afterDiscount * $this->tax / 100);

        return round($total, 2);
    }
}
