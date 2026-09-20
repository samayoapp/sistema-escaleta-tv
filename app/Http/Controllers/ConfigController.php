<?php

namespace App\Http\Controllers;

use App\Models\SegmentType;
use App\Models\ProductionType;
use App\Config\SegmentTypes;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    // ─── Vista principal ──────────────────────────────────────────────────────

    public function index()
    {
        $segmentTypes = SegmentType::orderBy('order_index')->orderBy('label')->get();

        $productionTypes = ProductionType::orderBy('order_index')
            ->with(['segmentTypes' => fn($q) => $q->orderBy('production_type_segment_type.order_index')])
            ->get();

        // Mapa [production_type_id => [segment_type_id => pivot_active]]
        $pivotMap = [];
        foreach ($productionTypes as $pt) {
            foreach ($pt->segmentTypes as $st) {
                $pivotMap[$pt->id][$st->id] = (bool) $st->pivot->active;
            }
        }

        return view('admin.config', compact('segmentTypes', 'productionTypes', 'pivotMap'));
    }

    // ─── CRUD Tipos de Ítem ───────────────────────────────────────────────────

    public function storeSegmentType(Request $request)
    {
        $request->validate([
            'value'       => 'required|string|alpha_dash|unique:segment_types,value',
            'label'       => 'required|string|max:50',
            'icon'        => 'required|string|max:10',
            'color_hex'   => 'required|string|max:7',
            'order_index' => 'nullable|integer|min:0',
        ]);

        $st = SegmentType::create([
            'value'       => strtoupper($request->value),
            'label'       => strtoupper($request->label),
            'icon'        => $request->icon,
            'color_hex'   => $request->color_hex,
            'order_index' => $request->order_index ?? 99,
            'active'      => true,
        ]);

        // Asociar a tipos de producción seleccionados
        if ($request->has('production_type_ids')) {
            foreach ($request->production_type_ids as $ptId) {
                $st->productionTypes()->attach($ptId, [
                    'active'      => true,
                    'order_index' => $request->order_index ?? 99,
                ]);
            }
        }

        return redirect('/admin/config#segment-types')
            ->with('success', "Tipo {$st->icon} {$st->label} creado.");
    }

    public function updateSegmentType(Request $request, $id)
    {
        $st = SegmentType::findOrFail($id);
        $request->validate([
            'label'       => 'required|string|max:50',
            'icon'        => 'required|string|max:10',
            'color_hex'   => 'required|string|max:7',
            'order_index' => 'nullable|integer|min:0',
        ]);
        $st->update([
            'label'       => strtoupper($request->label),
            'icon'        => $request->icon,
            'color_hex'   => $request->color_hex,
            'order_index' => $request->order_index ?? $st->order_index,
        ]);
        return redirect('/admin/config#segment-types')->with('success', "Tipo {$st->label} actualizado.");
    }

    public function destroySegmentType($id)
    {
        $st = SegmentType::findOrFail($id);
        $label = $st->label;
        $st->delete();
        return redirect('/admin/config#segment-types')->with('success', "Tipo {$label} eliminado.");
    }

    // ─── Toggle pivote (activar/desactivar ítem en tipo de producción) ────────

    public function togglePivot(Request $request, $productionTypeId, $segmentTypeId)
    {
        $pt = ProductionType::findOrFail($productionTypeId);
        $existing = $pt->segmentTypes()->where('segment_types.id', $segmentTypeId)->first();

        if ($existing) {
            $newActive = !$existing->pivot->active;
            $pt->segmentTypes()->updateExistingPivot($segmentTypeId, ['active' => $newActive]);
        } else {
            $maxOrder = $pt->segmentTypes()->max('production_type_segment_type.order_index') ?? 0;
            $pt->segmentTypes()->attach($segmentTypeId, [
                'active'      => true,
                'order_index' => $maxOrder + 1,
            ]);
        }

        return redirect('/admin/config#segment-types')->with('success', 'Asignación actualizada.');
    }

    // ─── CRUD Tipos de Producción ─────────────────────────────────────────────

    public function storeProductionType(Request $request)
    {
        $request->validate([
            'value' => 'required|string|alpha_dash|unique:production_types,value',
            'label' => 'required|string|max:100',
            'icon'  => 'required|string|max:10',
        ]);

        $pt = ProductionType::create([
            'value'        => strtolower($request->value),
            'label'        => $request->label,
            'icon'         => $request->icon,
            'has_air_time' => $request->boolean('has_air_time'),
            'has_lock'     => $request->boolean('has_lock'),
            'has_episode'  => $request->boolean('has_episode'),
            'order_index'  => $request->order_index ?? 99,
            'active'       => true,
        ]);

        ProductionType::clearCache();
        return redirect('/admin/config#production-types')
            ->with('success', "{$pt->icon} {$pt->label} creado.");
    }

    public function updateProductionType(Request $request, $id)
    {
        $pt = ProductionType::findOrFail($id);
        $request->validate([
            'label' => 'required|string|max:100',
            'icon'  => 'required|string|max:10',
        ]);
        $pt->update([
            'label'        => $request->label,
            'icon'         => $request->icon,
            'has_air_time' => $request->boolean('has_air_time'),
            'has_lock'     => $request->boolean('has_lock'),
            'has_episode'  => $request->boolean('has_episode'),
            'order_index'  => $request->order_index ?? $pt->order_index,
        ]);
        ProductionType::clearCache();
        return redirect('/admin/config#production-types')->with('success', "{$pt->label} actualizado.");
    }

    public function toggleProductionType($id)
    {
        $pt = ProductionType::findOrFail($id);
        $pt->active = !$pt->active;
        $pt->save();
        ProductionType::clearCache();
        return redirect('/admin/config#production-types')
            ->with('success', $pt->active ? "{$pt->label} activado." : "{$pt->label} desactivado.");
    }

    public function destroyProductionType($id)
    {
        $pt = ProductionType::findOrFail($id);
        $showCount = \App\Models\Show::where('production_type', $pt->value)->count();
        if ($showCount > 0) {
            return redirect('/admin/config#production-types')
                ->with('error', "No se puede eliminar: {$showCount} show(s) usan este tipo.");
        }
        $label = $pt->label;
        $pt->delete();
        ProductionType::clearCache();
        return redirect('/admin/config#production-types')->with('success', "{$label} eliminado.");
    }
}
