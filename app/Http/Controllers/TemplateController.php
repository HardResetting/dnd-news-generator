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

        return redirect()->route('templates.index');
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
     * Return Compile View
     */
    public function generate(){
        $template = $this->compile();
        return view('web\template\generate', ['template' => $template, 'title' => 'DnD Random Message']);
    }

    /**
     * Compile the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function compile(int $id = null)
    {
        if (is_null($id)) {
            $id = Template::all()->random()["id"];
        }

        return $this->compileFromId($id);
    }


    /////////////////////////////

    /**
     * Compile Methods
     */

    private function compileFromId(int $id)
    {
        $arr = ["started from id=" . $id];

        //$template = "[@Rasse_1=[Rasse]] und [@var_1=[ran(0,2)]] [?[@var_1],Rasse] [?[@var_1],'fordert','fordern'] Auswanderung von [@var_3=[ran(100,200)]] [?[@var_3],Rasse] aus der Stadt [Stadt]!";

        // DB Magic
        $template = Template::all()->random(1)->toArray()[0]['value'];
        
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


        //dd($arr);
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
            $items = DB::table('items')->join('types', 'items.type_id', '=', 'types.id')
                                    ->select('items.singular')
                                    ->where('types.name', '=', $tableName)
                                    ->get()->random(1)->toArray();                                    
            if (count($items)>0){                
                $value = $items[0]->singular;
            } else {
                throw new Exception("Keinen Eintrag in Items für den Typ '" . $tableName . "' gefunden.");
            }                        
            $replace = $value;
        } else if ($this->isTernaryTable($command, $match)) {
            $tableName      = $match["tableName"];
            $number         = $match["number"];

            $numerus = $number != 1 ?  "plural" : "singular";
            // --> DB Magic with tableName
            $items = DB::table('items')->join('types', 'items.type_id', '=', 'types.id')
                                    ->select('items.'.$numerus)
                                    ->where('types.name', '=', $tableName)
                                    ->get()->random(1)->toArray();
            if (count($items)>0){                
                $value = $items[0]->$numerus;
            } else {
                throw new Exception("Keinen Eintrag in Items für den Typ '" . $tableName . "' gefunden.");
            }            
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
