<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $sections = config('faq.sections');
        $questionsBySection = FaqQuestion::orderBy('sort_order')->orderBy('id')
            ->get()
            ->groupBy('section');

        return view('admin.faq.index', compact('sections', 'questionsBySection'));
    }

    public function create(Request $request): View
    {
        $sections = config('faq.sections');
        $defaultSection = $request->query('section', $sections[0]['slug']);

        return view('admin.faq.create', [
            'faq'            => null,
            'sections'       => $sections,
            'defaultSection' => $defaultSection,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRequest($request);

        $validated['sort_order'] = (int) FaqQuestion::where('section', $validated['section'])->max('sort_order') + 1;
        $validated['is_visible'] = $request->boolean('is_visible', true);

        FaqQuestion::create($validated);

        return redirect()->route('admin.faq.index')->with('success', 'Question créée.');
    }

    public function edit(FaqQuestion $faq): View
    {
        return view('admin.faq.edit', [
            'faq'      => $faq,
            'sections' => config('faq.sections'),
        ]);
    }

    public function update(Request $request, FaqQuestion $faq): RedirectResponse
    {
        $validated = $this->validateRequest($request);
        $validated['is_visible'] = $request->boolean('is_visible', false);

        $faq->update($validated);

        return redirect()->route('admin.faq.index')->with('success', 'Question mise à jour.');
    }

    public function destroy(FaqQuestion $faq): RedirectResponse
    {
        $faq->delete();
        return redirect()->route('admin.faq.index')->with('success', 'Question supprimée.');
    }

    public function reorder(FaqQuestion $faq, string $direction): RedirectResponse
    {
        $sibling = FaqQuestion::where('section', $faq->section)
            ->when($direction === 'up',
                fn ($q) => $q->where('sort_order', '<', $faq->sort_order)->orderBy('sort_order', 'desc'),
                fn ($q) => $q->where('sort_order', '>', $faq->sort_order)->orderBy('sort_order', 'asc'))
            ->first();

        if ($sibling) {
            $tmp = $faq->sort_order;
            $faq->update(['sort_order' => $sibling->sort_order]);
            $sibling->update(['sort_order' => $tmp]);
        }

        return redirect()->route('admin.faq.index');
    }

    public function toggleVisible(FaqQuestion $faq): RedirectResponse
    {
        $faq->update(['is_visible' => ! $faq->is_visible]);
        return redirect()->route('admin.faq.index');
    }

    private function validateRequest(Request $request): array
    {
        $sectionSlugs = collect(config('faq.sections'))->pluck('slug')->all();

        return $request->validate([
            'section'  => ['required', 'string', 'in:' . implode(',', $sectionSlugs)],
            'question' => ['required', 'string', 'max:500'],
            'answer'   => ['required', 'string'],
        ]);
    }
}
