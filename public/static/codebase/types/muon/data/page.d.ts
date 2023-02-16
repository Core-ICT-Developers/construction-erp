import { IValue, cellValue, parseContext } from "../types";
import { Store } from "../store";
export interface NumberHash<T> {
    [key: number]: T;
}
declare type MetaValues = {
    [key: string]: any;
};
export declare class DataPage {
    private _data;
    private _store;
    private _id;
    private _parseContext;
    private _meta;
    constructor(id: number, store: Store, ctx: parseContext);
    getMeta(): any;
    setMeta(obj: MetaValues): void;
    getID(): number;
    cellID(row: number, col: number): number;
    getSize(): [number, number];
    getContext(): parseContext;
    addRow(pos: number, count: number): void;
    removeRow(pos: number, count: number): void;
    addColumn(pos: number, count: number): void;
    removeColumn(pos: number, count: number): void;
    setCell(r: number, c: number, value: IValue): void;
    setValue(r: number, c: number, value?: cellValue, type?: number): void;
    getCell(r: number, c: number, force?: boolean): IValue;
    getValue(r: number, c: number, formula?: boolean): cellValue;
    getRange(r1: number, c1: number, r2: number, c2: number): cellValue[];
    getCellRange(r1: number, c1: number, r2: number, c2: number): IValue[];
    eachCell(cb: (row: number, col: number, value: IValue) => void): void;
    serialize(formula?: true): [number, number, IValue][];
    parse(data: [number, number, IValue][], process?: boolean): void;
}
export {};
