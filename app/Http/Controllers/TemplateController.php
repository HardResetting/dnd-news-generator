<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\Type;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('web/template/index', ['templates' => Template::all(), 'types' => Type::all(), 'title' => 'Templates']);
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
        $template = new Template;
        $template->value = $request->value;
        $template->save();

        return redirect()->action([TemplateController::class, 'index']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function show(int $id=null)
    {
        if (!is_null($id)) {
            return Template::find($id);
        } else {
            return Template::all()->pluck("value")->random();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function edit(Template $template)
    {
        return view('web/template/edit', ['template' => $template, 'types' => Type::all(), 'title' => 'Template ändern']);
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
        $template->value = $request->value;
        $template->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function destroy(Template $template)
    {
        $template->delete();

        return redirect()->route('templates.index');
    }

    /**
     * Return generate View
     */
    public function generate(int $id)
    {
        $template = Template::find($id);

        if(is_null($template)){
            throw new Exception("No template found with id=".$id);
        }

        $templateString = $template['value'];
        $templateId = $template['id'];

        $result = $this->generateFromString($templateString);

        return view('web\template\generate', ['templateId' => $templateId, 'templateString' => $templateString, 'result' => $result, 'title' => 'DnD Random Message']);
    }

    /**
     * Return random generate View
     */
    public function generateRandom()
    {
        $id = Template::randomID();

        return $this->generate($id);
    }



    /**
     * Return random compile View
     */
    public function compileRandom()
    {
        $id = Template::randomID();

        return $this->compile($id);
    }

    /**
     * Return compile View
     */
    public function compile($id)
    {
        $templateString = Template::find($id)['value'];
        if(is_null($templateString)){
            throw new Exception("No template found with id=".$id);
        }

        return $this->generateFromString($templateString);
    }

    /////////////////////////////

    /**
     * generate Methods
     */

    private function generateFromString(string $template)
    {
        $arr = [];
        array_push($arr, $template);

        $count = 0;
        do {
            $isDirty = true;

            $template = $this->DefineAndReplaceVariables($template, $variableList, $isDirty);
            array_push($arr, $template);

            if (++$count > 1000) {
                throw new Exception("Possible recursion detected! Current Template: " . $template);
            }
        } while ($isDirty);


        // dd($arr);
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

            try {
                $item = DB::table('items')->join('types', 'items.type_id', '=', 'types.id')
                    ->where('types.name', '=', $tableName)
                    ->pluck('singular')
                    ->random();
            } catch (\InvalidArgumentException $th) {
                throw new Exception("No entries found for type: '" . $tableName . "'", null, $th);
            }

            $replace = $item;
        } else if ($this->isTernaryTable($command, $match)) {
            $tableName      = $match["tableName"];
            $number         = $match["number"];

            $numerus = $number != 1 ?  "plural" : "singular";

            try {
                $item = DB::table('items')->join('types', 'items.type_id', '=', 'types.id')
                    ->where('types.name', '=', $tableName)
                    ->pluck($numerus)
                    ->random();
            } catch (\InvalidArgumentException $th) {
                throw new Exception("No entries found for type: '" . $tableName . "'", null, $th);
            }

            $replace = $item;
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
        $regex = "/^\?(?<number>[0-9]+),'(?<singular>.+?)','(?<plural>.+?)'$/";

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
