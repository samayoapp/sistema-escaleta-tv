<?php

namespace App\Http\Controllers;

use App\Models\SegmentType;
use App\Config\SegmentTypes;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function index()
    {
        $productionTypes = SegmentTypes::productionTypes();

        $tiposPorProduccion = collect($productionTypes)->mapWithKeys(function ($pt) {
            return [
                $pt['value'] => SegmentType::forType($pt['value'])
                    ->orderBy('order_index')
                    ->get()
            ];
        });

        return view('admin.config', compact('productionTypes', 'tiposPorProduccion'));
    }

    public function storeSegmentType(Request $request)
    {
        $request->validate([
            'production_type' => 'required|string',
            'value'           => 'required|string|alpha_dash|uppercase|unique:segment_types,value',
            'label'           => 'required|string|max:50',
            'icon'            => 'required|string|max:10',
            'color_hex'       => 'required|string|max:7',
            'order_index'     => 'nullable|integer|min:0',
        ]);

        SegmentType::create([
            'production_type' => $request->production_type,
            'value'           => strtoupper($request->value),
            'label'           => $request->label,
            'icon'            => $request->icon,
            'color_hex'       => $request->color_hex,
            'order_index'     => $request->order_index ?? 99,
            'active'          => true,
        ]);

        return redirect('/admin/config')->with('success', 'Tipo creado correctamente.');
    }

    public function updateSegmentType(Request $request, $id)
    {
        $tipo = SegmentType::findOrFail($id);

        $request->validate([
            'label'       => 'required|string|max:50',
            'icon'        => 'required|string|max:10',
            'color_hex'   => 'required|string|max:7',
            'order_index' => 'nullable|integer|min:0',
        ]);

        $tipo->update([
            'label'       => $request->label,
            'icon'        => $request->icon,
            'color_hex'   => $request->color_hex,
            'order_index' => $request->order_index ?? $tipo->order_index,
        ]);

        return redirect('/admin/config')->with('success', 'Tipo actualizado.');
    }

    public function toggleSegmentType($id)
    {
        $tipo = SegmentType::findOrFail($id);
        $tipo->active = !$tipo->active;
        $tipo->save();

        return redirect('/admin/config')->with('success',
            $tipo->active ? 'Tipo activado.' : 'Tipo desactivado.'
        );
    }

    public function destroySegmentType($id)
    {
        SegmentType::findOrFail($id)->delete();
        return redirect('/admin/config')->with('success', 'Tipo eliminado.');
    }
}
