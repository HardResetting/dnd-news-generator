export default function (template: string): string {
    const tc = new TemplateCompiler(template); 
    return "ok" + tc.compileTemplate();
}


class TemplateCompiler {
    recursionCountLimit = 100;

    private template: string;
    private templateChanged = false;
    private workingTemplate: string;

    private result = "";
    private variableArray = new Array<Variable>();
    private dirty = true;

    public compileTemplate(): string {
        //let debugArray = new Array<string>();
        let recursionCount = 0;

        let parseResult = this.Parse(this.template);

        while (parseResult.dirty && recursionCount < this.recursionCountLimit) {
            parseResult = this.Parse(parseResult.result);

            recursionCount++;
        }

        return this.result = parseResult.result;
    }


    constructor(template: string) {
        this.template = template;
        this.workingTemplate = template;
    }


    private Parse(template: string): ParseResult {
        const commandsAndVars = this.ParseCommandsAndVars();

        console.log(commandsAndVars);
        
        if (commandsAndVars.length == -1) {
            this.dirty = false;
            return new ParseResult(template, false);
        }



        return new ParseResult(template, false);
    }

    private ParseCommandsAndVars(): RegExpMatchArray[] {
        /* eslint-disable-next-line */
        const regex = /\[(?<variableName>@(?:[a-z0-9_])+)\]|\[(?<command>.|[^\[]+?)\]/gi;
        return [...this.workingTemplate.matchAll(regex)];
    }
}

class ParseResult {
    result: string;
    dirty: boolean;

    constructor(result: string, dirty: boolean) {
        this.result = result;
        this.dirty = dirty
    }
}

class Variable {
    name: string;
    value: string;

    constructor(name: string, value: string) {
        this.name = name;
        this.value = value;
    }
}