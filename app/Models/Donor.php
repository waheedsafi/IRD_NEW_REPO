<?php

namespace App\Models;

use App\Traits\template\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Sway\Traits\InvalidatableToken;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Donor extends Authenticatable
{
    use HasFactory, Notifiable, InvalidatableToken, Auditable;

    use InvalidatableToken;
    protected $guarded = [];
}
