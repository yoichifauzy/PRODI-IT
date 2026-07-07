<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
            'is_active' => 'boolean',
        ];
    }



    public function readAspirations(): HasMany
    {
        return $this->hasMany(Aspiration::class, 'read_by');
    }

    public function managedCurricula(): HasMany
    {
        return $this->hasMany(Curriculum::class, 'created_by');
    }

    public function createdAcademicEvents(): HasMany
    {
        return $this->hasMany(AcademicEvent::class, 'created_by');
    }

    public function createdProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function createdResearches(): HasMany
    {
        return $this->hasMany(Research::class, 'created_by');
    }

    public function createdCommunityServices(): HasMany
    {
        return $this->hasMany(CommunityService::class, 'created_by');
    }

    public function createdGalleries(): HasMany
    {
        return $this->hasMany(Gallery::class, 'created_by');
    }



    public function createdTracerAlumnis(): HasMany
    {
        return $this->hasMany(TracerAlumni::class, 'created_by');
    }

    public function createdHeroSlides(): HasMany
    {
        return $this->hasMany(HeroSlide::class, 'created_by');
    }

    public function updatedHeroSlides(): HasMany
    {
        return $this->hasMany(HeroSlide::class, 'updated_by');
    }

    public function createdLecturerStaff(): HasMany
    {
        return $this->hasMany(LecturerStaff::class, 'created_by');
    }

    public function updatedLecturerStaff(): HasMany
    {
        return $this->hasMany(LecturerStaff::class, 'updated_by');
    }
}
