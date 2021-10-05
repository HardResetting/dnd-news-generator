<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Template;
use App\Models\TemplateVariable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return Template::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return Template::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function edit(Template $template)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Template $template)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function destroy(Template $template)
    {
        //
    }


    /////////////////////////////

    /**
     * Compile Methods
     */

    public function compile(int $id)
    {
        $template = "[@var_1(0,4000)] [@var_1,Rasse,@var_2(2,3)] [?@var_1,'fordert','fordern'] Auswanderung von [@var_3(100,200)] [@var_3, Rasse] aus der Stadt [Stadt]!";
        $variables = $this->DefineReplaceAndReturnVariableDefinition($template);

        dd($template);

        return $template;
    }

    private function DefineReplaceAndReturnVariableDefinition(&$templateString)
    {
        $regex =  "/(@[a-zA-Z0-9_]+)\(([0-9]+),([0-9]+)\)/";
        preg_match_all($regex, $templateString, $matches, PREG_SET_ORDER);

        $variables = [];
        $count = 0;
        foreach ($matches as $match) {
            $count = $count+1;
            $fullMatch = $match[0];
            $variableName = $match[1];
            $variable = 0;
            $min = $match[2];
            $max = $match[3];

            if (!array_key_exists($variableName, $variables)) {
                $variable = rand($min, $max);
                $variables[$variableName] = $variable;
            } else {
                // TODO: Disallow defining same variable twice
                // Tolerated for now and simply replaced if same Variable is generated
                $variable = $variables[$variableName];
            }

            
            $templateString = str_replace(
                $fullMatch,
                $variableName,
                $templateString,
            );
        }

        return $variables;
    }
}
