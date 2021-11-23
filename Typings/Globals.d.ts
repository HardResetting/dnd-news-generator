// typings/interface/index.d.ts
export as namespace Types

export interface IPlainObject {
    key: string,
    value: string,
}

export class Item {
    singular: string;
    plural: string;
    type: string;
}