<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Position;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Role constants
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_DRIVER = 'driver';
    const ROLE_STAFF = 'staff';
    const ROLE_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'username',
        'email',
        'birthday',
        'password',
        'phone',
        'role',
        'status',
        'avatar',
        'employee_code',
        'position_id',
        'department_id',
        'salary_base',
        'id_number',
        'profile_image',
        'address',
        'gender',
        'notes'
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

    /**
     * Get all available roles
     *
     * @return array
     */
    public static function getRoles()
    {
        return [
            self::ROLE_ADMIN => 'Quản trị viên',
            self::ROLE_MANAGER => 'Quản lý',
            self::ROLE_DRIVER => 'Tài xế',
            self::ROLE_STAFF => 'Nhân viên',
            self::ROLE_USER => 'Người dùng'
        ];
    }

    /**
     * Get the position that owns the user.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Get the driver that owns the license.
     */
    public function license(): HasOne
    {
        return $this->hasOne(DriverLicense::class, 'user_id');
    }

    /**
     * Generate employee code for the user
     *
     * @return string|null
     */
    public function generateEmployeeCode(): ?string
    {
        // Nếu đã có mã, không tạo mới
        if ($this->employee_code) {
            return $this->employee_code;
        }

        // Nếu người dùng không có chức vụ, không tạo mã
        if (!$this->position_id) {
            return null;
        }

        // Tạo mã mới
        $position = $this->position;
        $this->employee_code = $position->getNextEmployeeCode();
        $this->save();

        return $this->employee_code;
    }

    /**
     * Assign position to user
     *
     * @param int $positionId
     * @param bool $generateCode
     * @return bool
     */
    public function assignPosition($positionId, $generateCode = true): bool
    {
        $this->position_id = $positionId;
        $saved = $this->save();

        if ($saved && $generateCode) {
            $this->generateEmployeeCode();
        }

        return $saved;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
        });
    }
}
