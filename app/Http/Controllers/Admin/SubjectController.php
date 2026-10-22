<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('createdBy')->latest()->get();
        return view('adventure.admin.subjects', ['subjects' => $subjects]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:subjects,slug',
            'description' => 'nullable|string',
            'color' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $subject = Subject::create(array_merge($validated, [
            'created_by' => auth()->id(),
        ]));

        return redirect()->route('admin.subjects')->with('success', 'Subject created');
    }

    public function edit($id)
    {
        $subject = Subject::findOrFail($id);
        return view('adventure.admin.subjects-edit', ['subject' => $subject]);
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:subjects,slug,' . $subject->id,
            'description' => 'nullable|string',
        ]);
        $subject->update($validated);
        return redirect()->route('admin.subjects')->with('success', 'Updated');
    }

    public function destroy($id)
    {
        Subject::destroy($id);
        return redirect()->route('admin.subjects')->with('success', 'Deleted');
    }
}
