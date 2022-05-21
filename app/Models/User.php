<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

use App\Libs\HasAccessControl;
use App\Libs\AccessControl;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasAccessControl;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function hasAccess(string $access) : bool
    {
        $this->setAccessControl($this->getAccessControl());
        $accessControl = $this->getUserAccessControl();

        return $accessControl->hasAccess($access);
    }

    public function getUserPermissions() : Collection
    {
        $this->setAccessControl($this->getAccessControl());
        $accessControl = $this->getUserAccessControl();

        return $accessControl->getPermissions();
    }

    public function getUserPermissionGroups() : Collection
    {
        $this->setAccessControl($this->getAccessControl());
        $accessControl = $this->getUserAccessControl();

        return $accessControl->getPermissionGroups();
    }

    private function getAccessControl()
    {
        $user = Auth::user();

        if(!empty($user))
            return new AccessControl($user);

        return null;
    }
}
