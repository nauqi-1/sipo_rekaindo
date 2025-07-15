<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasApiTokens ;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'firstname',
        'lastname',
        'username',
        'email',
        'password',
        'phone_number',
        'role_id_role',
        'position_id_position',
        'director_id_director',
        'divisi_id_divisi',
        'department_id_department',
        'section_id_section',
        'unit_id_unit',
        'profile_image',
    ];
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id_role','id_role');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id_position','id_position');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id_divisi','id_divisi');
    }
    
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id_department', 'id_department');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id_section', 'id_section');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id_unit', 'id_unit');
    }
    public function director()
    {
        return $this->belongsTo(Director::class, 'director_id_director', 'id_director');
    }
}
