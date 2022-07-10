<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOTools;
use Blade;
use DB;
use View;

class ProjectController extends Controller
{
    const VALIDATION_RULES = [
        'name' => 'required|string|max:255',
        'cost' => 'required|numeric|min:1.00',
        'info' => 'required|string|max:255',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Category $category)
    {
        SEOTools::setTitle(__('nav.create_project'));

        return view('project.create', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Category $category)
    {
        $proj = $category
            ->projects()
            ->create(request()->validate(self::VALIDATION_RULES));

        return redirect()->route('projects.show', [
            $category->slug,
            $proj->slug,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category, Project $project)
    {
        dd($project);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category, Project $project)
    {
        SEOTools::setTitle(__('nav.edit_project') . ' ' . $project->name);

        return view('project.edit', compact('category', 'project'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(
        Request $request,
        Category $category,
        Project $project
    ) {
        $project->update(request()->validate(self::VALIDATION_RULES));

        return redirect()->route('projects.show', [
            $category->slug,
            $project->slug,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category, Project $project)
    {
        $project->delete();

        return response()->noContent();
    }

    public function invite(Category $category, Project $project)
    {
        $req = request()->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::firstWhere('email', '=', $req['email']);

        if (!$project->addToTeam($user)) {
            // user already member of this team
            return response()->json(
                [
                    'message' => __('project.is_team'),
                ],
                422
            );
        }

        $html = file_get_contents(View::getFinder()->find('components.avatar'));

        return Blade::render(
            str_replace(
                ["@props(['src', 'title', 'id' => 'a' . random_int(1, 6999)])"],
                '',
                $html
            ),
            [
                'src' => $user->avatar,
                'id' => 'a' . random_int(1, 9999),
                'title' => $user->name,
                'attributes' => "alt='" . $user->name . " avatar'",
            ]
        );
    }
}
