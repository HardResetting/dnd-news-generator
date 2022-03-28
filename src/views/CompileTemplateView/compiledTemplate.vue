<template>
  <BasicCard style="margin-top: 4rem">
    <template #title>
      <h2>Compiled Template</h2>
    </template>
    <template #title-side>Done in {{ timeTaken }}s</template>
    <template #body>{{ compiledTemplateText }}</template>
  </BasicCard>
</template>
<script setup lang="ts">
import BasicCard from "@/components/BasicCard.vue";
import { useStore } from "@/store";
import { watch, ref, defineProps, defineEmits } from "vue";
import { FirebaseTemplateItem } from "@/typings/Globals";

const state = useStore();
const compiledTemplateText = ref("Waiting for template");
const timeTaken = ref(0);

const emit = defineEmits(["done"]);

const props = defineProps({
  template: {
    default: null,
    type: String
  },
});
watch(() => props.template, async (template) => {
  runCompileScript(template);
});

async function runCompileScript(template: string) {
  try {
    const result = await compileTemplate(template);
    compiledTemplateText.value = result.errors.length
      ? result.errors.length + " Error(s) occoured during the process.."
      : result.result;
    timeTaken.value = result.performance;
  } finally {
    emit("done");
  }
}

///////////////////////////////////////////////////////////////////////////////

async function compileTemplate(
  template: string,
  operationCountLimit = 100
): Promise<ParseObject> {
  const startTime = new Date();

  let operationCount = 0;
  let parseObject = new ParseObject(template);

  do {
    parseObject = await Parse(parseObject);

    if (++operationCount > operationCountLimit) {
      parseObject.errors.push(new TooManyOperationsError());
      break;
    }
  } while (!parseObject.unTouched);

  const endTime = new Date();
  const timeDiff = endTime.getMilliseconds() - startTime.getMilliseconds(); //in ms

  parseObject.performance = timeDiff / 10;
  return parseObject;
}

async function Parse(parseObject: ParseObject): Promise<ParseObject> {
  const commandOrVariable = FindFirstCommandOrVariable(parseObject.result);
  if (commandOrVariable == null) {
    parseObject.unTouched = true;
  } else if (commandOrVariable.type == "variable") {
    parseObject = ReplaceVariable(parseObject, commandOrVariable);
  } else {
    parseObject = await RunCommandAndReplace(parseObject, commandOrVariable);
  }

  return parseObject;
}

async function RunCommandAndReplace(parseObject: ParseObject, match: Match) {
  const Assignment = /^(?<variableName>@(?:[a-zA-Z0-9_])+)=(?<value>.+)$/,
    Random = /^ran\((?<min>[0-9]+),(?<max>[0-9]+)\)$/,
    TableItem = /^(?<tableName>[a-zA-Z0-9]+)$/,
    TernaryTable = /^\?(?<number>[0-9]+),(?<tableName>[a-zA-Z0-9]+)$/,
    TernaryString = /^\?(?<number>[0-9]+),'(?<singular>.+?)','(?<plural>.+?)'$/;

  let command: RegExpMatchArray | null;
  let replacement: string;

  if ((command = Assignment.exec(match.innerMatch)) != null) {
    const variableName = command.groups!.variableName;
    const value = command.groups!.value;

    parseObject.variableArray.push(new Variable(variableName, value));
    replacement = value;
  } else if ((command = Random.exec(match.innerMatch)) != null) {
    const min = Math.ceil(parseInt(command.groups!.min));
    const max = Math.floor(parseInt(command.groups!.max));
    const randomNumber = Math.floor(Math.random() * (max - min + 1)) + min;

    replacement = randomNumber.toString();
  } else if ((command = TableItem.exec(match.innerMatch)) != null) {
    const tableName = command.groups!.tableName;

    const possibleItems = await getTemplateItems(tableName);

    if (possibleItems.length == 0) {
      parseObject.errors.push(new NochSuchElementError());
      replacement = "ERROR";
    } else {
      const randomElement =
        possibleItems[Math.floor(Math.random() * possibleItems.length)];

      replacement = randomElement.singular || "ERROR";
    }
  } else if ((command = TernaryTable.exec(match.innerMatch)) != null) {
    const tableName = command.groups!.tableName;
    const number = parseInt(command.groups!.number);

    const possibleItems = await getTemplateItems(tableName);

    if (possibleItems.length == 0) {
      parseObject.errors.push(new NochSuchElementError());
      replacement = "ERROR";
    } else {
      const randomElement =
        possibleItems[Math.floor(Math.random() * possibleItems.length)];

      replacement =
        number == 1 ? randomElement.singular : randomElement.plural || "ERROR";
    }
  } else if ((command = TernaryString.exec(match.innerMatch)) != null) {
    const singular = command.groups!.singular;
    const plural = command.groups!.plural;
    const number = parseInt(command.groups!.number);

    replacement = number == 1 ? singular : plural;
  } else {
    replacement = "ERROR";
  }

  parseObject.result = parseObject.result.replace(match.fullMatch, replacement);
  return parseObject;
}

function ReplaceVariable(parseObject: ParseObject, match: Match): ParseObject {
  const variable = parseObject.variableArray.find(
    (e) => e.name == match.innerMatch
  );

  parseObject.result = parseObject.result.replace(
    match.fullMatch,
    variable!.value
  );

  return parseObject;
}

function FindFirstCommandOrVariable(
  template: string
): CommandOrVariableMatch | null {
  /* eslint-disable-next-line */
  const regex = /\[(?<variableName>@(?:[a-zA-Z0-9_])+)\]|\[(?<command>.|[^\[]+?)\]/gi;
  const result = regex.exec(template);

  return result == null
    ? null
    : new CommandOrVariableMatch(
      result[0],
      result.groups!.command ?? result.groups!.variableName,
      result.groups!.variableName == undefined ? "command" : "variable"
    );
}

/************************
 Database Helper
 ************************/

async function getTemplateItems(
  type: string
): Promise<Array<FirebaseTemplateItem>> {
  return state.FirebaseTemplateItems.filter((e) => e.type == type);
}

/************************
Class declarations
************************/

class TooManyOperationsError extends Error {
  constructor(msg?: string) {
    super(msg);

    Object.setPrototypeOf(this, TooManyOperationsError.prototype);
  }
}

class NochSuchElementError extends Error {
  constructor(msg?: string) {
    super(msg);

    Object.setPrototypeOf(this, NochSuchElementError.prototype);
  }
}

////////////////////////////

class ParseObject {
  result: string;
  unTouched: boolean;
  variableArray: Array<Variable>;
  errors: Array<Error>;
  performance = 0;

  constructor(
    result: string,
    unTouched = false,
    variableArray = new Array<Variable>(),
    errors = Array<Error>()
  ) {
    this.result = result;
    this.unTouched = unTouched;
    this.variableArray = variableArray;
    this.errors = errors;
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

////////////////////////////

interface Match {
  fullMatch: string;
  innerMatch: string;
}

class CommandOrVariableMatch implements Match {
  type: "command" | "variable";
  fullMatch: string;
  innerMatch: string;

  constructor(
    fullMatch: string,
    innerMatch: string,
    type: "command" | "variable"
  ) {
    this.fullMatch = fullMatch;
    this.innerMatch = innerMatch;
    this.type = type;
  }
}
</script>