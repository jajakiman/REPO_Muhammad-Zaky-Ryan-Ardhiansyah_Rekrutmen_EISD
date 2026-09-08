<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreIssueCategoryRequest;
use App\Http\Requests\Admin\UpdateIssueCategoryRequest;
use App\Models\IssueCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IssueCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.issue-categories.index', ['categories' => IssueCategory::orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('admin.issue-categories.create');
    }

    public function store(StoreIssueCategoryRequest $request): RedirectResponse
    {
        IssueCategory::create($request->validated());

        return $this->redirect('Kategori masalah berhasil ditambahkan.')
            ->with('success_modal', true);
    }

    public function edit(IssueCategory $issueCategory): View
    {
        return view('admin.issue-categories.edit', compact('issueCategory'));
    }

    public function update(UpdateIssueCategoryRequest $request, IssueCategory $issueCategory): RedirectResponse
    {
        $issueCategory->update($request->validated());

        return $this->redirect('Kategori masalah berhasil disimpan.');
    }

    public function deactivate(IssueCategory $issueCategory): RedirectResponse
    {
        $issueCategory->update(['is_active' => false]);

        return $this->redirect('Kategori masalah berhasil dinonaktifkan.');
    }

    public function status(Request $request, IssueCategory $issueCategory): RedirectResponse|JsonResponse
    {
        $issueCategory->update($request->validate(['is_active' => ['required', 'boolean']]));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Status kategori masalah berhasil diperbarui.', 'is_active' => $issueCategory->is_active]);
        }

        return $this->redirect('Status kategori masalah berhasil diperbarui.');
    }

    private function redirect(string $message): RedirectResponse
    {
        return redirect()->route('admin.issue-categories.index')->with('success', $message);
    }
}
