<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use App\Models\BudgetConfiguration;
use Illuminate\Support\Facades\Validator;

class BudgetConfigurationController extends Controller
{
    public function index(Group $group)
    {
        $configuration = $group->getBudgetConfiguration();

        return view('budget-configurations.index', compact('group', 'configuration'));
    }

    public function update(Request $request, Group $group)
    {
        $configuration = $group->getBudgetConfiguration();

        $validator = Validator::make($request->all(), [
            'necessities_percentage' => 'required|numeric|min:0|max:100',
            'debts_percentage' => 'required|numeric|min:0|max:100',
            'future_percentage' => 'required|numeric|min:0|max:100',
            'desires_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $validator->after(function ($validator) use ($request) {
            $sum = (float)$request->necessities_percentage +
                   (float)$request->debts_percentage +
                   (float)$request->future_percentage +
                   (float)$request->desires_percentage;

            if (abs($sum - 100) > 0.01) {
                $validator->errors()->add('total', 'La suma de los porcentajes debe ser exactamente 100%. Actualmente es ' . $sum . '%.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $configuration->update($validator->validated());

        return redirect()->route('budget-configurations.index', $group)
            ->with('success', 'Configuración de presupuesto actualizada correctamente.');
    }
}
