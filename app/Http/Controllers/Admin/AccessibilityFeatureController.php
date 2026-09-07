<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAccessibilityFeatureRequest;
use App\Http\Requests\Admin\UpdateAccessibilityFeatureRequest;
use App\Models\AccessibilityFeature;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccessibilityFeatureController extends Controller
{
    public function index(): View { return view('admin.features.index', ['features' => AccessibilityFeature::orderBy('name')->get()]); }
    public function create(): View { return view('admin.features.create'); }
    public function store(StoreAccessibilityFeatureRequest $request): RedirectResponse
    {
        AccessibilityFeature::create($request->validated());
        return $this->redirect('Fasilitas berhasil disimpan.');
    }
    public function edit(AccessibilityFeature $feature): View { return view('admin.features.edit', compact('feature')); }
    public function update(UpdateAccessibilityFeatureRequest $request, AccessibilityFeature $feature): RedirectResponse
    {
        $feature->update($request->validated());
        return $this->redirect('Fasilitas berhasil disimpan.');
    }
    public function deactivate(AccessibilityFeature $feature): RedirectResponse
    {
        $feature->update(['is_active' => false]);
        return $this->redirect('Fasilitas berhasil dinonaktifkan.');
    }
    private function redirect(string $message): RedirectResponse { return redirect()->route('admin.features.index')->with('success', $message); }
}
