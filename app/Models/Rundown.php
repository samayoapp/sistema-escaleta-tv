<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Carbon\Carbon;

class Rundown extends Model
{
    protected $fillable = [
        'show_id',
        'air_date',
        'air_time',
        'delivery_date',
        'status',
        'episode_name',
        'episode_number',
    ];

    protected $casts = [
        'air_date'      => 'date:Y-m-d',
        'delivery_date' => 'date:Y-m-d',
    ];

    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    public function segments(): HasMany
    {
        return $this->hasMany(Segment::class)->orderBy('order_index');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class)->orderBy('order_index');
    }

    /**
     * Determina si la escaleta está bloqueada por haber vencido su fecha y hora al aire.
     * Solo aplica si el tipo de producción tiene 'has_lock' activado.
     */
    public function isLocked(): bool
    {
        $show = $this->relationLoaded('show') ? $this->show : $this->show()->first();
        if (!$show || !$show->hasLock()) {
            return false;
        }

        $tz = 'America/Tegucigalpa';
        $time = ($this->air_time && $this->air_time !== '00:00:00')
            ? $this->air_time
            : ($show->hasAirTime() ? ($this->air_time ?? '00:00:00') : '23:59:59');

        $airDateStr = $this->air_date instanceof Carbon ? $this->air_date->format('Y-m-d') : (string)$this->air_date;

        try {
            $airDateTime = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $airDateStr . ' ' . $time,
                $tz
            );
            return Carbon::now($tz)->greaterThan($airDateTime->copy()->addHour());
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Retorna si la escaleta pertenece a un programa en vivo (no episódico).
     */
    public function isLive(): bool
    {
        $show = $this->relationLoaded('show') ? $this->show : $this->show()->first();
        return $show ? !$show->hasEpisode() : false;
    }

    /**
     * Retorna el título de la edición o episodio.
     * - Si tiene episode_name manual (ej. 'Edición Mediodía', 'Especial Elecciones', 'Piloto'), lo usa.
     * - Si no tiene episode_name:
     *   - Si el show es episódico: retorna 'Episodio #X' si tiene episode_number o null.
     *   - Si el show es en vivo / no episódico: retorna 'Edición [Día] [dd/mm/YYYY]'.
     */
    public function getEditionTitle(): ?string
    {
        if (!empty($this->episode_name)) {
            return $this->episode_name;
        }

        $show = $this->relationLoaded('show') ? $this->show : $this->show()->first();
        if ($show && $show->hasEpisode()) {
            return $this->episode_number ? "Episodio {$this->episode_number}" : null;
        }

        // Live / no episódico sin nombre manual: formato automático por fecha
        if ($this->air_date) {
            $date = $this->air_date instanceof Carbon ? $this->air_date->copy() : Carbon::parse($this->air_date);
            $date->locale('es');
            $dia = ucfirst($date->translatedFormat('l'));
            $fecha = $date->format('d/m/Y');
            return "Edición {$dia} {$fecha}";
        }

        return null;
    }
}