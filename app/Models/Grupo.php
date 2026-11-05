<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use DomainException;

class Grupo extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_ciclo',
        'ano',
        'numero_grupo',
        'abierto',
        'curso_id',
    ];
    protected function casts(): array
{
    return [
        'abierto' => 'boolean',
    ];
}

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    public function docentes(): BelongsToMany
    {
        return $this->belongsToMany(Docente::class)
            ->withTimestamps();
    }
    public function asignarDocente(int $codEmpleado): void
{
    $docente = Docente::where('cod_empleado', $codEmpleado)->firstOrFail();

    if ($this->docentes()->whereKey($docente->id)->exists()) {
        return;
    }

    if ($this->docentes()->count() >= 2) {
        throw new DomainException('Un grupo no puede tener más de dos docentes.');
    }

    $this->docentes()->attach($docente->id);
}

public function puedeAbrirse(): bool
{
    return $this->matriculas()->count() >= 15
        && $this->docentes()->exists();
}

public function abrir(): void
{
    if (! $this->puedeAbrirse()) {
        throw new DomainException(
            'El grupo requiere al menos 15 estudiantes y un docente para abrirse.'
        );
    }

    $this->abierto = true;
    $this->save();
}
    public function estudiantes(): BelongsToMany
    {
        return $this->belongsToMany(Estudiante::class, 'estudiante_matriculados')
            ->withPivot('nota')
            ->withTimestamps();
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(EstudianteMatriculado::class);
    }
}
