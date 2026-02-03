<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the employee record associated with the user.
     */
    public function pegawai()
    {
        return $this->hasOne(Pegawai::class);
    }

    /**
     * Check if user is owner
     */
    public function isOwner()
    {
        return $this->role === 'owner';
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is keuangan
     */
    public function isKeuangan()
    {
        return $this->role === 'keuangan';
    }

    /**
     * Check if user can access admin features
     */
    public function canAccessAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user can access keuangan features
     */
    public function canAccessKeuangan()
    {
        return $this->role === 'keuangan';
    }

    /**
     * Check if user can access owner features
     */
    public function canAccessOwner()
    {
        return $this->role === 'owner';
    }
}
