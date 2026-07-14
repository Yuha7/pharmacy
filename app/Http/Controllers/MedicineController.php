<?php

namespace App\Http\Controllers;

use App\Http\Requests\Medicine\StoreMedicineRequest;
use App\Http\Requests\Medicine\UpdateMedicineRequest;
use App\Models\Category;
use App\Models\Medicine;

class MedicineController extends Controller
{
    public function index()
    {
        $medicines = Medicine::with('category')->latest()->paginate(15);
        return view('medicines.index', compact('medicines'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('medicines.create', compact('categories'));
    }

    public function store(StoreMedicineRequest $request)
    {
        Medicine::create($request->validated());
        return redirect()->route('medicines.index')->with('success', 'Medicine added successfully.');
    }

    public function show(Medicine $medicine)
    {
        $medicine->load('category', 'stockMovements.user');
        return view('medicines.show', compact('medicine'));
    }

    public function edit(Medicine $medicine)
    {
        $categories = Category::orderBy('name')->get();
        return view('medicines.edit', compact('medicine', 'categories'));
    }

    public function update(UpdateMedicineRequest $request, Medicine $medicine)
    {
        $medicine->update($request->validated());
        return redirect()->route('medicines.index')->with('success', 'Medicine updated successfully.');
    }

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();
        return redirect()->route('medicines.index')->with('success', 'Medicine deleted.');
    }
}
