<?php

namespace App\Http\Controllers;

use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PartController extends Controller
{
    public function index(Request $request): View
    {
        $query = Part::query()
            ->where('is_visible', true)
            ->with(['category', 'subcategory', 'vehicle']);

        $this->applyFilters($query, $request);

        $parts = $query->latest()->paginate(12)->withQueryString();

        return view('parts.index', [
            'parts' => $parts,
            'filterMake' => $request->get('make'),
            'filterModel' => $request->get('model'),
            'filterYear' => $request->get('year'),
            'filterCategory' => $request->get('category'),
            'filterSubcategory' => $request->get('subcategory'),
            'filterKeyword' => $request->get('keyword'),
        ]);
    }

    public function show(Part $part): View
    {
        if (!$part->is_visible) {
            abort(404);
        }
        $part->load(['category', 'subcategory', 'vehicle']);
        return view('parts.show', ['part' => $part]);
    }

    public function enquire(Request $request)
    {
        $validated = $request->validate([
            'part_id' => 'required|exists:parts,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:50',
            'message' => 'nullable|string|max:2000',
        ]);

        $part = Part::findOrFail($validated['part_id']);

        $adminEmail = config('mail.from.address', env('ADMIN_EMAIL', 'admin@example.com'));

        Mail::raw(
            "Part enquiry from website\n\n" .
            "Part: {$part->title} (ID: {$part->id}, Stock: {$part->stock_number})\n" .
            "From: {$validated['name']} <{$validated['email']}>\n" .
            "Phone: " . ($validated['phone'] ?? 'N/A') . "\n\n" .
            "Message:\n" . ($validated['message'] ?? 'No message'),
            function ($message) use ($adminEmail, $part) {
                $message->to($adminEmail)
                    ->subject('Part Enquiry: ' . $part->title);
            }
        );

        return back()->with('success', 'Your enquiry has been sent. We will get back to you soon.');
    }

    protected function applyFilters($query, Request $request): void
    {
        if ($request->filled('make')) {
            $query->where('make', 'like', '%' . $request->get('make') . '%');
        }
        if ($request->filled('model')) {
            $query->where('model', 'like', '%' . $request->get('model') . '%');
        }
        if ($request->filled('year')) {
            $query->where('year', 'like', '%' . $request->get('year') . '%');
        }
        if ($request->filled('category')) {
            $query->where('part_category_id', $request->get('category'));
        }
        if ($request->filled('subcategory')) {
            $query->where('part_subcategory_id', $request->get('subcategory'));
        }
        if ($request->filled('keyword')) {
            $keyword = $request->get('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%')
                    ->orWhere('stock_number', 'like', '%' . $keyword . '%');
            });
        }
    }
}
