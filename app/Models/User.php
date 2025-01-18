<?php

namespace App\Models;

use App\Traits\template\Auditable;
use Sway\Traits\InvalidatableToken;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use InvalidatableToken, HasFactory, Notifiable, Auditable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];
    // protect column should be mention here

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'grant_permission' => 'boolean',
        'status' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
    public function email()
    {
        return $this->belongsTo(Email::class, 'email_id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', "id");
    }
    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id', "id");
    }
    public function job()
    {
        return $this->belongsTo(ModelJob::class, 'job_id', "id");
    }
    public function userPermissions()
    {
        return $this->hasMany(UserPermission::class, 'user_id', 'id');
    }

    public function destinationThrough()
    {
        return $this->hasOneThrough(
            Translate::class,
            Destination::class,
            'id',
            'translable_id',
            'destination_id',
            'id'
        );
    }

    public function jobThrough()
    {
        return $this->hasOneThrough(
            Translate::class,
            ModelJob::class,
            'id',
            'translable_id',
            'job_id',
            'id'
        );
    }
    // Define the relationship to the Permission model
    public function permissions()
    {
        return $this->hasManyThrough(Permission::class, UserPermission::class, 'user_id', 'name', 'id', 'permission');
    }

    public function hasPermission($permission)
    {
        return $this->permissions()->where('permission', $permission)->exists();
    }
}
