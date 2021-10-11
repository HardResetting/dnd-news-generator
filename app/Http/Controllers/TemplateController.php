<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Template;
use App\Models\TemplateVariable;
use Exception;
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
        // \[(?<variableName>@[a-zA-Z0-9_]+)=ran\((?<min>[0-9]+),(?<max>[0-9]+)\)\]|(?<variableName2>@[a-zA-Z0-9_]+)
        $template = "[@Rasse_1=[Rasse]] und [@var_1=[ran(0,2)]] [?[@var_1],Rasse] [?[@var_1],'fordert','fordern'] Auswanderung von [@var_3=[ran(100,200)]] [?[@var_3],Rasse] aus der Stadt [Stadt]!";

        $count = 0;
        do {
            $isDirty = true;

            $template = $this->DefineAndReplaceVariables($template, $variableList, $isDirty);

            $count++;
        } while ($isDirty);

        //$template = $this->DefineAndReplaceNumberVariables($template, $variableList);
        //$template = $this->DefineAndReplaceStringVariables($template, $variableList);

        dd($template);
        return $template;
    }

    private function ParseCommandsAndVars($template)
    {
        $regex = "/\[(?<variableName>@(?:[a-zA-Z0-9_])+)\]|\[(?<command>.|[^\[]+?)\]/";
        preg_match_all($regex, $template, $matches, PREG_SET_ORDER);
        return $matches;
    }

    private function DefineAndReplaceVariables(&$template, &$variables = [], &$isDirty = true)
    {
        $commandsAndVars = $this->ParseCommandsAndVars($template);
        if (empty($commandsAndVars)) {
            $isDirty = false;
            return $template;
        }

        $commandOrVar = array_shift($commandsAndVars);

        $fullMatch  = $commandOrVar[0];
        $var        = $commandOrVar["variableName"];
        $command    = array_key_exists("command", $commandOrVar) ? $commandOrVar["command"] : "";
        $replace    = "";


        if (!empty($var)) {
            if (!array_key_exists($var, $variables)) {
                throw new Exception("Variable undefined! '" . $var . "'");
            }

            $replace = $variables[$var];
        } else if ($this->isAssignment($command, $match)) {
            $variableName   = $match["variableName"];
            $value          = $match["value"];

            $variables[$variableName] = $value;

            $replace = $value;
        } else if ($this->isRandom($command, $match)) {
            $min            = $match["min"];
            $max            = $match["max"];

            $number = rand($min, $max);

            $replace = $number;
        } else if ($this->isTableItem($command, $match)) {
            $tableName      = $match["tableName"];

            // --> DB Magic with tableName
            $value = $tableName . "-singular";

            $replace = $value;
        } else if ($this->isTernaryTable($command, $match)) {
            $tableName      = $match["tableName"];
            $number         = $match["number"];

            // --> DB Magic with tableName
            $value = $number != 1 ?  $tableName . "-Plural" : $tableName . "-Singular";

            $replace = $value;
        } else if ($this->isTernaryString($command, $match)) {
            $number         = $match["number"];
            $singular       = $match["singular"];
            $plural         = $match["plural"];

            $replace = $value = $number != 1 ? $plural : $singular;;
        } else {
            throw new Exception("Invalid Command! '" . $command . "'");
        }


        $template = $this->ReplaceFirst(
            $template,
            $fullMatch,
            $replace,
        );


        return $template;
    }

    private function isAssignment($command, &$match = [])
    {
        $regex = "/^(?<variableName>@(?:[a-zA-Z0-9_])+)=(?<value>.+)$/";

        return preg_match($regex, $command, $match);
    }

    private function isRandom($command, &$match = [])
    {
        $regex = "/^ran\((?<min>[0-9]+),(?<max>[0-9]+)\)$/";

        return preg_match($regex, $command, $match);
    }

    private function isTableItem($command, &$match = [])
    {
        $regex = "/^(?<tableName>[a-zA-Z0-9]+)$/";

        return preg_match($regex, $command, $match);
    }

    private function isTernaryTable($command, &$match = [])
    {
        $regex = "/^\?(?<number>[0-9]+),(?<tableName>[a-zA-Z0-9]+)$/";

        return preg_match($regex, $command, $match);
    }

    private function isTernaryString($command, &$match = [])
    {
        $regex = "/^\?(?<number>[0-9]+),'(?<singular>[a-zA-Z0-9]+)','(?<plural>[a-zA-Z0-9]+)'$/";

        return preg_match($regex, $command, $match);
    }



    private function ReplaceFirst(string $haystack, string $needle, $replace)
    {
        $pos = strpos($haystack, $needle);
        if ($pos !== false) {
            return substr_replace($haystack, $replace, $pos, strlen($needle));
        }
    }
}
