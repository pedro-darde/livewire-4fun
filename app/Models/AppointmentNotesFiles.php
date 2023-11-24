<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AppointmentNotesFiles extends Model
{
    use HasFactory;

    protected $table = 'appointment_notes_files';

    protected $guarded = ['id'];

    protected $appends = [
        'name',
        'full_name',
        'normalized_file_path',
        'extension'
    ];

    public function note(): BelongsTo
    {
        return $this->belongsTo(AppointmentNote::class, 'appointment_note_id');
    }

    public function getNameAttribute(): string
    {
        return "{$this->file_name}";
    }

    public function getNormalizedFilePathAttribute(): string
    {
        return "storage/uploads/notes_files/{$this->note->id}/{$this->file_name}";
    }

    public function getFullNameAttribute(): string {
        return asset($this->normalized_file_path);
    }

    public function getExtensionAttribute(): string
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    public function delete()
    {
        Storage::delete($this->normalized_file_path);
        parent::delete();
    }
}
