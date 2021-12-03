export default function (template: string): string {
  return "ok" + template;
}

// const generateFromString = function (template: string) {
//     var arr: string[] = [];
//     arr.push(template);

//     var count: number = 0;

//     do {
//         var isDirty: boolean = true;

//         template = DefineAndReplaceVariables(template, variableList, isDirty);
//         array_push($arr, $template);

//         if (++$count > 1000) {
//             throw new Exception("Possible recursion detected! Current Template: " . $template);
//         }
//     } while (isDirty);
// }

// function DefineAndReplaceVariables(template, variableList, isDirty) {

// }
