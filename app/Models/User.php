<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Helpers de rol ────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEditor(): bool
    {
        return in_array($this->role, ['admin', 'editor']);
    }

    public function isViewer(): bool
    {
        return in_array($this->role, ['admin', 'editor', 'viewer']);
    }

    public function rolLabel(): string
    {
        return match($this->role) {
            'admin'  => '👑 Admin',
            'editor' => '✏️ Editor',
            'viewer' => '👁 Viewer',
            default  => $this->role,
        };
    }

    public function rolColor(): string
    {
        return match($this->role) {
            'admin'  => 'text-yellow-400 bg-yellow-400/10',
            'editor' => 'text-blue-400 bg-blue-400/10',
            'viewer' => 'text-gray-400 bg-gray-700/50',
            default  => 'text-gray-400 bg-gray-700/50',
        };
    }
}
